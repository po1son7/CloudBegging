<?php
require './config.php';
require './functions.php';

// 跨域
//header('Access-Control-Allow-Origin:*');  
//header('Access-Control-Allow-Methods:POST, GET');  
//header('Access-Control-Allow-Headers:x-requested-with,content-type'); 

if(!isset($_POST['name']) || !isset($_POST['mail']) || !is_numeric($_POST['amount']) || !isset($_POST['type']))
{
    exit('bad request');
}

$name = trim($_POST['name']);
$mail = trim($_POST['mail']);
$amount = $_POST['amount'];
$type = $_POST['type'];

if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

if ($name == '' || $mail == '') {
    exit(json_encode(array(
        'ok' => false,
        'msg' => '请填写名称与邮箱',
    )));
}

$ua = $_SERVER['HTTP_USER_AGENT'];

$tradeno = 'CloudBegging-'.strtoupper(hash('crc32b', random_bytes(10)));

file_put_contents("./data/$tradeno.json", json_encode(array(
    'name' => $name,
    'mail' => $mail,
    'amount' => $amount,
    'ua' => $ua,
    'ip' => $ip,
    'paid' => false,
    'pay_time' => 0
), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

$rsp = CreateOrder(
    $tradeno,
    $amount*100,
    $config['base_url'].'/notify.php',
    $config['base_url'].'/thanks.html',
    $type
);

if($rsp->code != 0) exit(json_encode(array(
    'ok' => false,
    'msg' => $rsp->msg,
)));

exit(json_encode(array(
    'ok' => true,
    'data' => $rsp->data,
)));