<?php
session_start();
include("funcs.php");
sschk();

//1.  DB接続
$pdo=db_conn();

// タグの一覧取得
$stmt = $pdo->query('SELECT * FROM prime_contractors');
$prime_contractors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ記録（元請管理）</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    
    <?php if ($_SESSION['is_admin'] == 1): ?>
    <section class="section">
      <div class="container">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title">元請会社の追加</p>
            <div class="card-header-icon">
              <span class="icon">
                <i class="fas fa-solid fa-truck-pickup has-text-info"></i>
              </span>
            </div>
          </header>
          <div class="card-content">
            <div class="form-container">
              <form id="pc_nameForm" action="insert_prime_contractors.php" method="POST">
                <div class="columns">
                  <div class="column">
                    <div class="field">
                      <label class="label" for="name">新しい元請会社の名称
                      <div class="control has-icons-left">
                        <input class="input" type="text" id="pc_name" name="name" required>
                        <span class="icon is-small is-left has-text-info">
                          <i class="fas fa-building"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="column">
                    <div class="control">
                      <br>
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
      <div class="chart-container block mt-5">
        <h1 class="title">元請会社一覧</h1>
        <table class="table is-striped is-fullwidth">
          <thead>
            <tr>
              <th>ID</th>
              <th>会社の名称</th>
              <th></th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($prime_contractors)): ?>
              <tr>
                  <td colspan="8">データがありません。</td>
              </tr>
            <?php else: ?>
            <?php foreach ($prime_contractors as $prime_contractor): ?>
              <tr>
                <td><?= $prime_contractor['id'] ?></td>
                <td><?= htmlspecialchars($prime_contractor['name']) ?></td>
                <td>
                  <?php if ($prime_contractor['life_flg'] == 1): ?>
                    <i class="fas fa-ban has-text-danger"></i>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($_SESSION['is_admin'] == 1): ?>
                    <a href="detail_prime_contractors.php?id=<?=$prime_contractor["id"]?>"><i class="fas fa-pencil-alt"></i></a>
                  <?php endif; ?>
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