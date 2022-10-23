<?php









// echo '<pre>';
// var_dump($result);
// echo '</pre>';

//ユーザー入力あり　prepare, bind, execute 悪意ユーザdelete * SQL
$sql ='select * from contacts where id = :id'; //名前付きプレース
$stmt = $pdo->prepare($sql);//プリペアードステートメント
$stmt->bindValue('id', 5, PDO::PARAM_INT);//紐付け
$stmt->execute(); //実行

$result = $stmt->fetchall();

echo '<pre>';
var_dump($result);
echo '</pre>';
