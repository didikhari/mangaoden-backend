<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class DriveAuthController extends REST_Controller {

        public function __construct() {
            parent::__construct();
        }

        public function driveauth_get(){
            $client = new Google_Client();
            $client->setAuthConfigFile('.assets/client_secrets.json');
            $client->setRedirectUri('https://crawl.didikhari.web.id/index.php/driveauth');
            $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

            if (! isset($_GET['code'])) {
                $auth_url = $client->createAuthUrl();
                header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            } else {
                log_message('code', $_GET['code']);
                $client->authenticate($_GET['code']);
                $_SESSION['access_token'] = $client->getAccessToken();
                $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            }
        }
    }
?>