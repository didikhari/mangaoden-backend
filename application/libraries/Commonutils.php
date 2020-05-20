<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Commonutils {

        public function startsWith ($string, $startString) 
        { 
            $len = strlen($startString); 
            return (substr($string, 0, $len) === $startString); 
        } 
        
        public function endsWith($string, $endString) 
        { 
            $len = strlen($endString); 
            if ($len == 0) { 
                return true; 
            } 
            return (substr($string, -$len) === $endString); 
        } 

        public function curl_get_contents($url)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);

            $data = curl_exec($ch);
            curl_close($ch);

            return $data;
        }

        public function downloadImage($folder, $filename, $url, $timeout){
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            //ini_set('allow_url_fopen', 'true');
            if (ini_get('allow_url_fopen')) {
                $ctx = stream_context_create(array('http'=>
                    array(
                        'timeout' => $timeout,  //1200 Seconds is 20 Minutes
                    )
                ));
                file_put_contents($folder.'/'.$filename, file_get_contents($url, false, $ctx));

            } else {
                $ch = curl_init();
                $fp = fopen($folder.'/'.$filename, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            }
        }

        public function getMimeTypes($url){
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

        public function imageUrlToBase64($url){
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

        function IsNullOrEmptyString($str){
            return (!isset($str) || trim($str) === '');
        }
    }
?>