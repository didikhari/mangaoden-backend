<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Googleservice {
        public function createSubFolder($parentFolderId, $folderName){
            log_message('info', 'creating sub folder');
            $client = $this->getClient();
            $service = new Google_Service_Drive($client);
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => $parentFolderId));
            $folder = $service.files().create($fileMetadata, array(
                'fields' => 'id'));
            return $folder->id;
        }

        public function upload($content, $filename, $mimeType) {
            log_message('info', 'uploading content');
            $client = $this->getClient();
            $service = new Google_Service_Drive($client);
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $filename));
            $file = $service->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id'));
            return $file->id;
        }

        /**
         * Returns an authorized API client.
         * @return Google_Client the authorized client object
         */
        private function getClient()
        {
            log_message('info', 'creating client START');
            $client = new Google_Client();
            $client->setApplicationName('Google Drive API PHP Quickstart');
            $client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
            $client->setAuthConfig('../assets/credentials.json');
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
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    // Request authorization from the user.
                    $authUrl = $client->createAuthUrl();
                    printf("Open the following link in your browser:\n%s\n", $authUrl);
                    print 'Enter verification code: ';
                    $authCode = trim(fgets(STDIN));

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
                        throw new Exception(join(', ', $accessToken));
                    }
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