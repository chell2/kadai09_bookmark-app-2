<?php
//1.  DB接続
include("funcs.php");
$pdo=db_conn();

// タグの一覧取得
$stmt = $pdo->query('SELECT * FROM tags');
$tags = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ記録（データ更新）</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    <section class="hero is-info">
      <p class="title has-text-centered mobile-hidden">
          お問い合わせ記録 タグ管理
      </p>
      <p class="title has-text-centered mobile-visible">
          お問い合わせ記録<br>タグ管理
      </p>
    </section>
    <section class="section">
      <div class="container">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title">タグの追加</p>
            <div class="card-header-icon">
              <span class="icon">
                <i class="fas fa-solid fa-truck-pickup has-text-info"></i>
              </span>
            </div>
          </header>
          <div class="card-content">
            <div class="form-container">
              <form id="tag_nameForm" action="insert_tags.php" method="POST">
                <div class="columns">
                  <div class="column">
                    <div class="field">
                      <label class="label" for="tag_name">新しいタグ名
                      <div class="control has-icons-left">
                        <input class="input" type="text" id="tag_name" name="tag_name" required>
                        <span class="icon is-small is-left has-text-info">
                          <i class="fas fa-tag"></i>
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
    <section class="section">
      <div class="chart-container block mt-5">
        <h1 class="title">タグ一覧</h1>
        <table class="table is-striped is-fullwidth">
          <thead>
            <tr>
              <th>ID</th>
              <th>タグ名</th>
              <th></th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($tags)): ?>
              <tr>
                  <td colspan="8">データがありません。</td>
              </tr>
            <?php else: ?>
            <?php foreach ($tags as $tag): ?>
              <tr>
                <td><?= $tag['id'] ?></td>
                <td><?= htmlspecialchars($tag['tag_name']) ?></td>
                <td>
                  <a href="detail_tags.php?id=<?=$tag["id"]?>"><i class="fas fa-pencil-alt"></i></a>
                </td>
                <td>
                  <a href="#" onclick="confirmDelete(<?= $tag['id'] ?>)"><i class="fas fa-trash-alt delete-icon"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
    <script>
      // 削除確認ダイアログ
      function confirmDelete(id) {
        if (confirm("本当に削除しますか？")) {
            // OKで削除処理へ
            window.location.href = "delete_tags.php?id=" + id;
        }
        // キャンセルは閉じるのみ
      }
    </script>
  </body>
</html>