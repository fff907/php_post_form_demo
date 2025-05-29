<?php
require_once 'db_connect.php';

// URLのクエリパラメータから記事IDを取得
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit("<div class='container mt-5'><p class='alert alert-danger'>記事が見つかりません。</p>
          <p><a href='index.php' class='btn btn-primary'><i class='bi bi-arrow-left'></i> 記事一覧へ戻る</a></p></div>");
}

// 記事を取得
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    exit("<div class='container mt-5'><p class='alert alert-danger'>記事が見つかりません。</p>
          <p><a href='index.php' class='btn btn-primary'><i class='bi bi-arrow-left'></i> 記事一覧へ戻る</a></p></div>");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事編集</title>
    <!-- BootstrapのCSSを読み込む -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons（矢印アイコン用） -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-pencil"></i> 記事を編集</h2>

    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?php echo $article['id']; ?>">

        <div class="mb-3">
            <label for="title" class="form-label">タイトル:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">本文:</label>
            <textarea id="content" name="content" rows="5" class="form-control" required><?php echo htmlspecialchars($article['content']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> 記事を更新</button>
        <a href="index.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> 記事一覧へ戻る</a>
    </form>
</div>

<!-- BootstrapのJSを読み込む -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>