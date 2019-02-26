<?php
namespace App\Http\Handlers\JobBoard;
    
    use GuzzleHttp\Client;
    
    // Represents an action which requires a JobStreet account.
    class JobStreetAccountRequiredHandler
    {
        private $account = null;
        public function __construct($account = null)
        {
            if ($account instanceof JobStreetAccount) {
                $this->setAccount($account);
            } else {
                $this->setAccount(new JobStreetAccount($account));
            }
        }
        
        public function getAccount()
        {
            return $this->account;
        }
        
        public function setAccount($account)
        {
            $this->account = $account;
            // Allow method chaining
            return $this;
        }
        
        public function getGuzzle()
        {
            return $this->account->getGuzzle();
        }
        
        public function setGuzzle($guzzle)
        {
            $this->account->setGuzzle($guzzle);
            // Allow method chaining
            return $this;
        }
    }
?>
