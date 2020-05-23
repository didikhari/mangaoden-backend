<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    use ImageKit\ImageKit;

    class Imagekitutils {
        
        public function upload($file, $fileName, $folder){
            log_message('info', 'Uploading '.$file.' '.$fileName.' '.$folder);
            $imageKit = new ImageKit(
                IMAGEKIT_PUBLIC_KEY,
                IMAGEKIT_PRIVATE_KEY,
                IMAGEKIT_ENDPOINT
            );

            $uploadFile = $imageKit->upload(array(
                'file' => $file,
                'fileName' => $fileName,
                'folder' => $folder,
                "useUniqueFileName" => false,
                "isPrivateFile" => false
            ));
            log_message('info', $uploadFile->err);
            return $uploadFile;
        }

    }
?>