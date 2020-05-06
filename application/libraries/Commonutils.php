<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Commonutils {

        public static function startsWith ($string, $startString) 
        { 
            $len = strlen($startString); 
            return (substr($string, 0, $len) === $startString); 
        } 
        
        public static function endsWith($string, $endString) 
        { 
            $len = strlen($endString); 
            if ($len == 0) { 
                return true; 
            } 
            return (substr($string, -$len) === $endString); 
        } 

        public static function curl_get_contents($url)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);

            $data = curl_exec($ch);
            curl_close($ch);

            return $data;
        }

        public static function getMimeTypes($url){
            $mime_types = array(
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'odt' => 'application/vnd.oasis.opendocument.text ',
                'docx'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'gif' => 'image/gif',
                'jpg' => 'image/jpg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'bmp' => 'image/bmp'
            );
            $ext = pathinfo($url, PATHINFO_EXTENSION);
        
            return $mime_types[$ext];
        }

        public static function imageUrlToBase64($url){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);

            $data = curl_exec($ch);
            curl_close($ch);

            $imageData = base64_encode($data);
            $mime_types = array(
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'odt' => 'application/vnd.oasis.opendocument.text ',
            'docx'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'gif' => 'image/gif',
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'bmp' => 'image/bmp'
            );
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            
            if (array_key_exists($ext, $mime_types)) {
            $a = $mime_types[$ext];
            }
            // log_message('info', 'data: '.$a.';base64,'.$imageData);
            return 'data: '.$a.';base64,'.$imageData;
        }
    }
?>