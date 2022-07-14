<?php
require __DIR__ . '/vendor/autoload.php';
use HNova\Db\db;
use HNova\Db\Pull;

db::connect(dbname: 'test');

// $pull = new Pull();

// echo db::pull()->insert(['t' => '2022-07-17'], 'tes1', 'id')->rows()[0]['id'] . "\n";


// echo db::error()?->getMessage();

db::pull()->update(['name' => 'johan heiler'], ['id=:id', [ 'id'=>1 ]], 'tes1');

$res1 = db::pull()->query("SELECT * FROM test where id=1");

echo json_encode($res1->rows());
echo json_encode($res1->rows());
// echo json_encode($res2->colums());