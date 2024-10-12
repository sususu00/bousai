<?php session_start(); ?>
<?php require 'header.php' ?>

<?php
// 現在日付
$now = date('Ymd');
// 誕生日
$birthday = $_SESSION['users']['birthday'];
$birthday = str_replace("-", "", $birthday); //str_replace関数を用いて、誕生日の文字列からハイフン(-)を取り除く処理を行っています。
// // // 年齢
$age = floor(($now - $birthday) / 10000); //年齢を$ageに代入
$userPattern = 0;  // 初期化 (1~8のパターン) 文字列なら空　数値なら0がいい

if ($age >= 65) {
    // 65歳以上の処理
    if (($_SESSION['users']['sex'] == 1 || $_SESSION['users']['sex'] == 9) && $_SESSION['users']['childcare'] == 1) {
        $userPattern = 4;
    } elseif (($_SESSION['users']['sex'] == 1 || $_SESSION['users']['sex'] == 9) && $_SESSION['users']['childcare'] == 0) {
        $userPattern = 3;
    } elseif ($_SESSION['users']['sex'] == 2 && $_SESSION['users']['childcare'] == 1) {
        $userPattern = 8;
    } elseif ($_SESSION['users']['sex'] == 2 && $_SESSION['users']['childcare'] == 0) {
        $userPattern = 7;
    }
} else {
    // 65歳未満の処理
    if (($_SESSION['users']['sex'] == 1 || $_SESSION['users']['sex'] == 9) && $_SESSION['users']['childcare'] == 1) {
        $userPattern = 2;
    } elseif (($_SESSION['users']['sex'] == 1 || $_SESSION['users']['sex'] == 9) && $_SESSION['users']['childcare'] == 0) {
        $userPattern = 1;
    } elseif ($_SESSION['users']['sex'] == 2 && $_SESSION['users']['childcare'] == 1) {
        $userPattern = 6;
    } elseif ($_SESSION['users']['sex'] == 2 && $_SESSION['users']['childcare'] == 0) {
        $userPattern = 5;
    }
}



// ●データベース接続
$pdo = new PDO(
    // ローカル用
    // 'mysql:host=localhost;dbname=bousai;charset=utf8',
    // 'member',
    // 'password1234'

    // // サーバー用
    'mysql:host=localhost;dbname=xslive230801_matumotosu;charset=utf8',
    'xslive230801_mas',
    'livebusiness'
);

//　⚫︎ログインユーザーのパターンと一致するtarget_id,t_backpackのユーザーのIDと一致する　データ取得
// データベースから取得したアイテムごとにループ処理を行う PDOクラスに用意されたqueryメソッドを呼び出している　メソッドを呼び出すには「->」 queryメソッドは引数に指定したSQL文をデータベースに対して実行する
//外部結合・・・ 結合すべき行が見つからなくても諦めず、全ての値がNULLである行を生成 (LEFT JOIN、RIGHT JOIN, FULL JOIN)
//テーブル名.カラム名　as つけたい名前でPHP上で指定分けできるようにする 
//配列の中身を区別するために各テーブルのカラムIDは名前をasで変更している
$sql = $pdo->prepare(
    'SELECT  
    t_backpack.item_id ,     m_backpack.item_name,     m_backpack.required_count, m_backpack.unit_name,  m_backpack.time_limit_flag,
    m_category.id AS category_id, m_category.category_name, t_backpack.time_limit,     t_backpack.remarks
     FROM t_backpack
     LEFT JOIN m_backpack
     ON t_backpack.item_id = m_backpack.id
     LEFT JOIN m_category
     ON m_backpack.id = m_category.item_id AND t_backpack.category_id = m_category.id
     LEFT JOIN m_display_pattern
     ON  m_backpack.id = m_display_pattern.item_id
     WHERE m_display_pattern.target_id = ? AND t_backpack.user_id = ?
     ORDER BY t_backpack.id,m_category.id'
);
$sql->execute([$userPattern, $_SESSION['users']['id']]);

