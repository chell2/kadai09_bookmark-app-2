<?php
session_start();

// 1. DB接続
include("funcs.php");
sschk();
$pdo = db_conn();

// ユーザー情報の取得（管理者と一般ユーザーで取得内容を切り替え）
if ($_SESSION['is_admin'] == 1) {
    // 管理者の場合、全ユーザー情報を取得
    $stmt = $pdo->query('SELECT * FROM users');
} else {
    // 一般の場合、自分の情報だけ取得
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ記録（ユーザー管理）</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
    <?php include("inc/menu.html"); ?>

    <!-- 管理者のみ表示 -->
    <?php if ($_SESSION['is_admin'] == 1): ?>
    <section class="section">
      <div class="container">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title">アカウントの新規作成</p>
            <div class="card-header-icon">
              <span class="icon">
                <i class="fas fa-truck-pickup has-text-info"></i>
              </span>
            </div>
          </header>
          <div class="card-content">
            <div class="form-container">
              <form id="tag_nameForm" action="insert_users.php" method="POST">
                <!-- 1行目 -->
                <div class="columns is-align-items-center">
                  <div class="column is-one-third">
                    <div class="field">
                      <div class="control has-icons-left">
                        <input class="input" type="text" id="user_name" name="user_name" placeholder="ユーザー名" required>
                        <span class="icon is-small is-left has-text-info">
                          <i class="fas fa-user"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="column is-two-thirds">
                    <div class="field">
                      <div class="control"><b>種別：</b>
                        <label class="radio">
                          <input type="radio" name="is_admin" value="0" checked> 一般
                        </label>　
                        <label class="radio">
                          <input type="radio" name="is_admin" value="1"> 管理者
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- 2行目 -->
                <div class="columns">
                  <div class="column">
                    <div class="field">
                      <div class="control has-icons-left">
                        <input class="input" type="email" id="user_email" name="user_email" placeholder="メールアドレス" required>
                        <span class="icon is-small is-left has-text-info">
                          <i class="fas fa-envelope"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="column">
                    <div class="field">
                      <div class="control has-icons-left">
                        <input class="input" type="password" id="user_pw" name="user_pw" placeholder="パスワード" required>
                        <span class="icon is-small is-left has-text-info">
                          <i class="fas fa-key"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="column">
                    <div class="control">
                      <button type="submit" class="button is-primary">追加</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section class="section">
      <div class="list-container block mt-5">
        <h1 class="title">
          <!-- 管理者と一般で表示切り替え -->
          <?php if ($_SESSION['is_admin'] == 1): ?>
            ユーザー一覧
          <?php else: ?>
            ユーザー情報
          <?php endif; ?>
        </h1>
        <table class="table is-striped is-fullwidth">
          <thead>
            <tr>
              <th>ID</th>
              <th>名前</th>
              <th>メールアドレス</th>
              <th>種別</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="8">データがありません。</td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['user_name']) ?></td>
                <td><?= htmlspecialchars($user['user_email']) ?></td>
                <td><?= htmlspecialchars($user['is_admin'] == 1 ? '管理者' : '一般') ?></td>
                <td>
                  <?php if ($user['life_flg'] == 1): ?>
                    <i class="fas fa-ban has-text-danger"></i>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="detail_users.php?id=<?= htmlspecialchars($user['id']) ?>"><i class="fas fa-pencil-alt"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </body>
</html>