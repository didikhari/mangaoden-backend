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
                'to' => 'fFEKY-hdRZ6vspBJY429oC:APA91bELxhuHgCE1Td1W8CcTFNjQj_Bd_LW-0olblmgz2zVZRRmuOM9nRxOrqM7AHRVdCBDTjhfnhsQ10wrZYa0t6emPq-l1_z49buGyRJZwCFc7_Qf1vp_UaLRBSCo59zwU931br-oi',
            );

            $response = Requests::post('https://fcm.googleapis.com/fcm/send', $requestHeader, json_encode($requestBody));
            log_message('info', 'fcm response '.$response->body);
        } 
        
    }
?>