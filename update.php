<?php
require_once 'db_connect.php';

// エラーレポート設定（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// フォームデータの取得（安全に処理）
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

if (!$id || !$title || !$content) {
    exit("エラー: すべての項目を入力してください。");
}

// 記事を更新
$sql = "UPDATE articles SET title = ?, content = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("SQLエラー: " . $conn->error);
}

$stmt->bind_param("ssi", $title, $content, $id);

if ($stmt->execute()) {
    // 更新成功 → 記事ページへリダイレクト
    header("Location: article.php?id=" . $id);
    exit();
} else {
    exit("エラー: " . $stmt->error);
}

// 接続を閉じる
$stmt->close();
$conn->close();
?>
