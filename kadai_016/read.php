<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = 'root';

try{
    $pdo = new PDO($dsn, $user, $password);

    // orderパラメータの値が存在すれば並び替えボタンを押した時、その値を変数$orderに代入
    if(isset($_GET['order'])){
        $order = $_GET['order'];
    }else{
        $order = NULL;
    }

    // keywordパラメータの値が存在すれば(商品名を検索した時)、その値を変数$keywordに代入
    if(isset($_GET['keyword'])){
        $keyword = $_GET['keyword'];
    }else{
        $keyword = NULL;
    }

    // orderのパラメータの値によってSQL文を変更
    if($order === 'desc'){
        $sql_select = 'SELECT * FROM books WHERE book_name LIKE :keyword ORDER BY updated_at DESC';
    }else{
        $sql_select = 'SELECT * FROM books WHERE book_name LIKE :keyword ORDER BY updated_at ASC';
    }

    // SQL文を用意する
    $stmt_select = $pdo->prepare($sql_select);

    // SQLのLIKE句を使うため、変数$keywordの前後を%で囲む
    $partial_match = "%{$keyword}%";
    // partial match 部分一致という意味

    // bindValue()メソッドを使って値をプレースホルダに割り当てる
    $stmt_select->bindValue(':keyword', $partial_match, PDO::PARAM_STR);

    // SQL文を実行する
    $stmt_select->execute();

    // SQL文の実行結果を配列で取得
    $books = $stmt_select->fetchALL(PDO::FETCH_ASSOC);
}catch (PDOException $e){
    exit($e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>書籍一覧</title>
        <link rel="stylesheet" href="css/style.css?2025.05.04">
    </head>
    <body>
        <header>
            <nav>
                <a href="index.php">書籍管理アプリ</a>
            </nav>
        </header>
        <main>
            <article class="products">
                <h1>書籍一覧</h1>
                <?php
                // messageパラメータ(商品の登録・編集・削除後)の値を受け取っていれば、それを表示する
                if(isset($_GET['message'])){
                    echo "<p class='success'>{$_GET['message']}</p>";
                }
                ?>
                <div class="products-ui">
                    <div>
                        <!-- 並び替えと検索ボックス -->
                        <a href="read.php?order=desc&keyword=<?= $keyword ?>">
                            <!-- 検索ソートした状態でも昇順降順が反映される様にするために上記の様な記述にする -->
                            <img src="images/desc.png" alt="降順に並び替え" class="sort-img">
                        </a>
                        <a href="read.php?order=asc&keyword=<?= $keyword ?>">
                            <img src="images/asc.png" alt="昇順に並び替え" class="sort-img">
                        </a>
                        <form action="read.php" method="get" class="search-form">
                            <input type="hidden" name="order" value="<?= $order ?>">
                            <!-- 隠しデータとして$orderの値を送信し、検索ソートされた状態を維持する -->
                            <!-- 「フォームに入力させる必要はないけど、この値を送信したい」というときに便利 -->
                            <input type="text" class="search-box" placeholder="書籍名で検索" name="keyword" value="<?= $keyword ?>">
                            <!-- form要素内にinput要素が1つの時は送信ボタンを作成しなくてもエンターキーを押せば入力内容を送信できる -->
                        </form>
                    </div>
                    <a href="create.php" class="btn">書籍登録</a>
                </div>
                <table class="products-table">
                    <tr>
                        <th>書籍コード</th>
                        <th>書籍名</th>
                        <th>単価</th>
                        <th>在庫数</th>
                        <th>ジャンルコード</th>
                        <th>編集</th>
                        <th>削除</th>
                    </tr>
                    <?php
                    // 配列の中身を順番に取り出し表形式で出力
                    foreach($books as $book){
                        $table_row = "
                        <tr>
                        <td>{$book['book_code']}</td>
                        <td>{$book['book_name']}</td>
                        <td>{$book['price']}</td>
                        <td>{$book['stock_quantity']}</td>
                        <td>{$book['genre_code']}</td>
                        <td><a href='update.php?id={$book['id']}'><img src='images/edit.png' alt='編集' class='edit-icon'></a></td>
                        <td><a href='delete.php?id={$book['id']}'><img src='images/delete.png' alt='削除' class='delete-icon'></a></td>
                        </tr>
                        ";
                        echo $table_row;
                    }
                    ?>
                </table>
            </article>
        </main>
        <footer>
            <p class="copyright">&copy; 書籍管理アプリ ALL rights reserved.</p>
        </footer>
    </body>
</html>
