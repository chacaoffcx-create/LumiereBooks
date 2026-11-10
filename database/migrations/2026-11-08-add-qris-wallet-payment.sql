-- Jalankan SATU KALI pada database Lumière Books yang sudah pernah diimpor.
-- Menambahkan opsi QRIS untuk simulasi dompet digital (DANA, OVO, GoPay).
ALTER TABLE orders
    MODIFY payment_method ENUM('bank_transfer', 'qris', 'cod') NOT NULL;
