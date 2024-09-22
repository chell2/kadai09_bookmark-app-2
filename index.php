<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    <section class="hero is-info">
        <!-- 通常表示 -->
        <p class="title has-text-centered mobile-hidden">
          Webシステム お問い合わせフォーム
        </p>
        <!-- モバイルサイズで表示 -->
        <p class="title has-text-centered mobile-visible">
          Webシステム<br>お問い合わせフォーム
        </p>
    </section>
    <section class="section">
      <div class="container">
        <div class="form-container">
          <form action="contact.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="field">
              <label class="label" for="name">名前</label>
              <div class="control has-icons-left">
                <input class="input" type="text" id="name" name="name" required>
                <span class="icon is-small is-left has-text-info">
                  <i class="fas fa-user"></i>
                </span>
              </div>
            </div>
            <div class="field">
              <label class="label" for="email">メールアドレス</label>
              <div class="control has-icons-left">
                <input class="input" type="email" id="email" name="email" required>
                <span class="icon is-small is-left has-text-info">
                  <i class="fas fa-envelope"></i>
                </span>
              </div>
            </div>
            <div class="field">
              <label class="label" for="message">お問い合わせ内容</label>
              <div class="control">
                <textarea class="textarea" id="message" name="message" rows="5" required></textarea>
              </div>
            </div>
            <div class="field">
              <label class="label">スクリーンショットなど</label>
              <div class="control">
                <input type="file" name="image[]" accept="image/*" multiple>
              </div>
            </div>
            <div class="field">
              <label class="label">連絡方法</label>
              <div class="control">
                <div class="select">
                  <select name="contact_method">
                    <option value="email" selected>メール</option>
                    <option value="zoom">Zoom</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="control">
              <button type="submit" class="button is-primary is-fullwidth">送信</button>
            </div>
          </form>
        </div>
      </div>
    </section>

    <script>
      function validateForm() {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const nameHelp = document.getElementById('name-help');
        const emailHelp = document.getElementById('email-help');
        
        let valid = true;

        // ユーザー名のバリデーション
        if (nameInput.value.trim() === "") {
          nameInput.classList.add('is-danger');
          nameHelp.style.display = 'block';
          valid = false;
        } else {
          nameInput.classList.remove('is-danger');
          nameHelp.style.display = 'none';
        }

        // メールのバリデーション
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value)) {
          emailInput.classList.add('is-danger');
          emailHelp.style.display = 'block';
          valid = false;
        } else {
          emailInput.classList.remove('is-danger');
          emailHelp.style.display = 'none';
        }
        
        // メッセージのバリデーション
        if (messageInput.value.trim() === "") {
          messageInput.classList.add('is-danger');
          messageHelp.style.display = 'block';
          valid = false;
        } else {
          messageInput.classList.remove('is-danger');
          messageHelp.style.display = 'none';
        }

        return valid; // フォームの送信
      }
    </script>
  </body>
</html>
