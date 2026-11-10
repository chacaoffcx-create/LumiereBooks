<?php
$defaultAddress = $savedAddresses[0] ?? null;

$shippingOptions = [
 ['value'=>'JNE Reguler',
        'label'=>'JNE Reguler',
        'estimate'=>'Estimasi tiba 2–3 hari',
        'cost'=>15000,
        'logo'=>'jne'],
    
 ['value'=>'JNE Express',
        'label'=>'JNE Express',
        'estimate'=>'Estimasi tiba 1–2 hari',
        'cost'=>22000,
        'logo'=>'jne-express'],
    
 ['value'=>'SiCepat REG',
        'label'=>'SiCepat',
        'estimate'=>'Estimasi tiba 2–4 hari',
        'cost'=>10000,
        'logo'=>'sicepat'],
    
 ['value'=>'Pos Indonesia',
        'label'=>'Pos Indonesia',
        'estimate'=>'Estimasi tiba 3–5 hari',
        'cost'=>13000,
        'logo'=>'pos'],
    
 ['value'=>'J&T Express',
        'label'=>'J&T Express',
        'estimate'=>'Estimasi tiba 2–3 hari',
        'cost'=>12000,
        'logo'=>'jnt'],
    
];

$selectedCourier='J&T Express';
 $selectedShipping=12000;

