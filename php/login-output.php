<?php session_start(); ?>
<?php require 'header.php'; ?>

<?php
unset($_SESSION['users']); // ユーザーがすでにログインしている場合はログアウトする

$pdo = new PDO(
    // ローカル用
    // 'mysql:host=localhost;dbname=bousai;charset=utf8',
    // 'member',
    // 'password1234'

    // サーバー用
    'mysql:host=localhost;dbname=xslive230801_matumotosu;charset=utf8',
    'xslive230801_mas',
    'livebusiness'
);

// ユーザーID（login_id）に基づいてユーザー情報を取得するクエリ
$sql = $pdo->prepare('SELECT * FROM users WHERE login_id = ?');
$sql->execute([$_REQUEST['login_id']]);
$user = $sql->fetch(PDO::FETCH_ASSOC); // ユーザー情報を1行取得


// ユーザーが存在し、パスワードが一致するか確認
if ($user && password_verify($_REQUEST['password'], $user['password'])) {
    // パスワードが一致した場合、セッションにユーザー情報を保存
    $_SESSION['users'] = [
        'id' => $user['id'],
        'user_name' => $user['user_name'],
        'login_id' => $user['login_id'],
        'birthday' => $user['birthday'],
        'sex' => $user['sex'],
        'childcare' => $user['childcare']
    ];

    // ログイン成功時、ホームページにリダイレクト
    header('Location: home.php');
    exit();
} else {
    // ユーザーが存在しないか、パスワードが一致しない場合
    echo <<<END
    <div class="wrapper">
    <header>
      <h3>ログインIDまたはパスワードが違います。</h3>
    </header>

    <section>
     <div class="return_btns">
        <button type="button" onclick="location.href='login-input.php'">戻る</button>
        </div>
    </section>
    </div>
END;
}
?>

<?php require 'footer.php'; ?>
