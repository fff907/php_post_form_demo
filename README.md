# PHP投稿管理システム（スキルデモ）

このリポジトリは、PHPとMySQLを用いて構築した簡易的な投稿管理システムのコードです。  
投稿フォームからのデータ送信、データベースへの接続・保存、記事の一覧表示・編集・削除といった一連の処理を通じて、サーバーサイド開発の基礎を学習・実装しました。

## 使用技術

- PHP / HTML / Bootstrap
- MySQL（InfinityFree 環境）
- GitHub（コード管理）

## 実装の流れと構成ファイル

このアプリケーションは以下のファイルで構成されています。  
それぞれの役割と処理内容を順に解説していきます。

- `post_form.html`  
　投稿フォームのフロント画面。タイトル・本文を入力し、`insert.php` にPOST送信します。

- `insert.php`  
　フォームから送られたデータをバリデーション（未入力チェック）し、データベースに登録します。

- `db_connect.php`  
　データベースとの接続処理（PDO）を共通化したファイルです。

- `index.php`  
　投稿された記事を一覧で表示し、編集・削除へのリンクも提供します。

- `edit.php`  
　既存の記事データを読み込み、フォームに反映する編集画面です。

- `update.php`  
　`edit.php`から送信された編集内容をデータベースに反映します。

- `delete.php`  
　指定された記事IDをもとに、対象データをデータベースから削除します。

- `article.php`  
　（オプション）特定の記事の詳細表示ページとして使用可能なファイルです。

## 各ファイルの解説

### post_form.html

投稿画面のフロントエンド（HTML）を担当するファイルです。ユーザーが「記事のタイトル」と「本文」を入力して `insert.php` にデータを送信します。

- **使用技術**：HTML5 / Bootstrap 5 / Bootstrap Icons

- **主な構造**：
  - `<form>` 要素を使って `insert.php` に `POST` メソッドで送信
  - 必須入力 (`required`) によるクライアントサイドバリデーション
  - 入力欄（タイトル・本文）＋送信ボタン
  - `index.php` への戻るボタンも設置

- **ポイント**：
  - BootstrapをCDNから読み込み、すぐに整ったUIが使える
  - ボタンにアイコン（✏️・⬅）を表示し、視認性と操作性を向上
  - PHP側との接続は `form action="insert.php"` で行う（実際の登録処理はPHPで行われる）  
 
    > CDNとは？  
    > "Content Delivery Network（コンテンツ配信ネットワーク）" の略。  
    > Bootstrapなどの外部ライブラリを、インターネット上の高速なサーバーから読み込む仕組み。  
    > ローカルにファイルを置かなくても簡単に利用できる。  

- **コード解説**：
  - ` <form action="insert.php" method="post">`  
    → 入力データを `insert.php` に `POST` メソッドで送信。PHP側でのバリデーション・DB登録処理に渡します。

  - `<input type="text" id="title" name="title" class="form-control" required>`  
    → ユーザーが投稿する「タイトル」の入力欄。`required` により未入力時の送信を防止。

  - `<textarea id="content" name="content" rows="5" class="form-control" required></textarea>`  
    → 投稿本文の入力欄。こちらも `required` 指定により空欄送信を防止。

  - `<button type="submit" class="btn btn-warning">`  
    → Bootstrapの `btn` `btn-warning` クラスでスタイル付けされた送信ボタン。中の `<i>` タグでアイコンを付加しています。

  - `<a href="index.php" class="btn btn-primary">`  
    → 記事一覧画面（`index.php`）への戻るリンク。ユーザーの操作導線を明示します。

### insert.php

投稿フォームから送られたデータを受け取り、バリデーション（未入力チェック）を行った上で、MySQLに保存する処理を行うPHPファイルです。

- **使用技術**：PHP / MySQL（filter_input / prepare / bind_param など）

