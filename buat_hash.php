<?php
// Ganti 'password_baru_anda' dengan password baru yang Anda inginkan.
// Pastikan Anda mengingat password ini!
$passwordPlainText = 'password123'; // <-- GANTI DENGAN PASSWORD BARU ANDA

// Membuat hash dari password menggunakan algoritma yang aman
$hashedPassword = password_hash($passwordPlainText, PASSWORD_DEFAULT);

// Menampilkan hash yang sudah dibuat
echo "Password Asli: " . $passwordPlainText . "<br><br>";
echo "Hash Baru Anda (salin teks di bawah ini):<br>";
echo "<textarea rows='4' cols='80' readonly>" . $hashedPassword . "</textarea>";

?>