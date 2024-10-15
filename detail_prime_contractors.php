<?php
session_start();
include("funcs.php");
sschk();

//1.  DB接続
$id = $_GET["id"]; //?id~**を受け取る
$pdo = db_conn();

//２．データ登録SQL作成
$stmt = $pdo->prepare("SELECT * FROM prime_contractors WHERE id=:id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

//３．データ表示
if($status==false) {
    sql_error($stmt);
}else{
    $row = $stmt->fetch();
}

// 元請会社の一覧取得
$stmt = $pdo->query('SELECT * FROM prime_contractors');
$prime_contractors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ記録（元請更新）</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    <section class="section">
      <div class="container">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title">元請会社の編集</p>
            <div class="card-header-icon">
              <span class="icon">
                <i class="fas fa-solid fa-truck-pickup has-text-info"></i>
              </span>
            </div>
          </header>
          <div class="card-content">
            <div class="form-container">
              <form id="pc_nameForm" action="update_prime_contractors.php" method="POST" onsubmit="return confirmUpdate();">
                <div class="columns">
                  <div class="column">
                    <div class="field">
                      <label class="label" for="pc_name">ID: <?=$row["id"]?>
                      <div class="control has-icons-left">
                        <input class="input" type="text" id="pc_name" name="name" value="<?=$row["name"]?>" required>
                        <span class="icon is-small is-left has-text-info">
                          <i class="fas fa-building"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="column">
                    <div class="control">
                      <br>
                      <div class="buttons">
                          <button type="submit" class="button is-primary">更新</button>
                          <input type="hidden" name="id" value="<?=$id?>">
                          <button type="button" class="button is-info is-outlined" onclick="window.location.href='prime_contractors.php'">キャンセル</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="columns">
                  <div class="column">
                    <div class="field">
                      <div class="control"><b>アカウントの削除：</b>
                        <label class="checkbox">
                          <input type="hidden" name="life_flg" value="0" />
                          <input type="checkbox" id="pcLifeFlgCheckbox" name="life_flg" value="1" <?php if ($row["life_flg"] == 1) echo 'checked'; ?> />
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="column">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="chart-container block mt-5">
        <h1 class="title">元請会社一覧</h1>
        <table class="table is-striped is-fullwidth">
          <thead>
            <tr>
              <th>ID</th>
              <th>元請会社の名称</th>
              <th></th>
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
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
    <script>
      function confirmUpdate() {
        const lifeFlgCheckbox = document.getElementById('pcLifeFlgCheckbox');
        if (lifeFlgCheckbox.checked) {
          return confirm('このアカウントを削除しますか？');
        }
        return true;
      }
    </script>
  </body>
</html>