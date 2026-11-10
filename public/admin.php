<?php

declare(strict_types=1);

require dirname(__DIR__) . '/sistem/mulai.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_admin_post();
}

// PETA HALAMAN ADMIN: alamat web (?page=...) -> nama file di tampilan/admin/halaman/
$fileHalamanAdmin = [
    'dashboard' => 'dasbor',
    'books' => 'kelola-buku',
    'categories' => 'kelola-kategori',
    'users' => 'kelola-pengguna',
    'orders' => 'daftar-pesanan',
    'order' => 'detail-pesanan',
    'payments' => 'pembayaran',
    'messages' => 'pesan-masuk',
    'reports' => 'laporan',
    'settings' => 'pengaturan-toko',
];

$admin = require_admin();
$page = (string) ($_GET['page'] ?? 'dashboard');

$allowedPages = [
    'dashboard',
    'books',
    'categories',
    'users',
    'orders',
    'payments',
    'order',
    'messages',
    'reports',
    'settings',
];

if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

$pdo = db();
$metrics = [];

if (in_array($page, ['dashboard', 'reports'], true)) {
    $metrics['books'] = (int) $pdo->query(
        'SELECT COUNT(*) FROM books'
    )->fetchColumn();

    $metrics['categories'] = (int) $pdo->query(
        'SELECT COUNT(*) FROM categories'
    )->fetchColumn();

    $metrics['users'] = (int) $pdo->query(
        'SELECT COUNT(*) FROM users WHERE role = "user"'
    )->fetchColumn();

    $metrics['orders'] = (int) $pdo->query(
        'SELECT COUNT(*) FROM orders'
    )->fetchColumn();

    $metrics['revenue'] = (float) $pdo->query(
        'SELECT COALESCE(SUM(total), 0)
         FROM orders
         WHERE payment_status = "paid"'
    )->fetchColumn();

    $metrics['pending'] = (int) $pdo->query(
        'SELECT COUNT(*)
         FROM orders
         WHERE order_status IN ("pending", "processing")'
    )->fetchColumn();

    $metrics['paid_orders'] = (int) $pdo->query(
        'SELECT COUNT(*) FROM orders WHERE payment_status = "paid"'
    )->fetchColumn();

    $metrics['average_order'] = $metrics['paid_orders'] > 0
        ? $metrics['revenue'] / $metrics['paid_orders']
        : 0;
}

if ($page === 'dashboard') {
    $latestOrders = $pdo->query(
        'SELECT o.*, u.name AS customer_name
         FROM orders o
         INNER JOIN users u ON u.id = o.user_id
         ORDER BY o.created_at DESC
         LIMIT 5'
    )->fetchAll();

    $monthly = $pdo->query(
        'SELECT DATE_FORMAT(created_at, "%b") AS month_name,
                SUM(
                    CASE
                        WHEN payment_status = "paid" THEN total
                        ELSE 0
                    END
                ) AS revenue
         FROM orders
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
         GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, "%b")
         ORDER BY YEAR(created_at), MONTH(created_at)'
    )->fetchAll();

    $orderStatusSummary = $pdo->query(
        'SELECT order_status, COUNT(*) AS total
         FROM orders
         GROUP BY order_status'
    )->fetchAll();

    // Seven-day order activity for the dashboard chart (real database data).
    $sales7Rows = $pdo->query(
        'SELECT DATE(created_at) AS sales_date, COUNT(*) AS total
         FROM orders
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
           AND created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
         GROUP BY DATE(created_at)
         ORDER BY sales_date'
    )->fetchAll();
    $sales7Map = [];
    foreach ($sales7Rows as $salesRow) {
        $sales7Map[$salesRow['sales_date']] = (int) $salesRow['total'];
    }
    $sales7Labels = [];
    $sales7Values = [];
    for ($dayOffset = 6; $dayOffset >= 0; $dayOffset--) {
        $salesDate = new DateTimeImmutable('-' . $dayOffset . ' days');
        $salesKey = $salesDate->format('Y-m-d');
        $sales7Labels[] = $salesDate->format('j M');
        $sales7Values[] = $sales7Map[$salesKey] ?? 0;
    }

    // Best-selling titles are ranked by quantities from paid orders only.
    $topBooks = $pdo->query(
        'SELECT b.id, b.title, b.author, b.cover,
                SUM(oi.quantity) AS units_sold
         FROM order_items oi
         INNER JOIN orders o ON o.id = oi.order_id
         LEFT JOIN books b ON b.id = oi.book_id
         WHERE o.payment_status = "paid"
         GROUP BY b.id, b.title, b.author, b.cover, oi.title, oi.author
         ORDER BY units_sold DESC, oi.title ASC
         LIMIT 3'
    )->fetchAll();
}

