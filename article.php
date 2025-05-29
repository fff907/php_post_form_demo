<?php
require_once 'db_connect.php';

// URLのクエリパラメータから記事IDを取得（安全な取得方法）
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "<div class='container mt-5'><p class='alert alert-danger'>記事が見つかりません。</p>";
    echo '<p><a href="index.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> 記事一覧へ戻る</a></p></div>';
    exit();
}

// 記事を取得
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    echo "<div class='container mt-5'><p class='alert alert-danger'>記事が見つかりません。</p>";
    echo '<p><a href="index.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> 記事一覧へ戻る</a></p></div>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事詳細</title>
    
    <!-- BootstrapのCSSを読み込む（index.phpと統一！） -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons（矢印アイコン用） -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h3>
            <p class="text-muted"><i class="bi bi-clock"></i> 投稿日: <?php echo date("Y-m-d H:i:s", strtotime($article['created_at'])); ?></p>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
        </div>
    </div>

    <div class="mt-4">
        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="bi bi-pencil-square"></i> 編集する</a>
        <a href="index.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> 記事一覧へ戻る</a>
    </div>
</div>

<!-- BootstrapのJSを読み込む（index.phpと統一！） -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>