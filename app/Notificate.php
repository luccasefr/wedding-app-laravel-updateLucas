<?php

namespace App;

use App\Action;
use Carbon\Carbon;
use App\User;


class Notificate
{   

    

    public static function NotificateUser($fcmToken,$title,$message)
    {
        // $apiKey = env('FCM_API_KEY');
        $apiKey = 'AAAAa3lT0DA:APA91bH4uZ_TVD_3XBCOJh30bg-k_WSS-szYsk9d7EdKX57TGFkra02-t9M8519X45dEs_n6PNDwyUSrjWE1yGMGrKanFBIzcMpJNosDYt12N7cS7IuHEAvfZnwSg-7Vvl1XW3sAbVsvgH4z1rXqdjswWu2E4CGmEw';

        // echo $apiKey."***********";
        // dd($apiKey);

        $client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/json','Authorization'=>'key='.$apiKey]]);
        $res = $client->post( 'https://fcm.googleapis.com/fcm/send',[
            \GuzzleHttp\RequestOptions::JSON =>[
                'to'=>$fcmToken,
                'notification'=>[
                    'title'=>$title,
                    'body'=>$message
                ]
                
            ]
        ]);
        // echo print_r($client,true)."##########";
        // echo print_r($res,true)."-----------";
        // dd($client);
        
        // dd($res->getBody()->getContents());
        
        
    }

    public static function CheckActinosNotifications()
    {
        $actions = Action::all();
        foreach ($actions as $action) {
            echo $action->isBetweenNotifyDate();
            if($action->isBetweenNotifyDate()&& $action->notify_guests==1){
                self::NotifyAllGuests($action->user,$action->title,$action->message);
            }
        }
    }

    public static function NotifyAllGuests(User $user,$title,$message)
    {
        $guests = $user->guests;
        foreach ($guests as $guest) {
            if($guest->fcm_device_token){
                self::NotificateUser($guest->fcm_device_token,$title,$message);
            }
        }
    }

    
}

// $apiKey = env('FCM_API_KEY');
