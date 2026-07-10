<?php

$password = 'admin123'; 

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h3>Générateur Password Hash</h3>";
echo "Password: <b>$password</b><br>";
echo "Hash: <br><textarea rows='3' cols='80'>$hash</textarea><br>";
echo "<br>";
?>