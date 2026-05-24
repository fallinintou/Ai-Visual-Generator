<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "ai_visual_generator";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/*
    Pollinations AI
    Tidak butuh API key.
*/
$AI_PROVIDER = "pollinations";
$AI_BASE_URL = "https://image.pollinations.ai/prompt/";

?>