$result = $sql->fetchAll(PDO::FETCH_ASSOC);
// 取得したデータ（PDOオブジェクト）を配列に変換　fetchALL()だとカラム名と連番　fetchAll(PDO::FETCH_ASSOC)だとカラム名のみ取得



//⚫︎t_backpackにユーザーのデータがないとき　（登録していない人の処理）
if (empty($result)) {
    $sql = $pdo->prepare(
        'SELECT  
            m_backpack.id AS item_id ,    m_backpack.item_name,    m_backpack.required_count, m_backpack.unit_name, m_backpack.time_limit_flag,
            m_category.id AS category_id, m_category.category_name
            FROM m_backpack
            LEFT JOIN m_category
            ON m_backpack.id = m_category.item_id
            LEFT JOIN m_display_pattern
            ON  m_backpack.id = m_display_pattern.item_id
            WHERE m_display_pattern.target_id = ?
            ORDER BY m_backpack.id,m_category.id'

    );
   
    $sql->execute([$userPattern]);

    $result = $sql->fetchAll(PDO::FETCH_ASSOC);

}

// ●期限切れアイテムがあるかどうかのチェック
$expiredItems = [];
foreach ($result as $row) {
    if (!empty($row['time_limit']) && $row['time_limit_flag'] == 1) { //time_limitの値があり（null排除）、かつ期限があるもの
        $timeLimit = str_replace('-', '', $row['time_limit']); // 期限日をYYYYMMDD形式に
        if ($timeLimit < $now) { // 現在の日付より前であれば期限切れ
            $expiredItems[] = $row['item_name'].$row['category_name']; // 期限切れアイテム名を配列に保存
        }
    }
}

// 期限切れのアイテムがある場合、アラートを表示
if (!empty($expiredItems)) {
    echo '<script>';
    echo 'alert("以下のアイテムの期限が切れています:\n' . implode('\n', $expiredItems) . '");';
    echo '</script>';
}



// ●テーブルの表示
echo <<<END
 <div class="wrapper">
  <header id="backpack_header">
        <h2><span style="color: blue;">{$_SESSION['users']['user_name']}</span>さんの防災グッズリスト</h2>
  </header>
<section>
  <form action="backpack.php" method="post">
     <table id="backpack-table">
       <tr>
        <th>アイテム</th>
        <th>内容</th>
        <th>必要量<br>目安</th>
        <th>期限</th>
        <th>備考</th>
       </tr>
END;



// フォームが送信された場合、データベースのt_backpackテーブルの(user_id,item_id,category_id,time_limit,remarks)に登録する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     //  まず、t_backpackテーブルから現在のユーザーのデータを削除
     $sql = $pdo->prepare('DELETE FROM t_backpack WHERE user_id = ?');
     $sql->execute([$_SESSION['users']['id']]);
 
     // フォームデータを挿入
    $postData = $_POST;
    $i = 1;
    $success = true; // 登録完了成功フラグ


    while ($i < $_SESSION['counter']) {
        $item_id = $postData['item_id' . $i];
        $category_id = $postData['category_id' . $i];
        $time_limit = $postData['time_limit' . $i];
        $remarks = $postData['remarks' . $i];
       
        // 日付が未入力ならNULLに変換
        if (empty($time_limit)) {
            $time_limit = null; // 空の場合はNULLをセット
        }

      
        // 新しいデータをデータベースに挿入
        $sql = $pdo->prepare('INSERT INTO t_backpack(user_id, item_id, category_id,time_limit, remarks) VALUES (?, ?, ?, ?, ?)');
        if (!$sql->execute([$_SESSION['users']['id'], $item_id, $category_id, $time_limit, $remarks])) {
            $success = false; // もし登録に失敗したらフラグをfalseに
        }
       
        // $sql->execute([
        //     $_SESSION['users']['id'],
        //     $item_id,
        //     $category_id,
        //     $time_limit,
        //     $remarks
        // ]);
        $i++;
    }
    // セッション変数をクリア
    unset($_SESSION['counter']);

      // 成功・失敗の結果をセッションに保存
     if($success === true) {
        $_SESSION['alert_message'] = '登録が完了しました。';
    } else {
        $_SESSION['alert_message'] = '登録に失敗しました。再度お試しください。';
    }

    // ページを再読み込み
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// ページ再読み込み後にアラートを表示
if (isset($_SESSION['alert_message'])) {
    echo '<script>alert("' . $_SESSION['alert_message'] . '");</script>';
    unset($_SESSION['alert_message']); // メッセージを一度表示したらセッションから削除
}



