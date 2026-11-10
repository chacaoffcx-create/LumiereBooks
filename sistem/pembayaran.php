<?php

declare(strict_types=1);

function get_payment_for_order(int $orderId): ?array
{
    $statement = db()->prepare(
        'SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1'
    );
    $statement->execute([$orderId]);

    return $statement->fetch() ?: null;
}

function release_order_stock(PDO $pdo, int $orderId): void
{
    $statement = $pdo->prepare(
        'SELECT stock_released FROM orders WHERE id = ? FOR UPDATE'
    );
    $statement->execute([$orderId]);
    $released = $statement->fetchColumn();

    if ($released === false || (int) $released === 1) {
        return;
    }

    $items = $pdo->prepare(
        'SELECT book_id, quantity FROM order_items WHERE order_id = ?'
    );
    $items->execute([$orderId]);

    foreach ($items->fetchAll() as $item) {
        if ($item['book_id']) {
            $restore = $pdo->prepare(
                'UPDATE books SET stock = stock + ? WHERE id = ?'
            );
            $restore->execute([$item['quantity'], $item['book_id']]);
        }
    }

    $pdo->prepare(
        'UPDATE orders SET stock_released = 1 WHERE id = ?'
    )->execute([$orderId]);
}