if ($page === 'books') {
    $search = trim((string) ($_GET['q'] ?? ''));
    $statusFilter = trim((string) ($_GET['status'] ?? ''));
    $categoryFilter = (int) ($_GET['category'] ?? 0);
    $editId = (int) ($_GET['edit'] ?? 0);
    $showBookForm = $editId > 0 || (($_GET['form'] ?? '') === '1');
    $editBook = null;

    if ($editId > 0) {
        $edit = $pdo->prepare('SELECT * FROM books WHERE id = ?');
        $edit->execute([$editId]);
        $editBook = $edit->fetch() ?: null;
    }

    $categories = $pdo->query(
        "SELECT * FROM categories
         ORDER BY FIELD(
            slug,
            'fiksi', 'non-fiksi', 'self-help', 'bisnis-ekonomi',
            'pendidikan', 'anak-anak', 'remaja', 'horor',
            'romance', 'klasik', 'sejarah', 'thriller'
         ), name"
    )->fetchAll();

    $bookMetrics = [
        'total' => (int) $pdo->query(
            'SELECT COUNT(*) FROM books'
        )->fetchColumn(),
        'categories' => (int) $pdo->query(
            'SELECT COUNT(*) FROM categories'
        )->fetchColumn(),
        'low_stock' => (int) $pdo->query(
            'SELECT COUNT(*) FROM books WHERE stock <= 5 AND status = "active"'
        )->fetchColumn(),
    ];

    $sql = '
        SELECT
            b.*,
            c.name AS category_name
        FROM books b
        LEFT JOIN categories c ON c.id = b.category_id
        WHERE 1 = 1
    ';
    $params = [];

    if ($search !== '') {
        $sql .= '
            AND (
                b.title LIKE ?
                OR b.author LIKE ?
                OR b.sku LIKE ?
            )
        ';
        $term = '%' . $search . '%';
        array_push($params, $term, $term, $term);
    }

    if ($statusFilter !== '') {
        $sql .= ' AND b.status = ?';
        $params[] = $statusFilter;
    }

    if ($categoryFilter > 0) {
        $sql .= ' AND b.category_id = ?';
        $params[] = $categoryFilter;
    }

    $sql .= ' ORDER BY b.id ASC';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $books = $statement->fetchAll();
}

if ($page === 'categories') {
    $editId = (int) ($_GET['edit'] ?? 0);
    $categorySearch = trim((string) ($_GET['q'] ?? ''));
    $editCategory = null;

    if ($editId > 0) {
        $edit = $pdo->prepare(
            'SELECT * FROM categories WHERE id = ?'
        );
        $edit->execute([$editId]);
        $editCategory = $edit->fetch() ?: null;
    }

    $categorySql = '
        SELECT
            c.*,
            COUNT(b.id) AS book_count
        FROM categories c
        LEFT JOIN books b ON b.category_id = c.id
    ';
    $categoryParams = [];

    if ($categorySearch !== '') {
        $categorySql .= ' WHERE c.name LIKE ? OR c.description LIKE ?';
        $term = '%' . $categorySearch . '%';
        $categoryParams = [$term, $term];
    }

    $categorySql .= "
        GROUP BY c.id
        ORDER BY FIELD(
            c.slug,
            'fiksi', 'non-fiksi', 'self-help', 'bisnis-ekonomi',
            'pendidikan', 'anak-anak', 'remaja', 'horor',
            'romance', 'klasik', 'sejarah', 'thriller'
        ), c.name
    ";

    $categoryQuery = $pdo->prepare($categorySql);
    $categoryQuery->execute($categoryParams);
    $categories = $categoryQuery->fetchAll();
}