- **主な構造**：
  - `filter_input()`＋`FILTER_SANITIZE_STRING` で入力の安全な取得・サニタイズ
  - 未入力の場合は `exit()` で処理を終了
  - `db_connect.php` を読み込んでDB接続
  - プリペアドステートメントを `prepare()` し、`bind_param()` で値を渡して `execute()`
  - 投稿が成功したら `index.php` にリダイレクト
 
    > サニタイズ（sanitize）とは？  
    > 不正な文字列を除去または無害化する処理のこと。
    > `filter_input()` で、入力された値を安全に取り出す。`FILTER_SANITIZE_STRING` はHTMLタグなどの危険な部分を除去するフィルター。  
    
    > プリペアドステートメントとは？  
    > SQL文を事前に準備しておいて、あとからデータを安全に流し込む仕組み。  
    > SQLインジェクション（悪意ある入力による攻撃）を防ぐことができる。  

- **ポイント**：
  - `filter_input()` による **POSTデータの安全な取得**
  - `FILTER_SANITIZE_STRING` で **HTMLタグなどの除去** を行う
  - `prepare()` による **SQLインジェクション対策**
  - 成功時は `header("Location: index.php")` で自然に一覧ページへ遷移

- **コード解説**：

  ```php
  require_once 'db_connect.php';
  ```
  → DB接続設定を外部ファイルから読み込むことで、保守性・再利用性を高めている。

    > `require_once`（リクワイア・ワンス）とは？  
    > 指定したファイルを「一度だけ」読み込む命令。  
    > データベース接続設定などの共通処理を別ファイルにしておくことで、再利用できて便利。
    
    > なぜ `require_once` なのか？  
    > `require`：そのファイルがなければエラーを出して止める。  
    > `require_once`：かつ「すでに読み込まれていればもう一度は読み込まない」。  
    > → これにより、重複読み込みのエラーを防げる（**特に複数のファイルが連携する大規模アプリでは重要**）。

  ```php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  ```
    > `ini_set`（イニセット）とは？  
    > PHPの設定を「一時的に変更」する関数。  
    > `'display_errors', 1` は「エラーを表示する」という意味。  
    > 本番環境では通常 `0`（非表示）にする。  
    > `error_reporting(E_ALL)` は「すべてのエラーを報告する」設定。

  ```php
  $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
  $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
  ```
  → それぞれの値をHTMLエスケープしながら安全に取得。空なら false になる。

    > `filter_input()`（フィルター・インプット）とは？  
    > フォームやURLなどから送られてきたデータを、**安全に取り出す関数**。  
    > 入力を直接使わず、事前に「この形式のデータしか通さない」と決めておけるのが特徴。  
    >
    > 例（URLから整数だけを受け取る）：
    > ```php
    > $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    > ```
    > → `?id=3` などの整数だけを取得。文字列などの不正な値は `false` になる。
    >
    > `insert.php` でも、POST送信された文字列をサニタイズするために使用：
    > ```php
    > $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    > ```
    > → このように、**POSTもGETも安全に扱える**のが `filter_input()` の利点。

  ```php
  if (!$title || !$content) {
    exit("エラー: タイトルまたは本文が空です。");
  }
  ```
  → どちらかでも未入力ならエラーメッセージを表示して終了。

  ```php
  $sql = "INSERT INTO articles (title, content, created_at) VALUES (?, ?, NOW())";
  $stmt = $conn->prepare($sql);
  ```
  → プリペアドステートメントとしてSQL文を準備。動的に値を差し込める。

  ```php
  $stmt->bind_param("ss", $title, $content);
  ```
  → プレースホルダに実際の値を紐づけ（"ss" は2つの文字列型）。

    > `bind_param`（バインド・パラメータ）とは？  
    > プレースホルダに「実際の値」を結びつける（バインドする）処理。  
    > "ss" は2つの文字列（string）を意味する。  
    > 他には「i（整数）」「d（小数）」「b（バイナリ）」がある。  
    > 例：`bind_param("is", $name, $age)` → 文字列＋整数  
    
    > プレースホルダとは？  
    > SQL文内の「あとで値を入れる部分」を示す記号。  
    > 例：`VALUES (?, ?, NOW())` の `?` がそれぞれ `title` と `content` に対応。  

  ```php
  if ($stmt->execute()) {
    header("Location: index.php");
    exit();
  } else {
      exit("エラー: " . $stmt->error);
  }
  ```
  → 成功すれば一覧へ自動遷移、失敗した場合はその原因（エラー内容）を表示。

    > `execute`（エグゼキュート）とは？  
    > プリペアドステートメントを実行する命令。  
    > つまり、DBへの実際の「登録・更新・削除」がここで行われる。  

  ```php
  $stmt->close();
  $conn->close();
  ```
  → 使用したリソースをしっかり解放。

    > `$stmt` / `$conn` の意味は？        
    > `$stmt`：statement（文）の略。プリペアドステートメントを扱うオブジェクト。  
    > `$conn`：connection（接続）の略。DBとの接続オブジェクト。   

