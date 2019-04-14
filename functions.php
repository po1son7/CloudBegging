<?php

function CreateOrder($order,$money,$notify_url,$return_url,$pay_type)
{
    require './config.php';
    $parameter = array(
        'appId' => $config['app_id'],
        'appSecret' => $config['app_secret'],
        'merchantTradeNo' => $order,
        'totalFee' => $money,
        'notifyUrl' => $notify_url,
        'returnUrl' => $return_url,
        'payType' => $pay_type
    );
    $parameter['sign'] = Sign($parameter);

    $parameter = http_build_query($parameter);

    $request = HttpPost('https://api.trimepay.com/gateway/pay/go', $parameter);
    if($request === false) return false;
    else return json_decode($request); 
}

function HttpPost($url,$d)
{
    $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $d);
	$res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function Sign($parameter)
{
    require './config.php';
    ksort($parameter);
    $parameter = http_build_query($parameter);
    $signature = strtolower(md5(md5($parameter).$config['app_secret']));
    return $signature;
}
