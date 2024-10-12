<?php session_start() ?>
<?php require 'header.php' ?>
<?php
if (isset($_SESSION['users'])) {
    unset($_SESSION['users']);
    header('Location: login-input.php');
    exit();
} else {
    echo <<<END
    <div class="wrapper">
    <header>
      <h3>すでにログアウトしています</h3>
    </header>
    
    <section>
      <div class="return_btns">
        <button type="button" onclick="location.href='login-input.php'">ログイン画面へ</button>
      </div>
    </section>
    </div>
END;
}
?>
<?php require 'footer.php' ?>