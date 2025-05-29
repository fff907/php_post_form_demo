<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";
// 本番用は環境変数で管理

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続チェック
if ($conn->connect_error) {
    exit("データベース接続失敗: " . $conn->connect_error);
}

// 文字コードを UTF-8 に設定
$conn->set_charset("utf8mb4");
?>
