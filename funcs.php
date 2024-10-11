<?php
//DB接続用の関数
function db_conn(){
    try {
        function loadEnv($file)
        {
            if (file_exists($file)) {
                $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    // '=' で分割して環境変数を設定
                    list($key, $value) = explode('=', $line, 2);
                    putenv(trim($key) . '=' . trim($value));
                }
            }
        }
        
        // 実行
        loadEnv('.env');

        // 環境変数の取得
        $db_name =  getenv('DB_NAME');
        $db_host =  getenv('DB_HOST');
        $db_id =    getenv('DB_ID');
        $db_pw =    getenv('DB_PW');
        
        $server_info ='mysql:dbname='.$db_name.';charset=utf8;host='.$db_host;
        $pdo = new PDO($server_info, $db_id, $db_pw);
        
        return $pdo;

    } catch (PDOException $e) {
        exit('DB Connection Error:'.$e->getMessage());
    }
}

//SQLエラー
function sql_error($stmt){
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("SQLError:".$error[2]);
}

//リダイレクト
function redirect($file_name){
    header("Location: ".$file_name);
    exit();
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//SessionCheck
function sschk(){
    if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
        // exit("Login Error");
        header("Location: login.php");
        exit();
    }else{
                session_regenerate_id(true);
                $_SESSION["chk_ssid"] = session_id();
            }
}
?>