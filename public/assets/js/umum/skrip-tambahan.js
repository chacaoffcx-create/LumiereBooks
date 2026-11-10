document.addEventListener('DOMContentLoaded', () => {
    const adminToggle = document.querySelector('[data-admin-toggle]');
    const adminSidebar = document.querySelector('[data-admin-sidebar]');

    adminToggle?.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-open');
    });

    document.addEventListener('click', (event) => {
        if (
            document.body.classList.contains('sidebar-open') &&
            adminSidebar &&
            !adminSidebar.contains(event.target) &&
            !adminToggle?.contains(event.target)
        ) {
            document.body.classList.remove('sidebar-open');
        }
    });

    document.querySelectorAll('[data-image-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file || !file.type.startsWith('image/')) {
                return;
            }

            const preview = input
                .closest('.upload-dropzone')
                ?.querySelector('.upload-preview');

            if (!preview) {
                return;
            }

            const image = document.createElement('img');
            image.alt = 'Pratinjau gambar yang dipilih';
            image.src = URL.createObjectURL(file);
            preview.replaceChildren(image);
        });
    });

    const userDialog = document.getElementById('userDetailDialog');

    document.querySelectorAll('[data-user-detail]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!userDialog) {
                return;
            }

            userDialog.querySelector('[data-detail-name]').textContent =
                button.dataset.userName || '-';
            userDialog.querySelector('[data-detail-email]').textContent =
                button.dataset.userEmail || '-';
            userDialog.querySelector('[data-detail-phone]').textContent =
                button.dataset.userPhone || '-';
            userDialog.querySelector('[data-detail-created]').textContent =
                button.dataset.userCreated || '-';
            userDialog.querySelector('[data-detail-orders]').textContent =
                button.dataset.userOrders || '0';

            userDialog.showModal();
        });
    });

    userDialog?.querySelector('[data-dialog-close]')?.addEventListener(
        'click',
        () => userDialog.close()
    );

    userDialog?.addEventListener('click', (event) => {
        if (event.target === userDialog) {
            userDialog.close();
        }
    });

    if (!window.Chart) {
        return;
    }

    if (window.ashOrderStatusData) {
        const canvas = document.getElementById('orderStatusChart');

        if (canvas) {
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: window.ashOrderStatusData.labels,
                    datasets: [{
                        data: window.ashOrderStatusData.values,
                        backgroundColor: [
                            '#f1bd6c',
                            '#6bc5b0',
                            '#76b7d7',
                            '#7eafe2',
                            '#e78b9b'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            });
        }
    }

    if (window.ashReportData) {
        const revenueCanvas = document.getElementById('reportRevenueChart');

        if (revenueCanvas) {
            new Chart(revenueCanvas, {
                type: 'line',
                data: {
                    labels: window.ashReportData.months,
                    datasets: [{
                        label: 'Pendapatan',
                        data: window.ashReportData.revenue,
                        borderColor: '#4f91ba',
                        backgroundColor: 'rgba(94, 159, 198, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#4f91ba'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) =>
                                    'Rp ' + Number(value).toLocaleString('id-ID')
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        const categoryCanvas = document.getElementById('categoryChart');

        if (categoryCanvas) {
            new Chart(categoryCanvas, {
                type: 'doughnut',
                data: {
                    labels: window.ashReportData.categories,
                    datasets: [{
                        data: window.ashReportData.categoryRevenue,
                        backgroundColor: [
                            '#4f91ba',
                            '#70afd0',
                            '#8ac9bc',
                            '#f0c17a',
                            '#d88d9a',
                            '#a6c8dd',
                            '#b7d7e8'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        const paymentCanvas = document.getElementById('paymentMethodChart');

        if (paymentCanvas) {
            new Chart(paymentCanvas, {
                type: 'bar',
                data: {
                    labels: window.ashReportData.paymentMethods,
                    datasets: [{
                        label: 'Pendapatan',
                        data: window.ashReportData.paymentRevenue,
                        backgroundColor: '#66a8ce',
                        borderRadius: 5,
                        maxBarThickness: 52
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) =>
                                    'Rp ' + Number(value).toLocaleString('id-ID')
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }
});