if ($page === 'users') {
    $userSearch = trim((string) ($_GET['q'] ?? ''));
    $userStatusFilter = trim((string) ($_GET['status'] ?? ''));

    $userStats = [
        'total' => (int) $pdo->query(
            'SELECT COUNT(*) FROM users WHERE role = "user"'
        )->fetchColumn(),
        'active' => (int) $pdo->query(
            'SELECT COUNT(*) FROM users WHERE role = "user" AND status = "active"'
        )->fetchColumn(),
        'inactive' => (int) $pdo->query(
            'SELECT COUNT(*) FROM users WHERE role = "user" AND status = "inactive"'
        )->fetchColumn(),
        'new' => (int) $pdo->query(
            'SELECT COUNT(*)
             FROM users
             WHERE role = "user"
               AND created_at >= DATE_FORMAT(CURDATE(), "%Y-%m-01")'
        )->fetchColumn(),
    ];

    $sql = '
        SELECT
            u.*,
            COUNT(o.id) AS order_count
        FROM users u
        LEFT JOIN orders o ON o.user_id = u.id
        WHERE u.role = "user"
    ';
    $params = [];

    if ($userSearch !== '') {
        $sql .= ' AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
        $term = '%' . $userSearch . '%';
        array_push($params, $term, $term, $term);
    }

    if ($userStatusFilter !== '') {
        $sql .= ' AND u.status = ?';
        $params[] = $userStatusFilter;
    }

    $sql .= '
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $users = $statement->fetchAll();
}

if ($page === 'payments') {
    $payments = $pdo->query('SELECT p.*, o.order_code, o.total, o.payment_method, o.payment_status, o.created_at, u.name AS customer_name, u.email FROM payments p JOIN orders o ON o.id=p.order_id JOIN users u ON u.id=o.user_id ORDER BY p.created_at DESC')->fetchAll();
}

if ($page === 'orders') {
    $statusFilter = trim((string) ($_GET['status'] ?? ''));
    $orderSearch = trim((string) ($_GET['q'] ?? ''));

    $orderStats = [
        'total' => (int) $pdo->query(
            'SELECT COUNT(*) FROM orders'
        )->fetchColumn(),
        'completed' => (int) $pdo->query(
            'SELECT COUNT(*) FROM orders WHERE order_status = "delivered"'
        )->fetchColumn(),
        'processing' => (int) $pdo->query(
            'SELECT COUNT(*) FROM orders
             WHERE order_status IN ("pending", "processing", "shipped")'
        )->fetchColumn(),
        'cancelled' => (int) $pdo->query(
            'SELECT COUNT(*) FROM orders WHERE order_status = "cancelled"'
        )->fetchColumn(),
    ];

    $sql = '
        SELECT
            o.*,
            u.name AS customer_name
        FROM orders o
        INNER JOIN users u ON u.id = o.user_id
        WHERE 1 = 1
    ';
    $params = [];

    if ($statusFilter !== '') {
        $sql .= ' AND o.order_status = ?';
        $params[] = $statusFilter;
    }

    if ($orderSearch !== '') {
        $sql .= ' AND (
            o.order_code LIKE ?
            OR u.name LIKE ?
            OR o.phone LIKE ?
        )';
        $term = '%' . $orderSearch . '%';
        array_push($params, $term, $term, $term);
    }

    $sql .= ' ORDER BY o.created_at DESC';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $orders = $statement->fetchAll();

    $previewId = (int) ($_GET['id'] ?? ($orders[0]['id'] ?? 0));
    $orderPreview = null;
    $orderPreviewItems = [];
    $orderPreviewPayment = null;

    if ($previewId > 0) {
        $previewQuery = $pdo->prepare(
            'SELECT
                o.*,
                u.name AS customer_name,
                u.email AS customer_email
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             WHERE o.id = ?
             LIMIT 1'
        );
        $previewQuery->execute([$previewId]);
        $orderPreview = $previewQuery->fetch() ?: null;

        if ($orderPreview) {
            $orderPreviewItems = order_items((int) $orderPreview['id']);
            $orderPreviewPayment = get_payment_for_order(
                (int) $orderPreview['id']
            );
        }
    }
}

