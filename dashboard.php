<?php
session_start();
include "funcs.php";
sschk();

//1.  DB接続
$pdo=db_conn();

// 検索キーワードの取得
$search = isset($_GET['search']) ? $_GET['search'] : '';

// SQLの準備 (検索条件を追加)
$sql = 'SELECT inquiries.*, users.user_name 
        FROM inquiries LEFT JOIN users ON inquiries.user_id = users.id 
        WHERE 
        company_name LIKE :search OR 
        contact_name LIKE :search OR 
        phone LIKE :search OR 
        email LIKE :search OR 
        prime_contractor LIKE :search OR 
        inquiry_content LIKE :search OR 
        users.user_name LIKE :search'; // 記録者名の検索
        
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);

$status = $stmt->execute(); //true or false

// データ表示
$view="";
if($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}

// 全データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>トイアワセキロク</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    <section>
      <div class="chart-container">
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
      <div class="list-container">
        <h1 class="title">お問い合わせ一覧</h1>
        <div style="overflow-x: auto;">
          <table id="inquiries_table" class="table is-striped is-fullwidth" style="table-layout: fixed; min-width: 1000px; width: 100%;">
            <thead>
              <tr>
                <th data-idx="0" class="sortable" style="width:  3%;">ID</th>
                <th data-idx="1" class="sortable" style="width:  6%;">記録</th>
                <th data-idx="2" class="sortable" style="width: 10%;">社名</th>
                <th data-idx="3" class="sortable" style="width:  6%;">担当</th>
                <th data-idx="4" data-no-sort="true" style="width:  8%;">電話</th>
                <th data-idx="5" data-no-sort="true" style="width:  8%;">メール</th>
                <th data-idx="6" class="sortable" style="width:  8%;">元請</th>
                <th data-idx="7" data-no-sort="true" style="width: 18%;">内容</th>
                <th data-idx="8" data-no-sort="true" style="width: 4%;"></th>
                <th data-idx="9" class="sortable" style="width: 10%;">タグ</th>
                <th data-idx="10" class="sortable" style="width:  8%;">日時</th>
                <th data-idx="11" class="sortable" style="width:  6%;">方法</th>
                <th data-idx="12" data-no-sort="true" style="width:  3%;"></th>
                <th data-idx="13" data-no-sort="true" style="width:  3%;"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($values)): ?>
                <tr>
                    <td colspan="8">データがありません。</td>
                </tr>
              <?php else: ?>
              <?PHP foreach($values as $value): ?>
              <?php
                // 各行の user_id を使ってユーザー名を取得
                $stmt_user = $pdo->prepare("SELECT user_name FROM users WHERE id=:user_id");
                $stmt_user->bindValue(":user_id", $value["user_id"], PDO::PARAM_INT);
                $status_user = $stmt_user->execute();
                
                if ($status_user == false) {
                    sql_error($stmt_user);
                    $user_name = "-"; // エラー時
                } else {
                    $user_row = $stmt_user->fetch();
                    $user_name = $user_row ? h($user_row['user_name']) : "-";
                }
              ?>
                <tr>
                  <td class="small-font"><?=$value["id"]?></td>
                  <td class="small-font"><?=$user_name?></td>
                  <td class="small-font"><?=h($value["company_name"])?></td>
                  <td class="small-font"><?=h($value["contact_name"])?></td>
                  <td class="small-font" style="word-break:break-all;"><?=h($value["phone"])?></td>
                  <td class="small-font" style="word-break:break-all;"><?=h($value["email"])?></td>
                  <td class="small-font"><?=h($value["prime_contractor"])?></td>
                  <td class="small-font"><?=h($value["inquiry_content"])?></td>
                  <td>
                    <?php if (!empty($value["file_name"])): ?>
                      <i class="fas fa-regular fa-images has-text-grey"></i>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($value["tags"])): ?>
                    <?php
                      $tags = explode(",", $value["tags"]);
                            foreach ($tags as $tag) {
                              echo '<span class="tag is-primary">' . h(trim($tag)) . '</span><br>';
                            }; ?>
                    <?php else : ?>
                    　-
                    <?php endif; ?>
                  </td>
                  <td class="small-font"><?=date('Y/m/d H:i', strtotime(h($value["inquiry_datetime"])))?></td>
                  <td class="small-font"><?=h($value["contact_method"])?></td>
                  <td>
                    <a href="detail.php?id=<?=$value["id"]?>"><i class="fas fa-pencil-alt edit-icon"></i></a>
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
      
      // リストの並び替え
      document.addEventListener('DOMContentLoaded', () => {
        const getCellValue = (row, idx) => row.cells[idx].innerText;

        const comparer = (idx, asc) => (a, b) =>
          ((v1, v2) =>
            v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2)
              ? v1 - v2
              : v1.toString().localeCompare(v2))(
            getCellValue(asc ? a : b, idx),
            getCellValue(asc ? b : a, idx)
          );

        document.querySelectorAll('#inquiries_table th').forEach((th) =>
          th.addEventListener('click', () => {
            // 並び替え禁止の列をチェック
            if (th.dataset.noSort === "true") return;

            const table = th.closest('table');
            const tbody = table.querySelector('tbody');
            const idx = Array.from(th.parentNode.children).indexOf(th);
            const asc = th.classList.toggle('asc', !th.classList.contains('asc'));

            Array.from(tbody.querySelectorAll('tr'))
              .sort(comparer(idx, asc))
              .forEach((tr) => tbody.appendChild(tr));
          })
        );
      });
    </script>
  </body>
</html>
