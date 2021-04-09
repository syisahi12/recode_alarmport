<?php
/**
 *  Created by Wahyu Arif P (warifp)
 *  07 April 2021
 * 
 * https://www.linkedin.com/in/warifp/
 */

require __DIR__ . '/vendor/autoload.php';
include 'config.php';

use Curl\Curl;

// init curl
$curl = new Curl();
$temp = "0";

function telegramConnect($curl, $telegramToken, $telegramChatIdAsk, $telegramChatId) {
    if ($telegramChatId != null){
    foreach($telegramChatId as $chat) {
    $curl->get('https://api.telegram.org/bot' . $telegramToken . '/sendMessage?chat_id=' . $chat . '&text=Package Portal alarm is connected.');
    return $curl->response->ok;
    }
    } else {
    $curl->get('https://api.telegram.org/bot' . $telegramToken . '/sendMessage?chat_id=' . $telegramChatIdAsk . '&text=Package Portal alarm is connected.');
    return $curl->response->ok;
    }
}

if(telegramConnect($curl, $telegramToken, $telegramChatIdAsk, $telegramChatId)) {
    echo "Package Portal alarm is connected.\n";
} else {
    echo "Failed to connect your Telegram bot.\n";
    exit;
}

start:
$list = file_get_contents($inputList);
$datas = explode("\n", str_replace("\r", "", $list));

foreach($telegramChatId as $chat) {
$curl->get('https://api.telegram.org/bot' . $telegramToken . '/getUpdates?chat_id=' . $chat. '&offset=-1');
//var_dump($curl->response);
if($curl->response->result[0]->update_id != $temp){
//    $curl->get('https://api.telegram.org/bot' . $telegramToken . '/getUpdates?chat_id=' . $telegramChatId . '&offset=-1');
    $temp = $curl->response->result[0]->update_id;
    $text = $curl->response->result[0]->message->text;
    $telegramChatIdAsk = $curl->response->result[0]->message->from->id;
    if($text == "cek"){
        telegramConnect($curl, $telegramToken,$telegramChatIdAsk, $telegramChatIdAsk);
        echo "Package Portal alarm is connected.\n";
    }else{
        printf ('Ketik "cek"');
        echo "\n";
    }
}
}

for ($i = 0; $i < count($datas); $i++) {

    $address = $datas[$i];

    $curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36');
    $curl->setHeader('Origin', 'https://viewblock.io');
    $curl->get('https://api.viewblock.io/zilliqa/addresses/' . $address . '?network=mainnet&page=1');

    
    if ($curl->error) {
        echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
    } else {
        if (isset($curl->response->tokens->zil18f5rlhqz9vndw4w8p60d0n7vg3n9sqvta7n6t2)) {
            if ($i <= 6) {
                $curl->get('https://api.telegram.org/bot' . $telegramToken . '/sendMessage?chat_id=' . $telegramChatId[0] . '&text=Hei, address : ' . $address . ' landing!');   
            } else {
                $curl->get('https://api.telegram.org/bot' . $telegramToken . '/sendMessage?chat_id=' . $telegramChatId[1] . '&text=Hei, address : ' . $address . ' landing!');  
            }
            }
        }
    }
sleep(10);
goto start;