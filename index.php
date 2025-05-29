<?php
require_once 'db_connect.php'; //  データベース接続

// エラーレポートの設定（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// タイムゾーンを設定（日本時間）
$conn->query("SET time_zone = '+09:00'");

// 記事一覧を取得
$sql = "SELECT * FROM articles ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事一覧</title>
    
    <!-- BootstrapのCSSを読み込む -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-primary"><i class="bi bi-journal-text"></i> 記事一覧</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h3 class="card-title">
                    <a href="article.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                </h3>
                <p class="text-muted"><?php echo date("Y-m-d H:i:s", strtotime($row['created_at'])); ?></p>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

                <form action="delete.php" method="post" class="mt-2">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('本当に削除しますか？');">
                        削除
                    </button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- BootstrapのJSを読み込む -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
