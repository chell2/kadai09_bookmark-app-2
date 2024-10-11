<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ログイン</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-centered">
          <div class="column is-4-desktop is-6-tablet">
            <div class="box">
              <h1 class="title has-text-centered">ログイン</h1>
              <form name="form1" action="login_act.php" method="post">
                <div class="field">
                  <label class="label">ID</label>
                  <div class="control">
                    <input class="input" type="text" name="user_email" placeholder="メールアドレスを入力">
                  </div>
                </div>
                <div class="field">
                  <label class="label">PW</label>
                  <div class="control">
                    <input class="input" type="password" name="user_pw" placeholder="パスワードを入力">
                  </div>
                </div>
                <div class="field">
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
</body>
</html>