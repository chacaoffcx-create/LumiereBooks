document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const mainNav = document.querySelector('[data-main-nav]');

    menuToggle?.addEventListener('click', () => {
        mainNav?.classList.toggle('is-open');
    });

    document.querySelectorAll('[data-alert-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-alert]')?.remove());
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    const bankSelectWrap = document.getElementById('bank-select-wrap');

    const updateBankSelectVisibility = () => {
        if (!bankSelectWrap) return;
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
        bankSelectWrap.hidden = selectedMethod !== 'bank_transfer';
    };

    paymentMethodInputs.forEach((input) => {
        input.addEventListener('change', updateBankSelectVisibility);
    });
    updateBankSelectVisibility();

    const adminToggle = document.querySelector('[data-admin-toggle]');
    const adminSidebar = document.querySelector('[data-admin-sidebar]');
    adminToggle?.addEventListener('click', () => {
        adminSidebar?.classList.toggle('is-open');
    });

    if (window.Chart && window.ashRevenueData) {
        const canvas = document.getElementById('revenueChart');
        if (canvas) {
            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: window.ashRevenueData.labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: window.ashRevenueData.values,
                        borderColor: '#4d95c5',
                        backgroundColor: 'rgba(100, 170, 210, .16)',
                        fill: true,
                        tension: .35,
                        pointRadius: 3,
                        pointBackgroundColor: '#4d95c5'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) =>
                                    'Rp ' + Number(value).toLocaleString('id-ID'),
                            },
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }

    if (window.Chart && window.ashCategoryData) {
        const canvas = document.getElementById('categoryChart');
        if (canvas) {
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: window.ashCategoryData.labels,
                    datasets: [{
                        data: window.ashCategoryData.values,
                        backgroundColor: ['#4d91bd', '#76b5d2', '#8ac8bd', '#f0c17a', '#d88d9a', '#b2cde0'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        }
    }

    // Tambah ke keranjang tanpa meninggalkan halaman saat ini.
    const cartToast = (message, type = 'success') => {
        let toast = document.querySelector('[data-cart-toast]');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'cart-toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            toast.dataset.cartToast = 'true';
            document.body.appendChild(toast);
        }
        toast.className = `cart-toast is-${type}`;
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(window.__lumiereCartToastTimer);
        window.__lumiereCartToastTimer = window.setTimeout(() => {
            toast.classList.remove('is-visible');
        }, 2800);
    };

    document.querySelectorAll('form.js-add-to-cart').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton?.disabled) return;

            const originalButtonContent = submitButton?.innerHTML;
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('is-adding');
                submitButton.setAttribute('aria-busy', 'true');
            }

            try {
                const cartEndpoint = form.dataset.cartEndpoint || form.getAttribute('action') || 'index.php';
                const response = await fetch(new URL(cartEndpoint, window.location.href).toString(), {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const responseText = await response.text();
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    // The central index.php cart action should always return JSON for AJAX requests.
                    // Keep the page in place and show a useful message if the server fails.
                    const statusMessage = response.status === 404
                        ? 'Alamat proses keranjang tidak dapat diakses. Periksa kembali folder proyek Lumière Books dan coba muat ulang halaman.'
                        : response.status === 419
                            ? 'Sesi halaman sudah kedaluwarsa. Muat ulang halaman dan coba lagi.'
                            : response.status >= 500
                                ? 'Server sedang bermasalah saat menambahkan buku. Coba lagi beberapa saat.'
                                : 'Respons keranjang tidak dapat dibaca. Muat ulang halaman dan coba lagi.';
                    throw new Error(statusMessage);
                }

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Buku belum berhasil ditambahkan.');
                }

                const cartBadge = document.querySelector('.cart-link span');
                if (cartBadge && typeof result.cart_count !== 'undefined') {
                    cartBadge.textContent = result.cart_count;
                    cartBadge.classList.add('has-update');
                    window.setTimeout(() => cartBadge.classList.remove('has-update'), 650);
                }

                cartToast(result.message || 'Buku berhasil ditambahkan ke keranjang!');
            } catch (error) {
                cartToast(error.message || 'Terjadi kesalahan. Silakan coba lagi.', 'error');
                if (error.message && error.message.includes('Sesi halaman sudah kedaluwarsa')) {
                    window.setTimeout(() => window.location.reload(), 1600);
                }
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('is-adding');
                    submitButton.removeAttribute('aria-busy');
                    if (originalButtonContent !== null) {
                        submitButton.innerHTML = originalButtonContent;
                    }
                }
            }
        });
    });

});