### db_connect.php

MySQLデータベースに接続するためのファイルです。全ページで共通して使うため、他のPHPファイルから `require_once 'db_connect.php';` のように読み込んで使用します。**シンプルながら重要な基盤ファイル**です。

- **使用技術**：PHP / MySQLi（オブジェクト指向スタイル）

- **主な構造**：
  - 接続情報（ホスト名、ユーザー名、パスワード、DB名）を変数で定義
  - `new mysqli()` により接続を試みる
  - 接続に失敗したらエラーメッセージを出して `exit()`
  - 成功後は文字コードを `utf8mb4` に設定

    > `utf8mb4` とは？  
    > UTF-8の拡張版で、絵文字など4バイトの文字にも対応。  
    > 通常の `utf8` は3バイトまでなので、最近は `utf8mb4` が推奨される。  

- **ポイント**：
  - **共通ファイル化**により、接続設定を一元管理・再利用できる
  - **エラー時に強制終了**させることで、不具合の原因を明示
  - **文字化け対策**として `set_charset()` の設定が重要

- **コード解説**：

  ```php
  $servername = "localhost";
  $username = "your_username";
  $password = "your_password";
  $dbname = "your_database";
  ```
  → 接続に必要な各種情報を変数として定義。セキュリティの観点から、**パスワードは直接記述せず環境変数で管理するのが理想的。**

    > 環境変数とは？  
    > パスワードをPHPコードに直接書くのはセキュリティ上NGです。
    > `getenv()` を使って、**サーバ側の環境変数や `.env` ファイルから読み込む方法**が安全です。
    > 現在は簡易構成のため `db_connect.php` に記述していますが、将来的には以下のように `getenv()` を用いた構成に移行予定です。
    > ```php
    > $password = getenv("DB_PASSWORD");
    > ```
    > これにより、コードをGitHubに公開してもパスワードが漏れることはありません。　

  ```php
  $conn = new mysqli($servername, $username, $password, $dbname);
  ```
  → `mysqli` オブジェクトを使って接続処理を行う。成功すれば `$conn` に接続インスタンスが代入される。

    > `new mysqli()`（マイエスキューエルアイ）とは？  
    > PHPからMySQLに接続するための「クラス」。  
    > オブジェクト指向スタイルで、安全かつ柔軟に接続できる。
    
    > オブジェクト指向・インスタンスとは？    
    > `new mysqli(...)` は「**MySQL接続用のオブジェクトを作る命令**」です。  
    > `$conn` という変数に、**接続インスタンス（= 実際に使える接続の“モノ”）** を代入します。
    >
    > - クラス = 設計図（例：スマホの設計）  
    > - オブジェクト = 実体（例：iPhone）
    >
    > `mysqli` は接続処理の設計図で、`$conn` がその実体です。   
    > これにより、`$conn->query()` や `$conn->prepare()` などの命令が使えるようになります。

  ```php
  if ($conn->connect_error) {
    exit("データベース接続失敗: " . $conn->connect_error);
  }
  ```
  → 接続に失敗した場合は、詳細なエラーメッセージを表示して処理を中断する。

    > `connect_error` とは？  
    > 接続に失敗したときのエラーメッセージを保持するプロパティ。  
    > `exit()` によって、その場でスクリプトの実行を終了できる。  

  ```php
  $conn->set_charset("utf8mb4");
  ```
  → データベースとPHP間の文字コードを `utf8mb4` に統一することで、文字化けを防止。**必須の設定項目。**

