<?php
require './config.php';
require './functions.php';

if (!isset(
    $_POST['payStatus'],
    $_POST['payFee'],
    $_POST['callbackTradeNo'],
    $_POST['payType'],
    $_POST['merchantTradeNo'],
    $_POST['sign']
)) exit('bad request');

$sign = Sign(array(
    'payStatus' => $_POST['payStatus'],
    'payFee' => $_POST['payFee'],
    'callbackTradeNo' => $_POST['callbackTradeNo'],
    'payType' => $_POST['payType'],
    'merchantTradeNo' => $_POST['merchantTradeNo']
));
if($_POST['sign'] != $sign) exit('invalid notify');

$tradeno = $_POST['merchantTradeNo'];

$trade = file_get_contents("./data/$tradeno.json");
if (!$trade) {
    exit('invalid trade');
}

$trade = json_decode($trade);

if ($config['enable_bot']) {
    $data = array(
        'chat_id' => $config['tg_chatid'],
        'text' => "[云讨饭]\r\n*$trade->name* 刚给咱捐了 *$trade->amount* 元呢（",
        'disable_web_page_preview' => true,
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode(array(
            'inline_keyboard' => array(array(array(
                'text' => '我也要投喂',
                'url' => $config['base_url']
            )))
        ))
    );
    $data = http_build_query($data);
    $request = HttpPost('https://api.telegram.org/bot'.$config['bot_token'].'/sendMessage', $data);
}

$trade->paid = 1;
$trade->pay_time = time();
file_put_contents("./data/$tradeno.json", json_encode($trade, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

exit('SUCCESS');
