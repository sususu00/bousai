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


if (isset($_SESSION['users'])) {//セッションデータが存在するかisset関数で調べる。セッションデータがある＝すでにログインしているユーザーがいる
	$id=$_SESSION['users']['id'];// 現在ログインしているユーザーのIDを記録します。
	$sql=$pdo->prepare('SELECT * FROM users WHERE id!=? AND login_id=?');  // SELECT文　指定したテーブルの指定した列を取得する　id!=?の条件により、現在のユーザー（ID）が含まれていないことを確認しつつ、同じlogin_idが他のユーザーに存在するかどうかを確認
	$sql->execute([$id, $_REQUEST['login_id']]); //実行して$idと $_REQUEST['login_id']取得する
} else {
	$sql=$pdo->prepare('SELECT * FROM users WHERE login_id=?');//セッションデータがない場合セッションデータに関係なく、送信されたlogin_idが既に存在するかどうかをチェックします。
	$sql->execute([$_REQUEST['login_id']]);
}

// 現在日付
$now = date('Ymd');

// 入力された誕生日
$newBirthday = $_REQUEST['birthday'];
$newBirthday = str_replace("-", "", $newBirthday);

 $regAge = $_SESSION['users']['birthday'];
 $regAge = str_replace("-", "",$regAge );


// // 年齢計算
 $newAge = floor(($now - $newBirthday) / 10000);  // 新しい年齢を計算
 



// データ更新の前に条件を確認する
$deleteData = false;  // データを削除するかどうかを判定

// 性別、育児中の有無、年齢に変更があればフラグを立てる
if (
    $_SESSION['users']['sex'] != $_REQUEST['sex'] ||  // 性別が変更された場合
    $_SESSION['users']['childcare'] != $_REQUEST['childcare'] ||  // 育児中の有無が変更された場合
    ($newAge >= 65 && floor(($now - $regAge) / 10000) < 65)  // 65歳以上に変わった場合
) {
    $deleteData = true;  // データ削除のフラグをオン
}

// データベースから該当するデータを削除する処理
if ($deleteData) {
    // t_backpackテーブルからデータを削除
    $sql = $pdo->prepare('DELETE FROM t_backpack WHERE user_id = ?');
    $sql->execute([$_SESSION['users']['id']]);

    echo "年齢(65歳以上になった)、性別、育児中いずれかの変更がありましたので<br>ユーザーのバックパックデータを削除しました。<br>";

}






if (empty($sql->fetchAll())) { //emptyは引数に指定した値が空の時TRUEを返す  fetchallは検索結果を配列で渡す　空なら空の配列を渡す
	if (isset($_SESSION['users'])) {//セッションデータが存在する場合　
		$sql=$pdo->prepare( //UPDATE文　指定したテーブルの指定した行に関して指定した列の値を更新する
            'UPDATE users 
             SET 
              user_name=?,
              login_id=?, 
			  password=?,
              birthday=?,
              sex=?,
              childcare=?
             WHERE
              id=?'
            );
		$sql->execute([ //inputで飛んできた値を実行 データベースの更新
			$_REQUEST['user_name'], 
            $_REQUEST['login_id'], 
			$hashed_password,
            $_REQUEST['birthday'],
            $_REQUEST['sex'],
            $_REQUEST['childcare'],
            $id
           ]);


		$_SESSION['users'] = [   //配列にキーと値を格納　[ 'キー' => 値 ] して$_SESSION['users']に代入、セッションの更新
			'id'=>$id,
            'user_name'=>$_REQUEST['user_name'], 
			'login_id'=>$_REQUEST['login_id'], 
			'birthday'=>$_REQUEST['birthday'],
            'sex'=>$_REQUEST['sex'],
            'childcare'=>$_REQUEST['childcare']
        ];

		echo <<<END
        <div class="wrapper">
        <header> 
          <h3> 会員情報を更新しました。</h3>
        </header>
        <section>
          <div class="return_btns">
           <button type="button" onclick="location.href='home.php'">HOMEへ</button>
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
	    <button  type="button" onclick="location.href='reg-change-input.php'">戻る</button>
      </div>
    </section>
    </div>
END;
}
?>
<?php require 'footer.php'; ?>


<!-- リュックのテーブル作成一番多いパターン⇒データベース作成⇒PHP -->

