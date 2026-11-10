<section class="container account-layout">
    <?php $accountActive = 'addresses'; include dirname(__DIR__) . '/bagian/menu-akun.php'; ?>
    <div class="account-main">
        <div class="section-heading">
            <div>
                <h1>
                    Alamat Saya
                </h1>
                <p>
                    Kelola alamat pengiriman untuk memudahkan proses checkout.
                </p>
            </div>
            <button
                class="button button-primary"
                type="button"
                onclick="document.getElementById('address-form').classList.toggle('hidden')"
            >
                ＋ Tambah Alamat Baru
            </button>
        </div>
        <form
            id="address-form"
            class="account-form <?= $editAddress ? '' : 'hidden' ?>"
            method="post"
            action="<?= e(url('addresses')) ?>"
        >
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_address">
            <input type="hidden" name="_back" value="<?= e(url('addresses')) ?>">
            <input type="hidden" name="address_id" value="<?= (int)($editAddress['id']??0) ?>">
            <h2>
                <?= $editAddress ? 'Ubah Alamat' : 'Tambah Alamat' ?>
            </h2>
            <div class="form-grid-two">
                <label>
                    Label Alamat
                    <input
                        name="label"
                        placeholder="Rumah / Kantor"
                        required
                        value="<?= e($editAddress['label']??'') ?>"
                    >
                </label>
                <label>
                    Nama Penerima
                    <input
                        name="recipient_name"
                        required
                        value="<?= e($editAddress['recipient_name']??$user['name']) ?>"
                    >
                </label>
                <label>
                    Nomor WhatsApp
                    <input
                        name="phone"
                        required
                        value="<?= e($editAddress['phone']??($user['phone']??'')) ?>"
                    >
                </label>
                <label>
                    Provinsi
                    <input name="province" required value="<?= e($editAddress['province']??'') ?>">
                </label>
                <label>
                    Kota/Kabupaten
                    <input name="city" required value="<?= e($editAddress['city']??'') ?>">
                </label>
                <label>
                    Kecamatan
                    <input name="district" value="<?= e($editAddress['district']??'') ?>">
                </label>
                <label>
                    Kelurahan
                    <input name="village" value="<?= e($editAddress['village']??'') ?>">
                </label>
                <label>
                    Kode Pos
                    <input
                        name="postal_code"
                        required
                        value="<?= e($editAddress['postal_code']??'') ?>"
                    >
                </label>
            </div>
            <label>
                Detail Alamat
                <textarea name="address_detail" rows="3" required>
                    <?= e($editAddress['address_detail']??'') ?>
                    </textarea>
                </textarea>
            </label>
            <label class="check-label">
                <input
                    type="checkbox"
                    name="is_default"
                    <?= !empty($editAddress['is_default']) ? 'checked' : '' ?>
                >
                Jadikan alamat utama
            </label>
            <button class="button button-primary">
                Simpan Alamat
            </button>
            <a class="button button-outline" href="<?= e(url('addresses')) ?>">
                Batal
            </a>
        </form>
        <?php if($addresses): foreach($addresses as $address): ?>
            <article class="address-card">
                <div>
                    <span class="address-icon">
                    ⌖
                    </span>
                </div>
                <div class="address-copy">
                    <h3>
                        <?= e($address['recipient_name']) ?>
                        <small>
                            <?= e($address['label']) ?>
                        </small>
                    </h3>
                    <p>
                        <?= e($address['phone']) ?>
                        <br>
                        <?= e($address['address_detail']) ?>
                    ,
                        <?= e($address['city']) ?>
                    ,
                        <?= e($address['province']) ?>
                        <?= e($address['postal_code']) ?>
                    </p>
                    <?php if($address['is_default']): ?>
                        <span class="soft-pill">
                    Alamat Utama
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="address-actions">
                        <a
                            class="button button-outline"
                            href="<?= e(url('addresses',['edit'=>$address['id']])) ?>"
                        >
                    Ubah
                        </a>
                        <?php if(!$address['is_default']): ?>
                            <form method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="default_address">
                                <input type="hidden" name="_back" value="<?= e(url('addresses')) ?>">
                                <input
                                    type="hidden"
                                    name="address_id"
                                    value="<?= (int)$address['id'] ?>"
                                >
                                <button>
                        Jadikan Utama
                                </button>
                            </form>
                            <?php endif; ?>
                            <form method="post" onsubmit="return confirm('Hapus alamat ini?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_address">
                                <input type="hidden" name="_back" value="<?= e(url('addresses')) ?>">
                                <input
                                    type="hidden"
                                    name="address_id"
                                    value="<?= (int)$address['id'] ?>"
                                >
                                <button>
                        Hapus
                                </button>
                            </form>
                        </div>
                    </article>
                    <?php endforeach; else: ?>
                    <div class="empty-state">
                        <h2>
                Belum ada alamat tersimpan
                        </h2>
                        <p>
                Tambahkan alamat agar checkout lebih cepat.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
