<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Google_utils {

        public function test() {
            $client = new Google_Client();
            $client->setAuthConfig('.assets/client_secrets.json');
            $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
            if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
                $client->setAccessToken($_SESSION['access_token']);
                $drive = new Google_Service_Drive($client);
                $files = $drive->files->listFiles(array())->getItems();
                echo json_encode($files);
            } else {
                $redirect_uri = 'https://crawl.didikhari.web.id/index.php/driveauth';
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            }
        }

    }
?>