### index.php

記事の一覧を表示するメインページです。データベースから記事を取得し、Bootstrapで整えたカード形式で一覧表示します。

- **使用技術**：PHP / MySQL / Bootstrap

- **主な構造**：
  - `db_connect.php` を読み込んでDB接続
  - タイムゾーンを設定（+09:00＝日本時間）
  - `SELECT * FROM articles` によって記事を取得
  - 1件ずつ `fetch_assoc()`（フェッチ・アソシエイティブ）で取り出し、HTML内に出力
  - 削除ボタン付き（`delete.php` にPOST）

    > `$conn->query("SET time_zone = '+09:00'");` とは？  
    > データベース内の時刻を日本時間に合わせる命令。これにより `created_at` などの日時が正しく表示されます。

- **ポイント**：
  - `htmlspecialchars()` でXSS対策
  - `nl2br()` により改行がHTML上で反映される
  - Bootstrapによるシンプルなデザイン適用
  - 投稿の個別ページへリンク → `article.php?id=...`
  - 削除フォーム付き → `delete.php` にidを渡す構成  

- **コード解説**：

  ```php
  $sql = "SELECT * FROM articles ORDER BY created_at DESC";
  $result = $conn->query($sql);
  ```
  → 記事を新しい順（DESC）で取得。

    > **`SELECT * FROM articles ORDER BY created_at DESC` の意味は？**  
    > `SELECT *`：すべての列を取得  
    > `FROM articles`：articles テーブルから  
    > `ORDER BY created_at DESC`：作成日時（created_at）の降順（新しい順）で並べ替え  
    >  
    > 昇順（古い順）は `ASC`（アセンド）      
    > 降順（新しい順）は `DESC`（ディセンド）   
    >  
    > **覚え方**：  
    > - ASC：上がる → 古い順に積み重ねる  
    > - DESC：下がる → 新しい順に落ちてくる（上に表示）

  ```php
  while ($row = $result->fetch_assoc()):
  ```
  → 1件ずつ連想配列で取り出し、`$row['title']` などでアクセス。

    > **`<?php while ($row = $result->fetch_assoc()): ?>` の意味は？**  
    > SQLの結果（$result）から、1件ずつ `$row` に取り出して処理するループ。  
    > `fetch_assoc()` は **「カラム名をキーとした連想配列」** を返す。  
    > 連想配列 ＝「名前付きの箱」が並んだようなイメージ。  
    > `$row` = **1件の記事のすべての情報が入った変数**。  
    >  
    > 例：  
    > ```php
    > $row = [
    >   'id' => 1,
    >   'title' => 'テスト記事',
    >   'content' => '本文'
    > ];
    > ```

  ```php
  <a href="article.php?id=<?php echo $row['id']; ?>">
  ```
  → 各記事のタイトルをクリックすると `article.php` に遷移し、IDパラメータで個別記事を表示。

    > **`article.php?id=<?php echo $row['id']; ?>` の意味は？**  
    > `article.php?id=1` のような形で「IDパラメータ付きリンク」を生成。  
    > このIDを使って、`article.php` 側で **該当記事だけを表示**する。  
    > IDパラメータ = URLの中に `?id=1` のような形で、**ID情報を渡す仕組み**。  

  ```php
  <form action="delete.php" method="post">
  ```    
  → 削除ボタンでPOST送信。`hidden` で記事IDを送信し、`delete.php` 側で処理。
  
  ```php
  <p class="text-muted"><?php echo date("Y-m-d H:i:s", strtotime($row['created_at'])); ?></p>
  ```
  
    > **`date("Y-m-d H:i:s", strtotime($row['created_at']))` の意味は？**    
    > `created_at` は文字列の日付 → `strtotime()` でタイムスタンプ（数値）に変換   
    > → `date()` で見やすい形式に整形  
    > strtotime（ストラトゥータイム）→ 日付文字列を「タイムスタンプ」（数値）に変換  
    > `date()`→ 数値の時間を "年-月-日 時:分:秒" の形に整形    
    >  
    > 例：  
    > ```php
    > strtotime("2025-05-29 10:00:00") // → 秒数に変換  
    > date("Y/m/d", ...) // → "2025/05/29"
    > ```

  ```php  
  <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
  ```
  
    > **`nl2br(htmlspecialchars($row['content']))` の意味は？**  
    > - `htmlspecialchars()`：HTMLタグの表示用変換（XSS対策）  
    > - `nl2br()`：改行文字 `\n` を `<br>` に変換 → **見た目上の改行が反映される**  
    >
    >  `htmlspecialchars()`（エイチティーエムエル・スペシャル・キャラクターズ）  
    > → `<` や `&` を無害な文字に変える（XSS対策）   
    >
    > 例：  
    > ```php
    > $content = "こんにちは\n改行されます";
    > echo nl2br(htmlspecialchars($content));
    > // → こんにちは<br>改行されます
    > ```

    > **XSS（クロスサイトスクリプティング）とは？**  
    > XSS（Cross Site Scripting）は、**Webサイトの入力欄などにスクリプト（JavaScript）を埋め込むことで、他のユーザーのブラウザ上で勝手に実行させる攻撃手法**です。  
    >
    > たとえば、掲示板やコメント欄に以下のようなコードを投稿された場合：
    > ```html
    > <script>alert('XSS攻撃！');</script>
    > ```
    > この内容がそのまま表示されてしまうと、ページを見た人のブラウザでアラートが出たり、Cookie情報が盗まれたりする危険があります。対策として `htmlspecialchars()` を使うことで、悪意あるコードが**ただの文字列として表示される**ようになります。　　　　
    >
    > 例：  
    > ```php
    > htmlspecialchars('<script>alert("XSS!")</script>');
    > // 出力: &lt;script&gt;alert("XSS!")&lt;/script&gt;
    > ```  

  > **用語補足まとめ**：  
  > 🔹 **カラム（column）**：テーブル内の「項目」（例：タイトル、本文、日付）  
  > 🔸 **連想配列（associative array）**：名前で中身を取り出せる配列  
  > 🔹 **IDパラメータ**：URLにくっついてデータを渡す仕組み（例：`?id=1`）  
  > 🔸 **$row**：fetch_assoc() によって得られた「1件のデータのかたまり」

