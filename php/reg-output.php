<?php session_start(); ?>
<?php require 'header.php'; ?>
<?php

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

$password = $_POST['password'];
// パスワードのハッシュ化
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if (isset($_SESSION['users'])) {//セッションデータが存在するかisset関数で調べる
	$id = $_SESSION['users']['id']; // セッションからログインIDを取得
	$sql = $pdo->prepare('SELECT * FROM users WHERE id!=? AND login_id=?');  // SELECT文　指定したテーブルの指定した列を取得する　現在のユーザー以外で同じログインIDが存在するかチェックするためのSQL文を準備
	$sql->execute([$id, $_REQUEST['login_id']]); //実行してこの変数を取得する
} else {
	$sql = $pdo->prepare('SELECT * FROM users WHERE login_id=?');
	$sql->execute([$_REQUEST['login_id']]);
}

if (empty($sql->fetchAll())) { //emptyは引数に指定した値が空の時TRUEを返す  fetchallは検索結果を配列で渡す　空なら空の配列を渡す
	if (!isset($_SESSION['users'])) { //セッションデータが存在しない場合　未登録の場合
		$sql = $pdo->prepare('INSERT INTO users VALUES(null,?,?,?,?,?,?)'); //INSERT文　指定したテーブルに新しい行を追加する ?に各会員情報を指定
		$sql->execute([
			$_REQUEST['user_name'],
			$_REQUEST['login_id'],
			$hashed_password,
			$_REQUEST['birthday'],
			$_REQUEST['sex'],
			$_REQUEST['childcare']
		]);
		echo <<<END
		<div class="wrapper">
		<header>
		 <h3>会員情報を登録しました。</h3>
         </header>
		  <section>
		    <div>
		      <button id="login_btn"  onclick="location.href='login-input.php'">ログイン画面へ</button>
		    </div>
		 </section>
		 </div>

END;
	}
} else {
	echo <<<END
	<div class="wrapper">
	<header>
	   <h3>ログインIDがすでに使用されていますので、変更してください。</h3>
	</header>

	<section>
	  <div class="return_btns">
	    <button type="button" onclick="location.href='reg-input.php'">戻る</button>
      </div>
	</section>
	</div>
END;
}


?>
<?php require 'footer.php'; ?>