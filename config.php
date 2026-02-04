<?php
$host = "localhost";
$user = "pkl_user";   
$password = "pkl123";    
$db = "portal_pkl";      

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>