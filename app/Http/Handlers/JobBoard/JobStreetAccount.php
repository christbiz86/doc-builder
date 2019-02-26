<?php
    namespace App\Http\Handlers\JobBoard;

    use GuzzleHttp\Client;
    
    // A set of credentials / keys to use to interact with JobStreet.
    class JobStreetAccount
    {
        private $guzzle;
        private $access_token;
        private $api_key;

        public function __construct($guzzle = null)
        {
            $this->setGuzzle($guzzle);
            if (empty($guzzle)) {
                $this->setGuzzle(new Client(['cookies' => true,]));
            }
            $this->setAccessToken(null);
            $this->setApiKey(null);
        }

        /**
         * @return Client
         */
        public function getGuzzle()
        {
            return $this->guzzle;
        }

        /**
         * @param Client $guzzle
         */
        public function setGuzzle($guzzle)
        {
            $this->guzzle = $guzzle;
            // Allow method chaining
            return $this;
        }

        /**
         * @return null
         */
        public function getAccessToken()
        {
            return $this->access_token;
        }

        /**
         * @param null $access_token
         */
        protected function setAccessToken($access_token)
        {
            $this->access_token = $access_token;
            // Allow method chaining
            return $this;
        }

        /**
         * @return null
         */
        public function getApiKey()
        {
            return $this->api_key;
        }

        /**
         * @param null $api_key
         */
        protected function setApiKey($api_key)
        {
            $this->api_key = $api_key;
            // Allow method chaining
            return $this;
        }
        
        public function fetchApiKey() {
            $client = $this->getGuzzle();
            $response = $client->get(JobStreetUrls::BROWSER_API_KEY);
            $status_code = $response->getStatusCode();
            if ($status_code != '200') {
                $http_error = new \Exception('Bad status code: '.$status_code);
                throw $http_error;
            }
            $this->setApiKey($response->getBody()->getContents());
        }
        
        public function fetchAccessToken() {
            $client = $this->getGuzzle();
            $response = $client->get(JobStreetUrls::ACCESS_TOKEN);
            $status_code = $response->getStatusCode();
            if ($status_code != '200') {
                $http_error = new \Exception('Bad status code: '.$status_code);
                throw $http_error;
            }
            $this->setAccessToken($response->getBody()->getContents());
        }

        public function login($username, $password)
        {
            $this->logout();
            $client = $this->getGuzzle();
            // Fetch the expected cookies for the login page
            $get_login = $client->get(JobStreetUrls::GET_LOGIN, []);
            // Build up request from username and password
            $login_request = [
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
                'json'    => [
                    "user_name" => $username,
                    "password"  => $password,
                    "ajax"      => true
                ]
            ];
            // Send to server
            $post_login = $client->post(JobStreetUrls::POST_LOGIN, $login_request);
            // Parse server's response
            $post_login_response = json_decode($post_login->getBody()->getContents());
            $post_login_status_code = $post_login->getStatusCode();
            if ($post_login_status_code != '200') {
                $http_error = new \Exception('Bad status code: '.$post_login_status_code);
                throw $http_error;
            }
            if (!isset($post_login_response)) {
                $http_error = new \Exception('Invalid JSON response from login.');
                throw $http_error;
            }
            if ($post_login_response->status != 'success') {
                $http_error = new \Exception('Login was not successful: '.$post_login_response->message->key);
                throw $http_error;
            }
            $this->setAccessToken($post_login_response->access_token);
            // Get browser API key
            $this->fetchApiKey();

            return $this;
        }

        public function isLoggedIn()
        {
            return isset($this->api_key) && isset($this->access_token);
        }

        public function logout()
        {
            if ($this->isLoggedIn()) {
                $this->getGuzzle()->get(JobStreetUrls::GET_LOGOUT);
            }
            $this->setApiKey(null);
            $this->setAccessToken(null);
        }

        public function assertLogin()
        {
            if (!$this->isLoggedIn()) {
                throw new \Exception("User must be logged in to perform this operation.");
            }
        }
        
        public function createHeaders()
        {
            $this->assertLogin();
            return [
                'Referer'      => JobStreetUrls::FAKE_REFERER,
                'Api-Key'      => $this->getApiKey(),
                'Access-Token' => $this->getAccessToken()
            ];
        }
    }

?>
