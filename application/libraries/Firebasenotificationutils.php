<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Firebasenotificationutils {

        public function broadcash ($title, $body) {
            
            $requestHeader = array(
                'Content-Type' => 'application/json', 
                'Authorization' => 'key=AAAAh43dxZs:APA91bG58A7_5mPz6KdOX7xOY_DbZYYQrbCm_e7Ef3WHf6wQ-LtdGd-JoKewDpo6LaH_g-GV0cWcz7BZQuKh4_pH8XOglBuxV4i-KtDootM7_jID8srxPC2pxfMcbahBVdMn7kk2movr'
            );

            $requestBody = array (
                'notification' => array (
                    'title' => $title,
                    'body' => $body,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ),
                'data' => array (
                    'name' => 'Kanaya',
                    'age' => 3,
                    ),
                'to' => 'fRC7yAMdTWKxKLS44UAInP:APA91bFIiwUJfPzt9X-u5OkOqRcXXuCvNgs18sikhsNb8jxhuMumKMwGtk759HpXhvbme-ducMb5f-VoE7GOVcY_2hcLvd33TCAJbv_5U-evMLcnyLFJUQkTS891GFPRoNWI3BaNf6a0',
            );

            $response = Requests::post('https://fcm.googleapis.com/fcm/send', $requestHeader, json_encode($requestBody));
            log_message('info', 'fcm response '.$response->body);
        } 
        
    }
?>