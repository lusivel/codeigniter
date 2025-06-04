<?php
$password_mentah = 'developer123';
$hashed_password_baru = password_hash($password_mentah, PASSWORD_DEFAULT);

echo "Password Mentah: " . $password_mentah . "<br>";
echo "Hash Baru dari Password Mentah: " . $hashed_password_baru . "<br><br>";

// Coba verifikasi hash yang ada di database Master
$hash_dari_db = '$2y$10$fQ7x.8Y8u6j0g5j0.z9m0u.9G6g7g8h9.k0m1g2j3l4n5o6p7q8r9s0.'; // Salin hash ini dari DB Master
if (password_verify($password_mentah, $hash_dari_db)) {
    echo "VERIFIKASI SUKSES: Password mentah cocok dengan hash dari DB!";
} else {
    echo "VERIFIKASI GAGAL: Password mentah TIDAK cocok dengan hash dari DB.";
}
?>