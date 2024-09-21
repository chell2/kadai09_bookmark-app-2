<?php
function countTags() {
    $tag_counts = [];
    if (($file = fopen("inquiries.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($file)) !== FALSE) {
            $tags = explode(",", $data[4]); // タグの位置
            // print_r($tags); 
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
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タグ別集計</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <h1 class="title mt-5">タグ別お問い合わせ数</h1>
    <div class="box">
        <canvas id="tagChart" class="my-4"></canvas>
        <canvas id="tagPieChart" class="my-4"></canvas>
    </div>
</div>
  <script>
    // バーグラフ
    var ctxBar = document.getElementById('tagChart').getContext('2d');
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
    var ctxPie = document.getElementById('tagPieChart').getContext('2d');
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
