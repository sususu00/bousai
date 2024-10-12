<?php require 'header.php' ?>
<div class="wrapper">
<header>
    <h2>新規会員登録</h2>
</header>
    <section >
        <form action="reg-output.php" method="post"  onsubmit="return checkPassword()">
            <table class="reg_table">
                <tr>
                    <td>ユーザー名</td>
                    <td><input type="text" name="user_name"
                            value="<?= isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name'], ENT_QUOTES) : '' ?>"
                            autocomplete="off" pattern=".{1,10}" title="1〜10文字以内で入力してください" required></td>
                </tr>
                <tr>
                    <td>ログインID</td>
                    <td><input type="text" name="login_id"
                            value="<?= isset($_POST['login_id']) ? htmlspecialchars($_POST['login_id'], ENT_QUOTES) : '' ?>"
                            autocomplete="off" pattern="[a-zA-Z0-9]{1,20}" title="1〜20文字以内の半角英数字で入力してください" required>
                    </td>
                </tr>
                <tr>
                    <td>パスワード</td>
                    <td><input type="password" id="password" name="password" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,20}"
                            title="大文字・小文字・数字を含む8〜20文字のパスワードを入力してください" required></td>
                </tr>
                <tr>
                    <td>パスワード（確認）</td>
                    <td><input type="password" id="password_confirm" name="password_confirm"
                          title="確認用パスワードはパスワードと一致する必要があります" required></td>
                </tr>
                <tr>
                    <td>生年月日</td>
                    <td><input type="date" name="birthday" required></td>
                </tr>
                <tr>
                    <td>性別</td>
                    <td class="radio_btn">
                        <label><input type="radio" name="sex" value="1" required> 男性</label>
                        <label><input type="radio" name="sex" value="2" required> 女性</label>
                        <label><input type="radio" name="sex" value="9" required> 回答しない</label>
                    </td>
                </tr>
                <tr>
                    <td>育児中</td>
                    <td class="radio_btn">
                        <label><input type="radio" name="childcare" value="1" required>はい</label>
                        <label><input type="radio" name="childcare" value="0" required>いいえ</label>
                    </td>
                </tr>
             </table>
            <div class="return_btns">
                <button type="button" onclick="location.href='login-input.php'">戻る</button>
                <button type="submit">登録</button>
            </div>
       
        </form>
    </section>
</div>
   <!-- パスワードチェックの処理 -->
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
            let form = document.getElementById('registrationForm');

            form.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Enterキーによるデフォルトのsubmit動作をキャンセル
                }
            });
        });
    </script>

<?php require 'footer.php' ?>