<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Firebasenotificationutils {

        public function broadcash ($title, $body, $mangaId, $chapterId, $chapterNumber) {
            
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
                    "action" => 1,
                    "comic_id" => $mangaId,
                    "title" => $title,
                    "chapter_id" => $chapterId,
                    "chapter_no" => $chapterNumber,
                    "chapter_title" => $body,
                    "is_read" => 0,
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                    ),
                'to' => 'eL0wiOyPTya5OR98-LtB4p:APA91bHKFdoLIsSmAZMMW4Zf-GlG7WukWNW7-TFG2d7b6bunNswb9VMzweBnj_9CN0LiQqkEZ5Nf9s2v6rJhP6UYQy3rmuBdDN_IfKT6VM8IKk64SJXwO79TGgfBXwZJL783Jku3YuPN',
            );

            $response = Requests::post('https://fcm.googleapis.com/fcm/send', $requestHeader, json_encode($requestBody));
            log_message('info', 'fcm response '.$response->body);
        } 
        
    }
?>