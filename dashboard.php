<?php
//1.  DB接続
include("funcs.php");
$pdo=db_conn();

//２．データ登録SQL作成
$sql = 'SELECT * FROM inquiries';
$stmt = $pdo->prepare($sql);
$status = $stmt->execute(); //true or false

//３．データ表示
$view="";
if($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC);

// タグの件数を集計
$tag_counts = [];
foreach($values as $value) {
    if (!empty($value['tags'])) {
        $tags = explode(",", $value['tags']);
        foreach ($tags as $tag) {
            $trimmed_tag = trim($tag);
            if (isset($tag_counts[$trimmed_tag])) {
                $tag_counts[$trimmed_tag]++;
            } else {
                $tag_counts[$trimmed_tag] = 1;
            }
        }
    }
}

// JSONに値を渡す場合に使う
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    <section class="hero is-info">
      <p class="title has-text-centered">ダッシュボード</p>
    </section>
    <section>
      <div class="chart-container block mt-5">
        <div class="columns is-8">
          <div class="column">
              <h2 class="subtitle">お問い合わせ件数</h2>
              <div class="is-align-self-flex-end">
              <canvas id="barChart" style="width: 100%;"></canvas>
              </div>
          </div>
          <div class="column is-4">
              <h2 class="subtitle">タグ分布図</h2>
              <canvas id="pieChart" style="width: 100%;"></canvas>
          </div>
        </div>
      </div>
    </section>
    <section>
      <div class="chart-container block mt-5">
        <h1 class="title">お問い合わせ一覧</h1>
        <table class="table is-striped is-fullwidth">
          <thead>
            <tr>
              <th>ID</th>
              <th>記録者</th>
              <th>社名</th>
              <th>担当者名</th>
              <th>電話</th>
              <th>メール</th>
              <th>元請会社名</th>
              <th>内容</th>
              <th>タグ</th>
              <th>画像</th>
              <th>問合せ日時</th>
              <th>問合せ方法</th>
              <th></th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($values)): ?>
              <tr>
                  <td colspan="8">データがありません。</td>
              </tr>
            <?php else: ?>
            <?PHP foreach($values as $value): ?>
              <tr>
                <td class="small-font"><?=$value["id"]?></td>
                <td class="small-font"><?=h($value["user_id"])?></td>
                <td class="small-font"><?=h($value["company_name"])?></td>
                <td class="small-font"><?=h($value["contact_name"])?></td>
                <td class="small-font"><?=h($value["phone"])?></td>
                <td class="small-font"><?=h($value["email"])?></td>
                <td class="small-font"><?=h($value["prime_contractor"])?></td>
                <td class="small-font"><?=h($value["inquiry_content"])?></td>
                <td>
                  <?php if (!empty($value["tags"])): ?>
                  <?php
                    $tags = explode(",", $value["tags"]);
                          foreach ($tags as $tag) {
                            echo '<span class="tag is-primary">' . htmlspecialchars(trim($tag)) . '</span><br>';
                          }; ?>
                  <?php else: ?>
                      ・・・
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($value["file_name"])): ?>
                      <img src="upload/<?php echo htmlspecialchars($value["file_name"]); ?>" alt="uploaded image" style="max-width: 100px; height: auto;">
                  <?php else: ?>
                      ・・・
                  <?php endif; ?>
                </td>
                <td class="small-font"><?=h($value["inquiry_datetime"])?></td>
                <td class="small-font"><?=h($value["contact_method"])?></td>
                <td>
                  <a href="detail.php?id=<?=$value["id"]?>"><i class="fas fa-pencil-alt"></i></a>
                </td>
                <td>
                  <a href="#" onclick="confirmDelete(<?= $value['id'] ?>)"><i class="fas fa-trash-alt delete-icon"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <script>
      // バーグラフ
      var ctxBar = document.getElementById('barChart').getContext('2d');
      var barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: [<?php echo '"' . implode('","', array_keys($tag_counts)) . '"'; ?>],
          datasets: [{
              label: '',
              data: [<?php echo implode(",", array_values($tag_counts)); ?>],
                  backgroundColor: [
                      'rgba(255, 99, 132, 0.2)',
                      'rgba(54, 162, 235, 0.2)',
                      'rgba(255, 206, 86, 0.2)',
                      'rgba(75, 192, 192, 0.2)',
                      'rgba(153, 102, 255, 0.2)',
                      'rgba(255, 150, 64, 0.2)' ,
                      'rgba(75, 200, 90, 0.2)',
                  ],
                  borderColor: [
                      'rgba(255, 99, 132, 1)',
                      'rgba(54, 162, 235, 1)',
                      'rgba(255, 206, 86, 1)',
                      'rgba(75, 192, 192, 1)',
                      'rgba(153, 102, 255, 1)',
                      'rgba(255, 150, 64, 1)',
                      'rgba(75, 200, 90, 1)',
                  ],
              borderWidth: 1
          }]
        },
        options: {
          scales: {
              y: { beginAtZero: true }
          },
          plugins: {
              legend: { display: false }
          },
        }
      });

      // 円グラフ
      var ctxPie = document.getElementById('pieChart').getContext('2d');
      var pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: [<?php echo '"' . implode('","', array_keys($tag_counts)) . '"'; ?>],
            datasets: [{
                label: '',
                data: [<?php echo implode(",", array_values($tag_counts)); ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 150, 64, 0.2)' ,
                    'rgba(75, 200, 90, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 150, 64, 1)',
                    'rgba(75, 200, 90, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false,
                    text: ''
                }
            }
        }
      });
      
      // 削除確認ダイアログ
      function confirmDelete(id) {
        if (confirm("本当に削除しますか？")) {
            // OKで削除処理へ
            window.location.href = "delete.php?id=" + id;
        }
        // キャンセルは閉じるのみ
      }
    </script>
  </body>
</html>
