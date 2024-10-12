<?php require 'header.php' ?>
<div class="wrapper">
<header>
  <h1>防災準備サポートアプリ</h1>
</header>
<section>
  <form action="login-output.php" method="post">
    ログインID<input type="text" name="login_id" autocomplete="off"><br>
    パスワード<input type="password" name="password" autocomplete="off"><br>
    <button  type="submit">ログイン</button>
<!-- autocomplete="off ブラウザがフォーム入力欄の履歴を自動的に保存・表示するオートコンプリート機能を無効化するため -->

    <p>会員登録がお済みでない方はこちらから<br>
      →<a href="reg-input.php">新規会員登録</a>
    </p>
  </form>
</section>
</div>
<?php require 'footer.php' ?>