<?php session_start(); ?>
<?php require 'header.php' ?>
<?php
echo <<<END
<div class="wrapper">
<header>
    
    <h3>こんにちは、<span style="color: blue;">{$_SESSION['users']['user_name']}</span>さん!</h3>
<h1>防災準備サポートアプリ</h1>
    </header>
END;
?>
<section>
    <div id="home_btns">
        <button onclick="location.href='backpack.php'">防災グッズリスト</button>
        <br>
        <button onclick="location.href='reg-change-input.php'">会員登録情報確認・変更</button>
        <br>
        <button onclick="location.href='logout-input.php'">ログアウト</button>
    </div>
</section>
</div>
<?php require 'footer.php' ?>