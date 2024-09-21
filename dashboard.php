<?php
function countTags() {
    $tag_counts = [];
    if (($file = fopen("inquiries.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($file)) !== FALSE) {
            $tags = explode(",", $data[4]); // タグの位置
            foreach ($tags as $tag) {
                if ($tag !== "") {
                    if (!isset($tag_counts[$tag])) $tag_counts[$tag] = 0;
                    $tag_counts[$tag]++;
                }
            }
        }
        fclose($file);
    }
    return $tag_counts;
}

$tag_counts = countTags();

// $inquiriesの初期化
$inquiries = [];
if (file_exists("inquiries.csv")) {
    if (($file = fopen("inquiries.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($file)) !== FALSE) {
            $inquiries[] = $data;
        }
        fclose($file);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php
    include("inc/menu.html");
    ?>
    <section class="section">
      <div class="container">
        <h1 class="title">ダッシュボード</h1>
        <div class="columns">
            <div class="column">
                <h2 class="subtitle">タグ別件数</h2>
                <canvas id="barChart"></canvas>
            </div>
            <div class="column">
                <h2 class="subtitle">分布</h2>
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <h1 class="title">お問い合わせ一覧</h1>
        <table class="table is-striped is-fullwidth">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名前</th>
                    <th>メール</th>
                    <th>メッセージ</th>
                    <th>タグ</th>
                    <th>連絡方法</th>
                    <th>日時</th>
                    <th>ファイル名</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inquiries)): ?>
                    <tr>
                        <td colspan="8">データがありません。</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inquiry[0]); ?></td>
                            <td><?php echo htmlspecialchars($inquiry[1]); ?></td>
                            <td><?php echo htmlspecialchars($inquiry[2]); ?></td>
                            <td><?php echo htmlspecialchars($inquiry[3]); ?></td>
                            <td><?php echo htmlspecialchars($inquiry[4]); ?></td>
                            <td><?php echo htmlspecialchars($inquiry[5]); ?></td>
                            <td><?php echo htmlspecialchars($inquiry[7]); ?></td>
                            <td>
                                <?php if (!empty($inquiry[6])): ?>
                                    <img src="upload/<?php echo htmlspecialchars($inquiry[6]); ?>" alt="uploaded image" style="max-width: 100px; height: auto;">
                                <?php else: ?>
                                    ・・・
                                <?php endif; ?>
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
                    label: 'お問い合わせ数',
                    data: [<?php echo implode(",", array_values($tag_counts)); ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // 円グラフ
        var ctxPie = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: [<?php echo '"' . implode('","', array_keys($tag_counts)) . '"'; ?>],
                datasets: [{
                    label: 'お問い合わせ数',
                    data: [<?php echo implode(",", array_values($tag_counts)); ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'お問い合わせのタグ分布'
                    }
                }
            }
        });
    </script>
</body>
</html>