### edit.php

投稿済みの記事を編集するためのページ。URLから渡されたIDに対応する記事を取得し、フォームに値を埋め込んで表示、更新処理は `update.php` に渡す構成です。

- **使用技術**：PHP / MySQL（filter_input / prepare / bind_param など）

- **主な構造**：
  - `filter_input()` でURLパラメータ（id）を取得＋バリデーション
  - 該当する記事が存在するかチェック（なければエラー表示）
  - SQLの `SELECT` 文でデータベースから対象記事を取得
  - `htmlspecialchars()` でフォーム内の文字をエスケープ
  - 編集完了後は `update.php` にPOST送信

    > **URLパラメータとは？**  
    > URLの末尾に `?id=3` のように付けて、データを一緒に渡す仕組み。  
    > `edit.php?id=3` のようにアクセスすることで、記事IDを指定して読み込める。  
    > 技術的には「GETパラメータ」「クエリパラメータ」とも呼ばれる。

- **コード解説**：

```php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
```
→ GETパラメータ（URL）の `id` を整数として取得。正しくない場合は `false` になる。

```php
if (!$id) {
    exit("<div class='container mt-5'><p class='alert alert-danger'>記事が見つかりません。</p>
          <p><a href='index.php' class='btn btn-primary'><i class='bi bi-arrow-left'></i> 記事一覧へ戻る</a></p></div>");
}
```
→ `filter_input()` によって取得した `$id` が **0 や null（不正な値）**　だった場合、ここで処理を強制終了します。`exit()` 関数内にエラーメッセージ付きのHTMLを直接書くことで、その場で「記事が見つかりません」ページが表示される仕組みです。

