<?php session_start(); ?>
<?php require 'header.php'; ?>
<div class="wrapper">
<header>
<h2>会員登録情報確認・変更</h2>
</header>
<?php
$pdo = NEW PDO(
		// ローカル用
	// 'mysql:host=localhost;dbname=bousai;charset=utf8',
	// 'member',
	// 'password1234'

	// サーバー用
	'mysql:host=localhost;dbname=xslive230801_matumotosu;charset=utf8',
	'xslive230801_mas',
	'livebusiness'
);
// ログイン中のユーザーに絞る
$sql = $pdo->prepare( 'SELECT * FROM users WHERE id=?');

$sql->execute([$_SESSION['users']['id']]);

$result = $sql->fetchAll(PDO::FETCH_ASSOC);//FETCH_ASSOC カラム名のみ

// 変数の初期化
$id = $user_name = $login_id = $password = $birthday = $sex = $childcare = '';

// DBにデータが存在する場合、各変数にデータをセット

if (count($result) > 0) {
    foreach ($result as $row) {
        $id = $row['id'];
        $user_name = $row['user_name'];
        $login_id = $row['login_id'];
        $password = $row['password'];
        $birthday = $row['birthday'];
        $sex = $row['sex'];
        $childcare = $row['childcare'];
    }
} else {
    echo '<p>ログイン情報が正しくありません。</p>';
}
// 性別と育児中の値をチェック済みにするための処理
// $変数名 = $sex == '1' ? 'trueの時の処理' : 'falseの時の処理';
$male_checked = $sex == '1' ? 'checked' : '';
$female_checked = $sex == '2' ? 'checked' : '';
$no_answer_checked = $sex == '9' ? 'checked' : '';

$childcare_yes_checked = $childcare == '1' ? 'checked' : '';
$childcare_no_checked = $childcare == '0' ? 'checked' : '';

// セキュリティのため htmlspecialchars でエスケープ処理
$secName = htmlspecialchars($user_name, ENT_QUOTES);
$secLoginID = htmlspecialchars($login_id, ENT_QUOTES);




echo <<<END
<section>
<form action="reg-change-output.php" method="post" onsubmit="return checkPassword()">
    <table class="reg_table">
        <tr>
            <td>ユーザー名</td>
            <td><input type="text" name="user_name" value="$secName" pattern=".{1,10}" title="1〜10文字以内で入力してください" required></td>
        </tr>
        <tr>
            <td>ログインID</td>
            <td><input type="text" name="login_id" value="$secLoginID"  pattern="[a-zA-Z0-9]{1,20}" title="1〜20文字以内の半角英数字で入力してください"  required></td>
        </tr>
        <tr>
            <td>パスワード</td>
            <td><input type="password" name="password" id="password" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,20}"
                 title="大文字・小文字・数字を含む8〜20文字のパスワードを入力してください"required></td>
        </tr>
        <tr>
            <td>パスワード（確認）</td>
            <td><input type="password" name="password_confirm" id="password_confirm" title="確認用パスワードはパスワードと一致する必要があります" required></td>
        </tr>
        <tr>
            <td>生年月日</td>
            <td><input type="date" name="birthday" value="$birthday" required></td>
        </tr>
        <tr>
            <td>性別</td>
            <td class="radio_btn">
                <label><input type="radio" name="sex" value="1" $male_checked>男性</label>
                <label><input type="radio" name="sex" value="2" $female_checked>女性</label>
                <label><input type="radio" name="sex" value="9" $no_answer_checked>回答しない</label>
            </td>
        </tr>
        <tr>
            <td>育児中</td>
            <td class="radio_btn">
                <label><input type="radio" name="childcare" value="1" $childcare_yes_checked>はい</label>
                <label><input type="radio" name="childcare" value="0" $childcare_no_checked>いいえ</label>
            </td>
        </tr>
    </table>
    <div class="return_btns">
      <button type="button" onclick="location.href='home.php'">戻る</button>
       <button type="submit">登録</button>
    </div>
    
</form>
</section>
</div>
END;
?>
<!-- パスワードチェックの処理  -->
<script>
     function checkPassword() {
         const password = document.getElementById('password').value;
         const password_confirm = document.getElementById('password_confirm').value;

         // パスワードが一致しない場合、アラートを表示して送信をキャンセル
         if (password !== password_confirm) {
             alert('パスワードが一致しません。もう一度確認してください。');
             return false;  // フォーム送信をキャンセル
         }
         return true;  // フォーム送信を許可
     }

     // Enterキーを無効化する
     document.addEventListener('DOMContentLoaded', function () {
         var form = document.getElementById('registrationForm');

         form.addEventListener('keydown', function (event) {
             if (event.key === 'Enter') {
                 event.preventDefault(); // Enterキーによるデフォルトのsubmit動作をキャンセル
             }
         });
     });
 </script>


<?php require 'footer.php'; ?>
