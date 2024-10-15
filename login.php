<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ログイン</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .password-control {
      position: relative;
    }
    .password-control .eye-icon {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
    }
  </style>
</head>
<body>
  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-4-desktop is-6-tablet">
            <div class="box">
              <img src="./assets/logo.png" alt="ロゴ" class="logo">
              <form name="form1" action="login_act.php" method="post">
                <div class="field mt-4">
                  <label class="label"></label>
                  <div class="control has-icons-left">
                    <input class="input" type="text" name="user_email" placeholder="メールアドレス">
                    <span class="icon is-small is-left">
                      <i class="fas fa-envelope has-text-info"></i>
                    </span>
                  </div>
                </div>
                <div class="field password-control mt-4">
                  <label class="label"></label>
                  <div class="control has-icons-left">
                    <input class="input" type="password" name="user_pw" id="login_pw" placeholder="パスワード">
                    <span class="icon is-small is-left">
                      <i class="fas fa-key has-text-info"></i>
                    </span>
                    <i id="eyeIcon_l" class="fas fa-eye-slash eye-icon has-text-grey" style="cursor: pointer;" onclick="togglePasswordVisibility('login_pw','eyeIcon_l')"></i>
                  </div>
                </div>
                <div class="field mt-4">
                  <div class="control">
                    <button class="button is-primary is-fullwidth">ログイン</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="./assets/script.js"></script>
</body>
</html>