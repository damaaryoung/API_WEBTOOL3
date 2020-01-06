<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public static function push_notif($fcm_token, $title, $msg)
    {
        define('API_ACCESS_KEY','AAAAt-7q_AI:APA91bH6xE4YaKuoiKoHqBIJY3O3vN9nvwZByWKi8UIoPrleakjmMK2wYg8AkISiuj4zEyiuHn5PjCxV2dV3ZYQfLDhXA7QZVoBCp5v_vbK3SbbpgseuIgb8qhBVzc48dEa8PXjQ_423');

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        // $fcm_token = array( $_GET['id'] );

        $notification = [
            'title'    => $title,
            'body'     => $msg,
            'icon'     => 'stock_ticker_update',
            'color'    => '#23e60d',
            'sound'    => 'default',
            'priority' => 'high'
        ];

        // $notification = array(
        //     'message'   => 'here is a message. message',
        //     'title'     => 'This is a title. title',
        //     'subtitle'  => 'This is a subtitle. subtitle',
        //     'tickerText'=> 'Ticker text here...Ticker text here...Ticker text here',
        //     'vibrate'   => 1,
        //     'sound'     => 1,
        //     'largeIcon' => 'large_icon',
        //     'smallIcon' => 'small_icon',
        //     'priority'  => 'high'
        // );

        $data = [
            'to'            => $fcm_token, //single token
            'notification'  => $notification
        ];

        $headers = [
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $options = array(
            'http' => array(
                'header'  => $headers,
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    public static function sendOTP($hp, $msg_otp)
    {
        $url = 'https://kreditmandiri.co.id/API_SMS/sms.php';

        $data  = array(
            'username' => 'dasjhfsj12EDD',
            'password' => 'uykmshfj126AEE',
            'hp'       => $hp,
            'message'  => $msg_otp
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    public static function img64enc($file) {
        // $file = $req->file('lam_imb');
        $type = pathinfo($file, PATHINFO_EXTENSION);
        $extention = $file->getClientOriginalExtension();
        $data = file_get_contents($file);
        $base64 = 'data:image/' . $extention . ';base64,' . base64_encode($data);

        return $base64;
    }

    public static function group_by($key, $array) {
        $result = array();
        $q = 0;
        foreach($array as $keys =>  $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]]['tgl'] = $val[$key];
                $result[$val[$key]]['list'][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }
}