?>
<section class="container page-section checkout-page">
    <nav class="checkout-breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(url('home')) ?>">
            <?= ui_icon('home') ?>
            Beranda
        </a>
        <span>
            ›
        </span>
        <a href="<?= e(url('cart')) ?>">
            Keranjang Saya
        </a>
        <span>
            ›
        </span>
        <b>
            Checkout
        </b>
    </nav>
    <div class="checkout-heading">
        <h1>
            Checkout
        </h1>
        <p>
            Lengkapi data pemesananmu dan pilih metode pembayaran.
        </p>
    </div>
    <?php if (!$cartItems): ?>
        <div class="empty-state">
            <h2>
            Keranjang masih kosong
            </h2>
            <a class="button button-primary" href="<?= e(url('books')) ?>">
            Belanja Sekarang
            </a>
        </div>
        <?php else: ?>
        <form
            class="checkout-layout checkout-layout-redesign"
            action="<?= e(url('checkout')) ?>"
            method="post"
        >
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="checkout">
            <input type="hidden" name="_back" value="<?= e(url('checkout')) ?>">
            <div class="checkout-main checkout-main-redesign">
                <section class="checkout-panel checkout-address-panel">
                    <h2 class="checkout-section-title">
                        <span class="checkout-step">
                        1
                        </span>
                        <span class="checkout-heading-icon">
                            <?= ui_icon('map-pin') ?>
                        </span>
                    Alamat Pengiriman
                    </h2>
                    <?php if ($defaultAddress): ?>
                        <div class="checkout-address-preview" id="checkout-address-preview">
                            <span class="checkout-address-check">
                                <?= ui_icon('circle-check') ?>
                            </span>
                            <div class="checkout-address-copy">
                                <div class="checkout-address-name">
                                <b id="preview-recipient">
                                <?= e($defaultAddress['recipient_name']) ?>
                                </b>
                                <span id="preview-phone">
                                <?= e($defaultAddress['phone']) ?>
                                </span>
                                </div>
                                <p id="preview-address">
                                <?= e($defaultAddress['address_detail']) ?>
                            ,
                                <?= e($defaultAddress['city']) ?>
                            ,
                                <?= e($defaultAddress['province']) ?>
                                <?= e($defaultAddress['postal_code']) ?>
                                </p>
                                <button
                                    type="button"
                                    class="checkout-soft-button"
                                    id="edit-address-toggle"
                                >
                                <?= ui_icon('edit') ?>
                            Ubah Alamat
                                </button>
                            </div>
                        </div>
                        <div class="checkout-address-switch">
                            <button type="button" id="other-address-toggle">
                                <?= ui_icon('plus') ?>
                        Gunakan alamat lain
                            </button>
                            <div class="checkout-saved-address-picker" id="saved-address-picker" hidden>
                                <label for="saved-address-select">
                            Pilih alamat tersimpan
                                </label>
                                <select id="saved-address-select">
                                <?php foreach ($savedAddresses as $i=>$savedAddress): ?>
                                <option value="<?= $i ?>" <?= $i===0?'selected':'' ?>>
                                <?= e($savedAddress['label']) ?>
                                —
                                <?= e($savedAddress['city']) ?>
                                <?= $savedAddress['is_default']?' (Utama)':'' ?>
                                </option>
                                <?php endforeach; ?>
                                <option value="manual">
                                Gunakan alamat baru
                                </option>
                                </select>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="checkout-address-empty">
                                <span>
                                <?= ui_icon('map-pin') ?>
                                </span>
                                <div>
                                <b>
                            Belum ada alamat tersimpan
                                </b>
                                <small>
                            Lengkapi alamat pengiriman di bawah ini.
                                </small>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div
                                class="checkout-address-editor"
                                id="checkout-address-editor"
                                <?= $defaultAddress?'hidden':'' ?>
                            >
                                <div class="checkout-form-grid">
                                <label>
                            Nama Penerima
                                <input
                                    type="text"
                                    name="recipient_name"
                                    required
                                    value="<?= e($defaultAddress['recipient_name']??$user['name']) ?>"
                                >
                                </label>
                                <label>
                            Nomor WhatsApp
                                <input
                                    type="tel"
                                    name="phone"
                                    required
                                    value="<?= e($defaultAddress['phone']??($user['phone']??'')) ?>"
                                >
                                </label>
                                </div>
                                <label>
                        Alamat Lengkap
                                <textarea
                                    name="shipping_address"
                                    rows="2"
                                    required
                                    placeholder="Nama jalan, nomor rumah, RT/RW, patokan..."
                                >
                                <?= e($defaultAddress['address_detail']??'') ?>
                            </textarea>
                                </label>
                                <div class="checkout-form-grid checkout-form-grid-three">
                                <label>
                                Kota/Kabupaten
                                <input
                                    type="text"
                                    name="city"
                                    required
                                    placeholder="Contoh: Jakarta Selatan"
                                    value="<?= e($defaultAddress['city']??'') ?>"
                                >
                                </label>
                                <label>
                                Provinsi
                                <input
                                    type="text"
                                    name="province"
                                    required
                                    placeholder="Contoh: DKI Jakarta"
                                    value="<?= e($defaultAddress['province']??'') ?>"
                                >
                                </label>
                                <label>
                                Kode Pos
                                <input
                                    type="text"
                                    name="postal_code"
                                    required
                                    inputmode="numeric"
                                    placeholder="Kode pos"
                                    value="<?= e($defaultAddress['postal_code']??'') ?>"
                                >
                                </label>
                                </div>
                                </div>
                            </section>
                            <section class="checkout-panel checkout-shipping-panel">
                                <h2 class="checkout-section-title">
                                <span class="checkout-step">
                            2
                                </span>
                                <span class="checkout-heading-icon">
                                <?= ui_icon('truck') ?>
                                </span>
                        Metode Pengiriman
                                </h2>
                                <div class="checkout-shipping-list">
                                <?php foreach($shippingOptions as $option): ?>
                                <label
                                    class="checkout-shipping-option <?= $option['value']===$selectedCourier?'is-selected':'' ?>"
                                >
                                <input
                                    type="radio"
                                    name="courier"
                                    value="<?= e($option['value']) ?>"
                                    data-cost="<?= (int)$option['cost'] ?>"
                                    <?= $option['value']===$selectedCourier?'checked':'' ?>
                                >
                                <span class="checkout-radio">
                                </span>
                                <span
                                    class="courier-logo courier-logo-<?= e($option['logo']) ?>"
                                    aria-label="<?= e($option['label']) ?>"
                                >
                                <?php if(str_starts_with($option['logo'],'jne')): ?>
                                <svg viewBox="0 0 130 42" role="img" aria-hidden="true">
                                <path d="M8 8h37L32 34H5z" fill="#d52c32"/>
                                <path d="M20 8h14L18 34H5z" fill="#174b91"/>
                                <text
                                    x="43"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="27"
                                    font-weight="900"
                                    font-style="italic"
                                    fill="#174b91"
                                >
                                JNE
                                </text>
                                <text
                                    x="44"
                                    y="37"
                                    font-family="Arial,sans-serif"
                                    font-size="7"
                                    font-weight="700"
                                    fill="#d52c32"
                                >
                                <?= $option['logo']==='jne-express'?'EXPRESS':'REGULER' ?>
                                </text>
                                </svg>
                                <?php elseif($option['logo']==='sicepat'): ?>
                                <svg viewBox="0 0 142 42" role="img" aria-hidden="true">
                                <path d="M5 9h31l-8 24H1z" fill="#d82b32"/>
                                <path d="M14 14l12 4-8 11z" fill="#fff"/>
                                <text
                                    x="39"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="21"
                                    font-weight="900"
                                    font-style="italic"
                                    fill="#c9252c"
                                >
                                SiCepat
                                </text>
                                </svg>
                                <?php elseif($option['logo']==='pos'): ?>
                                <svg viewBox="0 0 142 42" role="img" aria-hidden="true">
                                <rect
                                    x="5"
                                    y="9"
                                    width="26"
                                    height="24"
                                    rx="3"
                                    fill="#f18a22"
                                 />
                                <path d="M11 15h14M11 21h14M11 27h9" stroke="#fff" stroke-width="2.5"/>
                                <text
                                    x="36"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="21"
                                    font-weight="800"
                                    fill="#d8791c"
                                >
                                POS
                                </text>
                                <text
                                    x="38"
                                    y="36"
                                    font-family="Arial,sans-serif"
                                    font-size="7"
                                    font-weight="700"
                                    fill="#9b4b16"
                                >
                                INDONESIA
                                </text>
                                </svg>
                                <?php else: ?>
                                <svg viewBox="0 0 142 42" role="img" aria-hidden="true">
                                <path d="M5 9h28l-8 24H1z" fill="#d92d32"/>
                                <path d="M13 13l12 6-10 11z" fill="#fff"/>
                                <text
                                    x="37"
                                    y="27"
                                    font-family="Arial,sans-serif"
                                    font-size="24"
                                    font-weight="900"
                                    fill="#d52b32"
                                >
                                J&amp;T
                                </text>
                                <text
                                    x="83"
                                    y="26"
                                    font-family="Arial,sans-serif"
                                    font-size="9"
                                    font-weight="700"
                                    fill="#d52b32"
                                >
                                EXPRESS
                                </text>
                                </svg>
                                <?php endif; ?>
                                </span>
                                <span class="checkout-shipping-copy">
                                <b>
                                <?= e($option['label']) ?>
                                </b>
                                <small>
                                <?= e($option['estimate']) ?>
                                </small>
                                </span>
                                <strong>
                                <?= money($option['cost']) ?>
                                </strong>
                                </label>
                                <?php endforeach; ?>
                                </div>
                                </section>
                                <section class="checkout-panel checkout-payment-panel">
                                <h2 class="checkout-section-title">
                                <span class="checkout-step">
                            3
                                </span>
                                <span class="checkout-heading-icon">
                                <?= ui_icon('wallet') ?>
                                </span>
                        Metode Pembayaran
                                </h2>
                                <label class="checkout-payment-option is-selected">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="bank_transfer"
                                    checked
                                >
                                <span class="checkout-radio">
                                </span>
                                <span class="checkout-payment-icon">
                                <svg viewBox="0 0 48 48" aria-hidden="true">
                                <path
                                    d="M5 17L24 6l19 11H5zM9 20h30M11 21v15m9-15v15m8-15v15m9-15v15M6 39h36"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.6"
                                    stroke-linejoin="round"
                                    stroke-linecap="round"
                                 />
                                </svg>
                                </span>
                                <span class="checkout-payment-copy">
                                <b>
                                Transfer Bank
                                </b>
                                <small>
                                Verifikasi pembayaran otomatis
                                </small>
                                </span>
                                <span class="checkout-payment-chevron">
                            ⌄
                                </span>
                                </label>
                                <div class="checkout-payment-detail" id="bank-select-wrap">
                                <label for="bank-name">
                            Pilih Bank
                                </label>
                                <select name="bank_name" id="bank-name">
                                <option value="BCA">
                                BCA
                                </option>
                                <option value="BRI">
                                BRI
                                </option>
                                </select>
                                <div class="checkout-bank-markers">
                                <span class="bank-marker bank-bca">
                                BCA
                                </span>
                                <span class="bank-marker bank-bri">
                                BRI
                                </span>
                                </div>
                                </div>
                                <label class="checkout-payment-option">
                                <input type="radio" name="payment_method" value="qris">
                                <span class="checkout-radio">
                                </span>
                                <span class="checkout-payment-icon">
                                <svg viewBox="0 0 48 48" aria-hidden="true">
                                <rect
                                    x="6"
                                    y="7"
                                    width="36"
                                    height="34"
                                    rx="5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.6"
                                 />
                                <path
                                    d="M13 14h8v8h-8zM27 14h8v8h-8zM13 27h8v8h-8zM27 27h3v3h-3zM33 27h3v8h-3zM27 34h3"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                    stroke-linejoin="round"
                                 />
                                </svg>
                                </span>
                                <span class="checkout-payment-copy">
                                <b>
                                E-Wallet / QRIS
                                </b>
                                <small>
                                DANA, OVO, dan GoPay
                                </small>
                                </span>
                                <span class="checkout-payment-chevron">
                            ⌄
                                </span>
                                </label>
                                <div class="checkout-payment-detail" hidden>
                                <p class="checkout-detail-label">
                            Pilih dompet digital
                                </p>
                                <div class="checkout-wallet-brands">
                                <span class="wallet-brand wallet-dana">
                                DANA
                                </span>
                                <span class="wallet-brand wallet-ovo">
                                OVO
                                </span>
                                <span class="wallet-brand wallet-gopay">
                                GoPay
                                </span>
                                </div>
                                <small>
                            Pilih metode pembayaran digital yang tersedia.
                                </small>
                                </div>
                                <label class="checkout-payment-option">
                                <input type="radio" name="payment_method" value="cod">
                                <span class="checkout-radio">
                                </span>
                                <span class="checkout-payment-icon">
                                <svg viewBox="0 0 48 48" aria-hidden="true">
                                <rect
                                    x="5"
                                    y="12"
                                    width="38"
                                    height="25"
                                    rx="3"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.6"
                                 />
                                <path
                                    d="M5 19h38M12 29h9m9-4a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.4"
                                    stroke-linecap="round"
                                 />
                                </svg>
                                </span>
                                <span class="checkout-payment-copy">
                                <b>
                                COD (Bayar di Tempat)
                                </b>
                                <small>
                                Bayar langsung kepada kurir saat pesanan diterima
                                </small>
                                </span>
                                <span class="checkout-payment-chevron">
                            ⌄
                                </span>
                                </label>
                                <div class="checkout-payment-detail" hidden>
                                <p>
                            Siapkan pembayaran tunai sesuai total pesanan saat kurir mengantarkan buku.
                                </p>
                                </div>
                                </section>
                                </div>
                                <aside class="checkout-panel checkout-summary-redesign">
                                <h2 class="checkout-summary-title">
                                <?= ui_icon('cart') ?>
                    Ringkasan Pesanan
                                </h2>
                                <div class="checkout-summary-items">
                                <?php foreach($cartItems as $item): ?>
                                <div
                                    class="checkout-summary-item"
                                    data-unit-price="<?= (float)$item['price'] ?>"
                                    data-stock="<?= (int)$item['stock'] ?>"
                                >
                                <img
                                    src="<?= e(cover_url($item['cover'])) ?>"
                                    alt="Sampul <?= e($item['title']) ?>"
                                >
                                <div class="checkout-summary-book">
                                <b>
                                <?= e($item['title']) ?>
                                </b>
                                <small>
                                <?= e($item['author']) ?>
                                </small>
                                <strong>
                                <?= money((float)$item['price']*(int)$item['quantity']) ?>
                                </strong>
                                <div class="checkout-qty-control">
                                <button type="button" data-qty-minus aria-label="Kurangi jumlah">
                                −
                                </button>
                                <span data-qty-value>
                                <?= (int)$item['quantity'] ?>
                                </span>
                                <button type="button" data-qty-plus aria-label="Tambah jumlah">
                                +
                                </button>
                                <input
                                    type="hidden"
                                    name="checkout_quantities[<?= (int)$item['cart_id'] ?>]"
                                    value="<?= (int)$item['quantity'] ?>"
                                >
                                </div>
                                </div>
                                <button
                                    type="button"
                                    class="checkout-remove-item"
                                    data-qty-remove
                                    aria-label="Hapus buku dari pesanan"
                                >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M4 7h16M9 7V4h6v3m3 0-1 14H7L6 7m3 4v6m6-6v6"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                 />
                                </svg>
                                </button>
                                </div>
                                <?php endforeach; ?>
                                </div>
                                <div class="checkout-summary-line">
                                <span>
                        Subtotal (
                                <span id="checkout-book-count">
                                <?= array_sum(array_map(static fn(array $item):int=>(int)$item['quantity'],$cartItems)) ?>
                                </span>
                        Buku)
                                </span>
                                <b id="checkout-subtotal">
                                <?= money($cartSubtotal) ?>
                                </b>
                                </div>
                                <div class="checkout-summary-line">
                                <span>
                        Ongkos Kirim
                                </span>
                                <b id="checkout-shipping-cost">
                                <?= money($selectedShipping) ?>
                                </b>
                                </div>
                                <div class="checkout-summary-total">
                                <span>
                        Total Pembayaran
                                </span>
                                <b id="checkout-grand-total">
                                <?= money($cartSubtotal+$selectedShipping) ?>
                                </b>
                                </div>
                                <button
                                    class="button button-primary button-full checkout-submit"
                                    type="submit"
                                >
                                <?= ui_icon('lock') ?>
                    Buat Pesanan
                                </button>
                                <small class="checkout-summary-footnote">
                    Pastikan alamat dan pilihan pengiriman sudah benar sebelum membuat pesanan.
                                </small>
                                </aside>
                                </form>
                                <?php endif; ?>
                            </section>
                            <script>
