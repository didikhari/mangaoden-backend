<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Googleservice {

        public function test() {
            $client = $this->getClient();
            if (is_null($client)) {
                $client->setAccessToken($_SESSION['access_token']);
                $service = new Google_Service_Drive($client);
                // create folder
                // $folderMetadata = new Google_Service_Drive_DriveFile(array(
                //     'name' => '1',
                //     'mimeType' => 'application/vnd.google-apps.folder',
                //     'parents' => array('1q-hrmGn4Y9XZClqXN0Rg1HUsJBqQVz0L')
                // ));
                
                // $folder = $service->files->create($folderMetadata, array(
                //     'fields' => 'id'));

                // upload file
                $file = $this->list(100, '16v42vuk9MRsLF_5b6FLjPYxObA7-bsub');
                log_message('info', json_encode($file));
                return $file->id;
            } else {
                $redirect_uri = 'https://crawl.didikhari.web.id/index.php/driveauth';
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            }
        }

        public function createSubFolder($parentFolderId, $folderName){
            $client = $this->getClient();
            if(is_null($client)) {
                return null;
            }
            $service = new Google_Service_Drive($client);
            $folderMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => array($parentFolderId)
            ));
            $folder = $service->files->create($folderMetadata, array(
                'fields' => 'id'));
            return $folder->id;
        }

        public function upload($content, $filename, $mimeType, $folderId) {
            log_message('info', 'uploading content');
            $client = $this->getClient();
            if(is_null($client)) {
                return null;
            }
            $service = new Google_Service_Drive($client);
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $filename,
                'parents' => array($folderId)
            ));
            $file = $service->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id'));
            return $file->id;
        }

        public function list($pageSize, $parentGdriveId) {
            // Get the API client and construct the service object.
            $client = $this->getClient();
            if(is_null($client)) {
                return null;
            }
            $service = new Google_Service_Drive($client);

            // Print the names and IDs for up to $pageSize files.
            $optParams = array(
                'pageSize' => $pageSize,
                'fields' => 'nextPageToken, files(id, name)',
                'q' => "'".$parentGdriveId."' in parents"
            );
            $results = $service->files->listFiles($optParams);

            if (count($results->getFiles()) == 0) {
                return null;
            } else {
                return $results->getFiles();
            }
        }

        /**
         * Returns an authorized API client.
         * @return Google_Client the authorized client object
         */
        private function getClient()
        {
            log_message('info', 'creating client START');
            $client = new Google_Client();
            $client->setAuthConfig($_SERVER['DOCUMENT_ROOT'].'/assets/client_secret.json');
            $client->addScope(Google_Service_Drive::DRIVE);
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            // Load previously authorized token from a file, if it exists.
            // The file token.json stores the user's access and refresh tokens, and is
            // created automatically when the authorization flow completes for the first
            // time.
            $tokenPath = 'token.json';
            if (file_exists($tokenPath)) {
                $accessToken = json_decode(file_get_contents($tokenPath), true);
                $client->setAccessToken($accessToken);
            }

            // If there is no previous token or it's expired.
            if ($client->isAccessTokenExpired()) {
                log_message('info', 'Access Token Expired');
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    log_message('info', 'Refresh the token');
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    log_message('info', 'ERROR: Need to Request authorization from the user');
                    // // Request authorization from the user.
                    // $authUrl = $client->createAuthUrl();
                    // printf("Open the following link in your browser:\n%s\n", $authUrl);
                    // print 'Enter verification code: ';
                    // $authCode = trim(fgets(STDIN));

                    // // Exchange authorization code for an access token.
                    // $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    // $client->setAccessToken($accessToken);

                    // // Check to see if there was an error.
                    // if (array_key_exists('error', $accessToken)) {
                    //     throw new Exception(join(', ', $accessToken));
                    // }
                    return null;
                }
                // Save the token to a file.
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
            log_message('info', 'creating client DONE');
            return $client;
        }
        
    }
?>