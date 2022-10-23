<?php

// DB接続　PDO
function insertContact($request){
require 'db_connection.php';

// 入力 DB保存　prepare, execute(配列(全て文字列))


$params = [
  'id' => null,
  'your_name' => $request['your_name'],
  'email' => $request['email'],
  'url' => $request['url'],
  'gender' => $request['gender'],
  'age' => $request['age'],
  'contact' => $request['contact'],
  'created_at' => null
];

//$params = [
  //'id' => null,
  //'your_name' => 'hoge',
  //'email' => 'hoge@mail.com',
  //'url' => 'http://hoge.com',
  //'gender' => '2',
  //'age' => '20',
  //'contact' => '本文',
  //'created_at' => null
//];

$count = 0;
$columns = '';
$values = '';

$hoge = array_keys($params);
// ['id', 'your_name', 'email', 'url', 'gender', 'contact', 'created_at'];

foreach(array_keys($params) as $key){
  if($count++>0){
    $columns .= ',';
    $values .=',';
  }
  $columns .= $key;
  // 'id, your_name, email, url, gender, contact, created_at'
  $values .= ':'.$key;
  // ':id, :your_name, :email, :url, :gender, :contact, :created_at'
}

$sql ="INSERT INTO contacts (${columns}) VALUES (${values})";
/**
 * INSERT INTO contacts
 * (id, your_name, email, url, gender, contact, created_at)
 * VALUES
 * (:id, :your_name, :email, :url, :gender, :contact, :created_at);
 */

$stmt = $pdo->prepare($sql);//プリペアードステートメント
$stmt->execute($params); //実行
echo '保存成功';
}