document.addEventListener('DOMContentLoaded',function() {
    const savedAddresses=
    <?= json_encode($savedAddresses??[],JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>
    ,editor=document.getElementById('checkout-address-editor'),picker=document.getElementById('saved-address-picker'),
    select=document.getElementById('saved-address-select'),edit=document.getElementById('edit-address-toggle'),
    other=document.getElementById('other-address-toggle');
    const names=['recipient_name','phone','shipping_address','city','province','postal_code'];
    function fill(a) {
        if(!a)return;
        names.forEach(n=> {
            const el=document.querySelector('[name="'+n+'"]'),k=n==='shipping_address'?'address_detail':n;
            if(el)el.value=a[k]||''
        });
        const put=(id,v)=> {
            const el=document.getElementById(id);
            if(el)el.textContent=v||''
        };
        put('preview-recipient',a.recipient_name);
        put('preview-phone',a.phone);
        put('preview-address',[a.address_detail,a.city,a.province,a.postal_code].filter(Boolean).join(', '))
    }
    function showEditor(show) {
        if(!editor)return;
        editor.hidden=!show;
        editor.querySelectorAll('input,textarea').forEach(el=>el.required=show)
    }
    if(editor && editor.hidden) showEditor(false);
    if(edit)edit.addEventListener('click',()=>showEditor(editor.hidden));
    if(other&&picker)other.addEventListener('click',()=>picker.hidden=!picker.hidden);
    if(select)select.addEventListener('change',function() {
        if(this.value==='manual') {
            names.forEach(n=> {
                const el=document.querySelector('[name="'+n+'"]');
                if(el)el.value=''
            });
            showEditor(true);
            return
        }
        fill(savedAddresses[Number(this.value)]);
        showEditor(false);
        picker.hidden=true
    });
    let subtotal=
    <?= json_encode((float)$cartSubtotal) ?>
    ;
    const money=n=>'Rp '+new Intl.NumberFormat('id-ID').format(n),shipping=document.getElementById('checkout-shipping-cost'),
    total=document.getElementById('checkout-grand-total'),subtotalEl=document.getElementById('checkout-subtotal'),
    bookCountEl=document.getElementById('checkout-book-count');
    function recalcSummary() {
        let sum=0,count=0;
        document.querySelectorAll('.checkout-summary-item').forEach(row=> {
            const qty=Number(row.querySelector('[data-qty-value]')?.textContent||0),unit=Number(row.dataset.unitPrice||0);
            sum+=qty*unit;
            count+=qty;
            const price=row.querySelector('.checkout-summary-book strong');
            if(price)price.textContent=money(qty*unit)
        });
        subtotal=sum;
        if(subtotalEl)subtotalEl.textContent=money(sum);
        if(bookCountEl)bookCountEl.textContent=count;
        shippingUpdate();
        const submit=document.querySelector('.checkout-submit');
        if(submit)submit.disabled=count===0
    }
    document.querySelectorAll('.checkout-summary-item').forEach(row=> {
        const hidden=row.querySelector('input[type=hidden]'),value=row.querySelector('[data-qty-value]');
        function setQty(n) {
            n=Math.max(0,Math.min(Number(row.dataset.stock||99),n));
            value.textContent=n;
            hidden.value=n;
            if(n===0)row.classList.add('is-removed');
            else row.classList.remove('is-removed');
            recalcSummary()
        }
        row.querySelector('[data-qty-minus]').addEventListener('click',()=>setQty(Number(value.textContent)-1));
        row.querySelector('[data-qty-plus]').addEventListener('click',()=>setQty(Number(value.textContent)+1));
        row.querySelector('[data-qty-remove]').addEventListener('click',()=>setQty(0))
    });
    function shippingUpdate() {
        const r=document.querySelector('input[name="courier"]:checked');
        if(!r)return;
        const cost=Number(r.dataset.cost||0);
        if(shipping)shipping.textContent=money(cost);
        if(total)total.textContent=money(subtotal+cost);
        document.querySelectorAll('.checkout-shipping-option').forEach(x=>x.classList.toggle('is-selected',
        x.querySelector('input').checked))
    }
    document.querySelectorAll('input[name="courier"]').forEach(x=>x.addEventListener('change',shippingUpdate));
    shippingUpdate();
    const paymentLabels=document.querySelectorAll('.checkout-payment-option');
    function paymentUpdate() {
        paymentLabels.forEach(label=> {
            const radio=label.querySelector('input'),detail=label.nextElementSibling;
            label.classList.toggle('is-selected',radio.checked);
            if(detail&&detail.classList.contains('checkout-payment-detail'))detail.hidden=!radio.checked
        })
    }
    document.querySelectorAll('input[name="payment_method"]').forEach(x=>x.addEventListener('change',
    paymentUpdate));
    paymentUpdate();
    recalcSummary();
});
</script>
