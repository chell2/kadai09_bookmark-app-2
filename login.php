<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ログイン</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
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
                <div class="field">
                  <label class="label">ID</label>
                  <div class="control">
                    <input class="input" type="text" name="user_email" placeholder="メールアドレス">
                  </div>
                </div>
                <div class="field password-control">
                  <label class="label">PW</label>
                  <div class="control">
                    <input class="input" type="password" name="user_pw" id="password" placeholder="パスワード">
                    <i class="fas fa-eye-slash eye-icon" id="togglePassword" onclick="togglePasswordVisibility()"></i>
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

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('togglePassword');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    }
  </script>
</body>
</html>