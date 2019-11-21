<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public static function push_notif($fcm_token, $title, $message){
        define('API_ACCESS_KEY','AAAAjrvLI_4:APA91bGI_urQhVNWgEMEReiqUG8Jz3o8pXX55T69mDGv9KW-BwphHdsk4E74UUkx4kb3XqUfA_QMu_QjWAJw3PLg2eovQtqD2hCfJhHFMdxfptKlvP0ZTW6hC9XgB06KBmuvi45LU9nA'); //Server Key on SERVER

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title'    => $title,
            'body'     => $message,
            'icon'     => 'stock_ticker_update',
            'color'    => '#23e60d',
            'sound'    => 'default',
            'priority' => 'high'
        ];

        $fcmNotification = [
            'to'            => $fcm_token, //single token
            'notification'  => $notification
        ];

        $headers = [
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $result;
        }
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
}