// ●防災リュック登録画面の表示処理
// 取得したデータで重複するitem_nameの数をカウント
$countArray = []; //空の配列を用意
$currentItemName = $result[0]['item_name']; //SQLの取得した配列の1番目＝水
$categoryCount= 0; //カウントに0を代入
foreach ($result as $row) {

    if ($currentItemName != $row['item_name']) {       //アイテムネームが一致しないとき
        $countArray[] = $categoryCount;                //配列にカウント数を入れる
        $currentItemName = $row['item_name'];          //$currentItemNameの中身をアイテムネームに書き換える
        $categoryCount = 0;                            //カウントを０にリセットしないとアイテムが変わっているのに前回のアイテムのカウントの続きからになるから
    }
    $categoryCount++;                                  //1足す
}
$countArray[] = $categoryCount;                        //ループを抜けたカウント値を配列に代入
                                                       //                             [水]　  値　[非常食]　値
                                                       //$countArray[]の中身は　Array ( [0] => 1   [1] => 　5 etc...）









$countIndex = 0; //$countArray[]の配列の中身のindex番号として使う
$loopCount = 0;
$rowCounter = 1; //inputタグのname属性カウントで区別するための変数

 // ⚫︎データベースから取得してきたアイテムのデータをテーブルに出力
foreach ($result as $row) {
    echo '<tr>';
    if ($loopCount == 0) { //1回目のループは必ずtrueになる
        echo '<td rowspan="' . $countArray[$countIndex] . '">' . $row['item_name'] . '</td>';  //ここでテーブルの１列目作ってる
    }
    $loopCount++;//1足すのでループ抜ける

    if ($countArray[$countIndex] <= $loopCount) {//$loopCountがアイテムの内容の数以上の時
        $loopCount = 0; // $loopCountを０にする
        $countIndex++;  //$countIndexに1足す=次ループするとき＋１されたインデックス番号のアイテムの配列に指定されて処理をする
    }

    echo '<td>' . $row['category_name'] . '</td>';
    echo '<td>' . $row['required_count'] . $row['unit_name'] . '</td>';
    echo '<td>';
    if ($row['time_limit_flag'] == 1) {
        if (empty($row['time_limit'])) {
            echo '<input type="date" name="time_limit' . $rowCounter . '">';
        } else {
            echo '<input type="date" name="time_limit' . $rowCounter . '" value=' . htmlspecialchars($row['time_limit'], ENT_QUOTES) . '>';
        }
    } elseif (($row['time_limit_flag'] == 0)) {
        echo '<input type="hidden" name="time_limit' . $rowCounter . '">';
    }

    echo '</td>';
    if (empty($row['remarks'])) {
        echo '<td><input type="text" name="remarks' . $rowCounter . '"></td>';
    } else {
        echo '<td><input type="text" name="remarks' . $rowCounter . '" value=' . htmlspecialchars($row['remarks'], ENT_QUOTES) . '></td>';
    }

    echo '</tr>';
    echo '<input type="hidden" name="item_id' . $rowCounter . '" value="' . $row['item_id'] . '">';
    echo '<input type="hidden" name="category_id' . $rowCounter . '" value="' . $row['category_id'] . '">';


    $rowCounter++;
   
}
$_SESSION['counter'] = $rowCounter ;

// テーブルの終了部分を出力
echo <<<END
</table>
<div class="return_btns">
    <button  type="button" onclick="location.href='home.php'">戻る</button>
    <button type="submit">登録</button>
  </form>
  </section>
</div>
END;



?>

<?php require 'footer.php' ?>