<?php
declare(strict_types=1);

function handle_account_post(): void
{
    verify_csrf();
    $user = require_login();
    $pdo = db();
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'upload_avatar') {
            $file = $_FILES['avatar'] ?? null;
            if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
                throw new RuntimeException('Pilih foto profil terlebih dahulu.');
            }
            if ((int) $file['size'] > 3 * 1024 * 1024) {
                throw new RuntimeException('Ukuran foto maksimal 3 MB.');
            }
            if (!is_uploaded_file($file['tmp_name'])) {
                throw new RuntimeException('Berkas foto tidak valid. Silakan pilih ulang.');
            }
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $imageInfo = @getimagesize($file['tmp_name']);
            if (!isset($allowed[$mime]) || !$imageInfo || !in_array($imageInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
                throw new RuntimeException('Format foto harus JPG, PNG, atau WEBP.');
            }
            $uploadDir = lumiere_storage_root() . '/avatars';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                throw new RuntimeException('Folder penyimpanan foto tidak dapat dibuat.');
            }
            $fileName = 'user-' . (int) $user['id'] . '-' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
            $destination = $uploadDir . '/' . $fileName;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new RuntimeException('Foto gagal disimpan. Periksa izin folder uploads.');
            }
            $relativePath = 'storage/avatars/' . $fileName;
            $pdo->prepare('UPDATE users SET avatar=? WHERE id=?')->execute([$relativePath, $user['id']]);
            $oldAvatar = (string) ($user['avatar'] ?? '');
            if (str_starts_with($oldAvatar, 'storage/avatars/')) {
                $oldPath = lumiere_storage_file(substr($oldAvatar, strlen('storage/')));
                if (is_file($oldPath)) @unlink($oldPath);
            } elseif (str_starts_with($oldAvatar, 'uploads/avatars/')) {
                // Clean up legacy avatar files from earlier versions.
                $oldPath = dirname(__DIR__, 2) . '/public/assets/' . $oldAvatar;
                if (is_file($oldPath)) @unlink($oldPath);
            }
            flash('success', 'Foto profil berhasil diperbarui.');
        } elseif ($action === 'favorite_toggle') {
            $bookId = (int) ($_POST['book_id'] ?? 0);
            $check = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND book_id = ?');
            $check->execute([$user['id'], $bookId]);
            if ($check->fetchColumn()) {
                $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND book_id = ?')->execute([$user['id'], $bookId]);
                flash('success', 'Buku dihapus dari favorit.');
            } else {
                $pdo->prepare('INSERT IGNORE INTO favorites (user_id, book_id) VALUES (?, ?)')->execute([$user['id'], $bookId]);
                flash('success', 'Buku ditambahkan ke favorit.');
            }
        } elseif ($action === 'favorites_delete_all') {
            $pdo->prepare('DELETE FROM favorites WHERE user_id=?')->execute([$user['id']]);
            flash('success','Semua buku favorit berhasil dihapus.');
        } elseif ($action === 'save_profile') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $birth = trim((string) ($_POST['birth_date'] ?? '')) ?: null;
            $gender = trim((string) ($_POST['gender'] ?? '')) ?: null;
            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Nama dan email wajib diisi dengan benar.');
            $dup = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id <> ?'); $dup->execute([$email, $user['id']]);
            if ($dup->fetchColumn()) throw new RuntimeException('Email sudah digunakan akun lain.');
            $pdo->prepare('UPDATE users SET name=?, email=?, phone=?, birth_date=?, gender=? WHERE id=?')->execute([$name,$email,$phone,$birth,$gender,$user['id']]);
            $_SESSION['user']['name']=$name; $_SESSION['user']['email']=$email; $_SESSION['user']['phone']=$phone;
            flash('success','Profil berhasil diperbarui.');
        } elseif ($action === 'change_password') {
            $old=(string)($_POST['old_password']??''); $new=(string)($_POST['new_password']??''); $confirm=(string)($_POST['confirm_password']??'');
            $q=$pdo->prepare('SELECT password_hash FROM users WHERE id=?'); $q->execute([$user['id']]); $hash=$q->fetchColumn();
            if (!$hash || !password_verify($old,$hash)) throw new RuntimeException('Kata sandi lama tidak sesuai.');
            if (strlen($new)<8 || $new!==$confirm) throw new RuntimeException('Kata sandi baru minimal 8 karakter dan konfirmasi harus sama.');
            $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($new,PASSWORD_DEFAULT),$user['id']]);
            flash('success','Kata sandi berhasil diperbarui.');
        } elseif ($action === 'save_address') {
            $id=(int)($_POST['address_id']??0); $label=trim((string)($_POST['label']??'Rumah')); $recipient=trim((string)($_POST['recipient_name']??'')); $phone=trim((string)($_POST['phone']??''));
            $province=trim((string)($_POST['province']??'')); $city=trim((string)($_POST['city']??'')); $district=trim((string)($_POST['district']??'')); $village=trim((string)($_POST['village']??'')); $postal=trim((string)($_POST['postal_code']??'')); $detail=trim((string)($_POST['address_detail']??'')); $default=isset($_POST['is_default'])?1:0;
            if ($recipient===''||$phone===''||$province===''||$city===''||$postal===''||$detail==='') throw new RuntimeException('Lengkapi informasi alamat pengiriman.');
            if (!$id) { $hasAddress = $pdo->prepare('SELECT COUNT(*) FROM addresses WHERE user_id=?'); $hasAddress->execute([$user['id']]); if ((int)$hasAddress->fetchColumn() === 0) $default = 1; }
            if ($default) $pdo->prepare('UPDATE addresses SET is_default=0 WHERE user_id=?')->execute([$user['id']]);
            if ($id>0) $pdo->prepare('UPDATE addresses SET label=?,recipient_name=?,phone=?,province=?,city=?,district=?,village=?,postal_code=?,address_detail=?,is_default=? WHERE id=? AND user_id=?')->execute([$label,$recipient,$phone,$province,$city,$district,$village,$postal,$detail,$default,$id,$user['id']]);
            else $pdo->prepare('INSERT INTO addresses (user_id,label,recipient_name,phone,province,city,district,village,postal_code,address_detail,is_default) VALUES (?,?,?,?,?,?,?,?,?,?,?)')->execute([$user['id'],$label,$recipient,$phone,$province,$city,$district,$village,$postal,$detail,$default]);
            flash('success','Alamat berhasil disimpan.');
        } elseif ($action === 'delete_address') {
            $pdo->prepare('DELETE FROM addresses WHERE id=? AND user_id=?')->execute([(int)($_POST['address_id']??0),$user['id']]);
            flash('success','Alamat berhasil dihapus.');
        } elseif ($action === 'default_address') {
            $pdo->prepare('UPDATE addresses SET is_default=0 WHERE user_id=?')->execute([$user['id']]);
            $pdo->prepare('UPDATE addresses SET is_default=1 WHERE id=? AND user_id=?')->execute([(int)($_POST['address_id']??0),$user['id']]);
            flash('success','Alamat utama berhasil diubah.');
        } elseif ($action === 'notification_read') {
            $pdo->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?')->execute([(int)($_POST['notification_id']??0),$user['id']]);
            flash('success','Notifikasi ditandai sudah dibaca.');
        } elseif ($action === 'notifications_read_all') {
            $pdo->prepare('UPDATE notifications SET is_read=1 WHERE user_id=?')->execute([$user['id']]);
            flash('success','Semua notifikasi ditandai sudah dibaca.');
        } elseif ($action === 'notifications_delete_all') {
            $pdo->prepare('DELETE FROM notifications WHERE user_id=?')->execute([$user['id']]);
            flash('success','Semua notifikasi dihapus.');
        } else {
            throw new RuntimeException('Aksi akun tidak dikenali.');
        }
        $back=(string)($_POST['_back']??url('profile'));
        redirect(str_starts_with($back,'index.php?')?$back:url('profile'));
    } catch (Throwable $e) {
        flash('danger',$e->getMessage());
        $back=(string)($_POST['_back']??url('profile'));
        redirect(str_starts_with($back,'index.php?')?$back:url('profile'));
    }
}