```php
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();
```
→ `$sql` には「articles テーブルの中から id が一致するレコードを1件取り出す」というSQL文を記述します。  
`WHERE id = ?` の `?` は **プレースホルダ**と呼ばれ、後から `$id` の値を差し込む場所になります。

- **`$stmt = $conn->prepare($sql)`**  
  → SQL文を「準備」する。プリペアドステートメントとして安全に処理する準備段階です。

- **`$stmt->bind_param("i", $id)`**  
  → `?` に実際の値（この場合は整数 `$id`）をバインドします。  
　  `"i"` は整数（int）を意味しています。

- **`$stmt->execute()`**  
  → SQL文をデータベースに送って実行します。ここで実際に検索が行われます。

- **`$result = $stmt->get_result()`**  
  → 検索された結果セットをオブジェクトとして取得します。

- **`$article = $result->fetch_assoc()`**  
  → 結果セットから1行取り出し、**連想配列**として `$article` に格納します。  
　  つまり `$article['title']` や `$article['content']` のように使えるようになります。

```php
if (!$article) {
    exit("<div class='container mt-5'><p class='alert alert-danger'>記事が見つかりません。</p>
          <p><a href='index.php' class='btn btn-primary'><i class='bi bi-arrow-left'></i> 記事一覧へ戻る</a></p></div>");
}
```
→ `$article` は `$result->fetch_assoc()` によって取得された記事データの「連想配列」です。  
この値が空（＝null または false）だった場合は、該当する記事がデータベース上に存在しないということを意味します。

つまり、`SELECT` 文を実行した結果が **0件（該当なし）**だったとき、この `if` 文が true になり、  
`exit()` によって処理を途中で終了します。

`exit()` の中には、Bootstrapで装飾されたHTMLが書かれており、  
ユーザーには「記事が見つかりません」というメッセージと「戻る」ボタンが表示されます。

```php
<form action="update.php" method="post">
  <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
```
→ 編集対象の記事IDを `update.php` にPOSTで渡すための隠し入力欄。

```html
<div class="mb-3">
    <label for="title" class="form-label">タイトル:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" class="form-control" required>
</div>
```
→ `$article['title']` には、編集対象の記事のタイトルが格納されています。  
この値を `<input>` フィールドの `value` 属性に挿入することで、**フォーム表示時にすでに入力済みのような状態**にできます。

`htmlspecialchars()` を使うことで、万が一タイトル内に `<` や `"` などのタグや記号が含まれていても、  
HTMLとして解釈されず安全に表示されます。これは **XSS（クロスサイトスクリプティング）対策**として必須の処理です。

```html
<div class="mb-3">
    <label for="content" class="form-label">本文:</label>
    <textarea id="content" name="content" rows="5" class="form-control" required><?php echo htmlspecialchars($article['content']); ?></textarea>
</div>
```
→ 本文のデータ `$article['content']` を `<textarea>` 要素の中に直接埋め込んで表示しています。  
`textarea` は `<input>` と違い、`value=""` ではなく、**タグの中に値を挿入する形式**です。

ここでも `htmlspecialchars()` を使用しており、本文中のHTMLタグや記号がそのまま表示され、  
悪意あるスクリプトの実行を防ぐ安全な出力になっています。

また、`required` 属性があるため、空のままでは送信できず、未入力チェックが自動で行われます。

📌 **スキルデモはこちら → [http://news-portfolio.rf.gd/post_form.html]**
