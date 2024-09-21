<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>お問い合わせフォーム</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php
    include("inc/menu.html");
    ?>
  <section class="section">
    <div class="container">
      <h1 class="title">お問い合わせフォーム</h1>
      <form action="contact.php" method="POST"  enctype="multipart/form-data">
        <div class="field">
          <label class="label" for="name">名前</label>
          <div class="control">
            <input class="input" type="text" id="name" name="name" required>
          </div>
        </div>
        <div class="field">
          <label class="label" for="email">メールアドレス</label>
          <div class="control">
            <input class="input" type="email" id="email" name="email" required>
          </div>
        </div>
        <div class="field">
          <label class="label" for="message">問い合わせ内容</label>
          <div class="control">
            <textarea class="textarea" id="message" name="message" rows="5" required></textarea>
          </div>
        </div>
        <div class="field">
          <label class="label">スクリーンショットなど</label>
            <div class="control">
              <input type="file" name="image[]" accept="upload/*" multiple>
            </div>
        </div>
        <div class="field">
          <label class="label">連絡方法</label>
          <div class="control">
            <label class="radio">
              <input type="radio" name="contact_method" value="email" checked> メール
            </label>
            <label class="radio">
              <input type="radio" name="contact_method" value="zoom"> Zoom
            </label>
          </div>
        </div>
        <div class="control">
          <button type="submit" class="button is-primary">送信</button>
        </div>
      </form>
    </div>
  </section>
</body>
</html>
