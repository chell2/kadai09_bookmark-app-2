<?php
//1.  DB接続
$dsn = 'mysql:dbname=ada02_contact_form;charset=utf8;host=localhost';
$id = 'root';
$password = ''; //Password:MAMP='root',XAMPP=''
try {
  $pdo = new PDO($dsn, $id, $password);
} catch (PDOException $e) {
  exit('DB_CONECT:'.$e->getMessage());
}

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
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
//JSONに値を渡す場合に使う
$json = json_encode($values,JSON_UNESCAPED_UNICODE);

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>フリーアンケート表示</title>
<link rel="stylesheet" href="css/range.css">
<link href="css/bootstrap.min.css" rel="stylesheet">
<style>div{padding: 10px;font-size:16px;}</style>
</head>
<body id="main">
<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
      <a class="navbar-brand" href="index.php">データ登録</a>
      </div>
    </div>
  </nav>
</header>
<!-- Head[End] -->


<!-- Main[Start] -->
<div>
    <div class="container jumbotron">
      <?PHP foreach($values as $value){ ?>
        <p><?=$value["id"]?></p>
        <p><?=$value["name"]?></p>
        <p><?=$value["email"]?></p>
        <p><?=$value["message"]?></p>
        <p><?=$value["tags"]?></p>
      <?PHP } ?>
    
    </div>
</div>
<!-- Main[End] -->


<script>
  //JSON受け取り
        $a = <?=$json?>
        const obj = JSON.parse
        console.log(obj) 


</script>
</body>
</html>
