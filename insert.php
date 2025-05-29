<?php
require_once 'db_connect.php';

// エラーレポート設定（デバッグ用・本番環境ではOFF推奨）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// POSTデータの取得
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

if (!$title || !$content) {
    exit("エラー: タイトルまたは本文が空です。");
}

// SQLを準備（プリペアドステートメント）
$sql = "INSERT INTO articles (title, content, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("SQLエラー: " . $conn->error);
}

$stmt->bind_param("ss", $title, $content);

// SQLの実行
if ($stmt->execute()) {
    // 投稿成功 → 記事一覧へリダイレクト
    header("Location: index.php");
    exit();
} else {
    exit("エラー: " . $stmt->error);
}

// 接続を閉じる
$stmt->close();
$conn->close();
?>
