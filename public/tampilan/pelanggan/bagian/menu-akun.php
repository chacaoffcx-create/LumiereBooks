<?php
$accountActive = $accountActive ?? ($page ?? 'profile');

$accountMenu = [
    ['page' => 'profile',
         'label' => 'Profil Saya',
         'icon' => 'user'],
    
    ['page' => 'my-orders',
         'label' => 'Pesanan Saya',
         'icon' => 'box'],
    
    ['page' => 'favorites',
         'label' => 'Buku Favorit',
         'icon' => 'heart'],
    
    ['page' => 'addresses',
         'label' => 'Alamat Saya',
         'icon' => 'map-pin'],
    
    ['page' => 'notifications',
         'label' => 'Notifikasi',
         'icon' => 'bell'],
    
    ['page' => 'help',
         'label' => 'Pusat Bantuan',
         'icon' => 'help-circle'],
    
];

?>
<aside class="account-sidebar<?= $accountActive === 'profile' ? ' profile-sidebar' : '' ?>">
    <h2>
        Akun Saya
    </h2>
    <nav class="account-sidebar-nav" aria-label="Menu akun">
        <?php foreach ($accountMenu as $item): ?>
            <a
                href="<?= e(url($item['page'])) ?>"
                class="<?= $accountActive === $item['page'] ? 'active' : '' ?>"
                <?= $accountActive === $item['page'] ? 'aria-current="page"' : '' ?>
            >
                <?= ui_icon($item['icon']) ?>
                <span>
                    <?= e($item['label']) ?>
                </span>
            </a>
            <?php endforeach; ?>
        </nav>
        <form method="post" action="<?= e(url('home')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="logout">
            <button type="submit">
                <?= ui_icon('log-out') ?>
                <span>
                Keluar
                </span>
            </button>
        </form>
    </aside>
