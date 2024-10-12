<?php
session_start();
include("funcs.php");
sschk();

//1.  DB接続
//select.phpのPHPコードをマルっとコピーしてきます。
//※SQLとデータ取得の箇所を修正します。

// 表示されないときのエラーチェック
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$id = $_GET["id"]; //?id~**を受け取る
$pdo = db_conn();

//２．データ登録SQL作成
$stmt = $pdo->prepare("SELECT * FROM inquiries WHERE id=:id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

//３．データ表示
if($status==false) {
    sql_error($stmt);
  }else{
    $row = $stmt->fetch();
  }

//2-2. usersテーブルから記録者名を取得
$stmt_user = $pdo->prepare("SELECT user_name, life_flg FROM users WHERE id=:user_id");
$stmt_user->bindValue(":user_id", $row["user_id"], PDO::PARAM_INT);
$status_user = $stmt_user->execute();

//3-2．データ表示
if ($status_user == false) {
    sql_error($stmt_user);
  } else {
      $user_row = $stmt_user->fetch();
  }
?>


<!--
4．HTML
以下にindex.phpのHTMLをまるっと貼り付ける！
理由：入力項目は「登録/更新」はほぼ同じになるからです。
※form要素 input type="hidden" name="id" を１項目追加（非表示項目）
※form要素 action="update.php"に変更
※input要素 value="ここに変数埋め込み"
-->

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ記録（編集フォーム）</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    <!-- <section class="hero is-info">
        <p class="title has-text-centered mobile-hidden">
          お問い合わせ記録 編集フォーム
        </p>
        <p class="title has-text-centered mobile-visible">
          お問い合わせ記録<br>編集フォーム
        </p>
    </section> -->
    <section class="section">
      <div class="container">
        <div class="card">
        <div class="card-content">
          <div class="form-container">
            <form id="inquiryForm" action="update.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
              <!-- 1行目 -->
              <div class="columns">
                <div class="column is-one-thirds">
                  <div class="field">
                    <label class="label">元請会社</label>
                    <div class="control">
                      <div class="select">
                        <select name="prime_contractor">
                          <option value="D建設" selected>D建設</option>
                          <option value="S工業">S工業</option>
                          <option value="K工務店">K工務店</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="column is-one-thirds">
                  <div class="field">
                    <label class="label">問い合わせ方法</label>
                    <div class="control">
                      <div class="select">
                        <select name="contact_method">
                          <option value="電話" selected>電話</option>
                          <option value="メール">メール</option>
                          <option value="その他">その他</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="column is-one-thirds">
                  <div class="field">
                    <label class="label" for="user_id">記録者</label>
                    <div class="control is-flex is-align-items-center ml-2" style="padding-top: 6px;">
                      <input type="hidden" id="user_id" name="user_id" value="<?=$row["user_id"]?>" required>
                      <span class="icon is-small is-left <?= $user_row['life_flg'] == 1 ? 'has-text-grey' : 'has-text-primary' ?>">
                        <i class="fas fa-user"></i>
                      </span>
                      <span class="ml-3"><?=$user_row['user_name']?></span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- 2行目 -->
              <div class="columns">
                <div class="column is-two-thirds">
                  <div class="field">
                    <label class="label" for="company_name">社名 <span class="has-text-danger">*</span></label>
                    <div class="control has-icons-left">
                      <input class="input" type="text" id="company_name" name="company_name" value="<?=$row["company_name"]?>" required>
                      <span class="icon is-small is-left has-text-info">
                        <i class="fas fa-building"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="column is-one-third">
                  <div class="field">
                    <label class="label" for="contact_name">担当者</label>
                    <div class="control has-icons-left">
                      <input class="input" type="text" id="contact_name" name="contact_name" value="<?=$row["contact_name"]?>" required>
                      <span class="icon is-small is-left has-text-info">
                        <i class="fas fa-user"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- 3行目 -->
              <div class="field">
                <label class="label">連絡先</label>
                <div class="columns">
                  <div class="column is-one-third">
                    <div class="control has-icons-left">
                      <input class="input" type="tel" id="phone" name="phone" placeholder="電話番号" value="<?=$row["phone"]?>">
                      <span class="icon is-small is-left has-text-info">
                        <i class="fas fa-phone"></i>
                      </span>
                    </div>
                  </div>
                  <div class="column is-two-thirds">
                    <div class="control has-icons-left">
                      <input class="input" type="email" id="email" name="email" placeholder="メールアドレス" value="<?=$row["email"]?>">
                      <span class="icon is-small is-left has-text-info">
                        <i class="fas fa-envelope"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- 4行目 -->
              <div class="field">
                <label class="label" for="inquiry_content">問い合わせ内容 <span class="has-text-danger">*</span></label>
                <div class="control">
                  <textarea class="textarea" id="inquiry_content" name="inquiry_content" rows="5" required><?=$row["inquiry_content"]?></textarea>
                </div>
              </div>
              <div class="field">
                <!-- <label class="label" for="tags">タグ</label> -->
                <?php if (!empty($row["tags"])): ?>
                <?php
                  $tags = explode(",", $row["tags"]);
                        foreach ($tags as $tag) {
                          echo '<span class="tag is-primary">' . h(trim($tag)) . '</span> ';
                        }; ?>
                <?php endif; ?>
              </div>
              <!-- 5行目 -->
              <div class="field">
                <label class="label">スクリーンショットなど（あれば）</label>
                <div class="control">
                  <input type="file" name="image[]" accept="image/*" multiple>
                </div>
              </div>
              <div class="field">
                  <?php if (!empty($row["file_name"])): ?>
                      <img src="upload/<?php echo h($row["file_name"]); ?>" alt="uploaded image" style="max-width: 100px; height: auto;">
                  <?php endif; ?>
              </div>
              <!-- 7行目 -->
              <div class="field">
                <label class="label" for="inquiry_datetime">問い合わせ日時 <span class="has-text-danger">*</span></label>
                <div class="control">
                  <input class="input" type="datetime-local" id="inquiry_datetime" name="inquiry_datetime" value="<?=$row["inquiry_datetime"]?>" required>
                </div>
              </div>
              <!-- ボタン -->
              <div class="columns">
                <div class="column is-two-thirds">
                  <div class="control">
                    <button type="submit" class="button is-info is-fullwidth">更新</button>
                    <input type="hidden" name="id" value="<?=$id?>">
                  </div>
                </div>
                <div class="column is-one-third">
                  <div class="control">
                    <div class="columns">
                      <div class="column is-half">
                        <button type="button" class="button is-info is-outlined is-fullwidth" onclick="clearForm()">編集をクリア</button>
                      </div>
                      <div class="column is-half">
                          <button type="button" class="button is-info is-outlined is-fullwidth" onclick="window.location.href='dashboard.php'">キャンセル</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          </div>
        </div>
      </div>
    </section>

    <script>
      function validateForm() {
        const userIdInput = document.getElementById('user_id');
        const companyNameInput = document.getElementById('company_name');
        const phoneInput = document.getElementById('phone');
        const emailInput = document.getElementById('email');
        const inquiryContentInput = document.getElementById('inquiry_content');
            
        let valid = true;

        // 各フィールドのバリデーション
        [userIdInput, companyNameInput, inquiryContentInput].forEach(input => {
          if (input.value.trim() === "") {
            input.classList.add('is-danger');
            valid = false;
          } else {
            input.classList.remove('is-danger');
          }
        });

        // メールアドレスのバリデーション
        if (emailInput.value.trim() !== "") {
          const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
          if (!emailPattern.test(emailInput.value)) {
            emailInput.classList.add('is-danger');
            valid = false;
          } else {
            emailInput.classList.remove('is-danger');
          }
        }

        // 電話番号のバリデーション
        if (phoneInput && phoneInput.value.trim() !== "") {
          const phonePattern = /^[0-9]{10,11}$/; // 10〜11桁の数字
          if (!phonePattern.test(phoneInput.value)) {
            phoneInput.classList.add('is-danger');
            valid = false;
          } else {
            phoneInput.classList.remove('is-danger');
          }
        }

        return valid; // フォームの送信
      }

      function clearForm() {
        // フォームの要素をリセット
        document.getElementById('inquiryForm').reset();
        
        // すべての入力フィールドからエラースタイルを削除
        const inputs = document.querySelectorAll('#inquiryForm .input, #inquiryForm .textarea');
        inputs.forEach(input => {
          input.classList.remove('is-danger');
        });
      }
    </script>
  </body>
</html>