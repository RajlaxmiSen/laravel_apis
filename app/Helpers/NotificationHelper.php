<?php

namespace App\Helpers;
use App\Models\FcmToken;
use Log;
use Config;

class NotificationHelper
{

    public static $notification_type = [
        1  => 'Friend request send', 
        2  => 'You have a new message', 
    ];

    public static $push_notification_type = [0 => 'Friend request send', 1 => 'You have a new message'];

    public static function send($token, $type, $title, $message)
    {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key =Config::get('custom_setting.SERVER_KEY');

        Log::info($server_key);

        $fields          = array();
        $data            = array();
        $data['type']    = $type;
        $data['title']   = $title;
        $data['message'] = $message;
        $data['sound']   = 'default';
        $fields['data']  = $data;

        if (is_array($token)) {
            $token=array_unique($token);
            $fields['registration_ids'] = $token;
        } else {
            $fields['to'] = $token;
        }

        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key,
        );

        $notification                 = array();
        $notification['click_action'] = $type;
        $notification['title']        = $title;
        $notification['body']         = $message;
        $notification['sound']        = 'default';
        $fields['notification'] = $notification;
        $result['fields']=$fields;
        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result['response'] = curl_exec($ch);
        Log::info($result);

        curl_close($ch);
        return $result;

    }

}