if ($page === 'order') {
    $orderId = (int) ($_GET['id'] ?? 0);
    $statement = $pdo->prepare(
        'SELECT
            o.*,
            u.name AS customer_name,
            u.email AS customer_email
         FROM orders o
         INNER JOIN users u ON u.id = o.user_id
         WHERE o.id = ?
         LIMIT 1'
    );
    $statement->execute([$orderId]);
    $order = $statement->fetch();

    if ($order) {
        $items = order_items((int) $order['id']);
        $payment = get_payment_for_order((int) $order['id']);
    }
}

if ($page === 'messages') {
    $messages = $pdo->query(
        'SELECT * FROM contact_messages ORDER BY created_at DESC'
    )->fetchAll();
}

if ($page === 'reports') {
    $byCategory = $pdo->query(
        'SELECT
            COALESCE(c.name, "Tanpa Kategori") AS name,
            SUM(oi.subtotal) AS revenue
         FROM order_items oi
         INNER JOIN orders o ON o.id = oi.order_id
         LEFT JOIN books b ON b.id = oi.book_id
         LEFT JOIN categories c ON c.id = b.category_id
         WHERE o.payment_status = "paid"
         GROUP BY c.id, c.name
         ORDER BY revenue DESC'
    )->fetchAll();

    $monthly = $pdo->query(
        'SELECT
            DATE_FORMAT(created_at, "%b") AS month_name,
            SUM(
                CASE
                    WHEN payment_status = "paid" THEN total
                    ELSE 0
                END
            ) AS revenue
         FROM orders
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
         GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, "%b")
         ORDER BY YEAR(created_at), MONTH(created_at)'
    )->fetchAll();

    $paymentMethods = $pdo->query(
        'SELECT
            payment_method,
            COUNT(*) AS total_orders,
            SUM(total) AS revenue
         FROM orders
         WHERE payment_status = "paid"
         GROUP BY payment_method
         ORDER BY revenue DESC'
    )->fetchAll();

    $recentTransactions = $pdo->query(
        'SELECT
            o.*,
            u.name AS customer_name
         FROM orders o
         INNER JOIN users u ON u.id = o.user_id
         ORDER BY o.created_at DESC
         LIMIT 6'
    )->fetchAll();
}

if ($page === 'settings') {
    $settings = [
        'store_name' => app_setting('store_name', 'Lumière Books'),
        'store_email' => app_setting('store_email', 'halo@lumierebooks.id'),
        'store_phone' => app_setting('store_phone', '0812 0000 0000'),
        'store_address' => app_setting('store_address', 'Indonesia'),
        'store_description' => app_setting(
            'store_description',
            'Setiap Buku, Sejuta Cahaya.'
        ),
        'store_facebook' => app_setting('store_facebook', ''),
        'store_instagram' => app_setting('store_instagram', ''),
        'store_tiktok' => app_setting('store_tiktok', ''),
        'store_x' => app_setting('store_x', ''),
        'store_whatsapp' => app_setting('store_whatsapp', ''),
        'books_per_page' => app_setting('books_per_page', '10'),
        'currency' => app_setting('currency', 'IDR'),
        'date_format' => app_setting('date_format', 'd M Y'),
        'language' => app_setting('language', 'id'),
        'maintenance_mode' => app_setting('maintenance_mode', '0'),
        'hours_monday' => app_setting('hours_monday', '08:00 - 21:00'),
        'hours_tuesday' => app_setting('hours_tuesday', '08:00 - 21:00'),
        'hours_wednesday' => app_setting('hours_wednesday', '08:00 - 21:00'),
        'hours_thursday' => app_setting('hours_thursday', '08:00 - 21:00'),
        'hours_friday' => app_setting('hours_friday', '08:00 - 21:00'),
        'hours_saturday' => app_setting('hours_saturday', '08:00 - 22:00'),
        'hours_sunday' => app_setting('hours_sunday', '09:00 - 21:00'),
    ];
}

include __DIR__ . '/tampilan/admin/bagian/kepala-halaman.php';

$adminView = __DIR__ . '/tampilan/admin/halaman/' . ($fileHalamanAdmin[$page] ?? $page) . '.php';

if (is_file($adminView)) {
    include $adminView;
}

include __DIR__ . '/tampilan/admin/bagian/kaki-halaman.php';
