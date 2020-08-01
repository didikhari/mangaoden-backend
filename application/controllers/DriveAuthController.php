<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class DriveAuthController extends REST_Controller {

        public function __construct() {
            parent::__construct();
        }

        public function driveauth_get(){
            $client = new Google_Client();
            $client->setAuthConfigFile($_SERVER['DOCUMENT_ROOT'].'/assets/client_secret.json');
            $client->setRedirectUri('https://crawl.didikhari.web.id/index.php/driveauth');
            $client->addScope(Google_Service_Drive::DRIVE);

            if (! isset($_GET['code'])) {
                $auth_url = $client->createAuthUrl();
                header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            } else {
                log_message('info', $_GET['code']);
                $client->authenticate($_GET['code']);
                $_SESSION['access_token'] = $client->getAccessToken();
                // Save the token to a file.
                $tokenPath = 'token.json';
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
        }
    }
?>