<?php
require_once 'db_connect.php';

// エラーレポートの設定（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// POSTリクエストかどうかを確認
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("不正なアクセスです。");
}

// 記事IDを取得（安全に整数型で取得）
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit("削除する記事が指定されていません！");
}

// SQLを準備（プリペアドステートメント）
$sql = "DELETE FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("SQLエラー: " . $conn->error);
}

$stmt->bind_param("i", $id);

// SQLの実行
if ($stmt->execute()) {
    // 削除成功 → 記事一覧にリダイレクト
    header("Location: index.php");
    exit();
} else {
    exit("エラーが発生しました: " . $conn->error);
}

// 接続を閉じる
$stmt->close();
$conn->close();
?>