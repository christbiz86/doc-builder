<?php
    namespace App\Http\Handlers\JobBoard;
    
    // Performs the 'talent search' function in JobStreet.
    class JobStreetTalentSearchHandler extends JobStreetAccountRequiredHandler
    {
        private $position_titles = [];
        private $skills = [];
        private $keywords = [];
        private $filters = [];
        private $page_number = 1;

        /**
         * @return int
         */
        public function getPageNumber()
        {
            return $this->page_number;
        }

        /**
         * @param int $page_number
         */
        public function setPageNumber($page_number)
        {
            $this->page_number = $page_number;
            // Allow method chaining
            return $this;
        }

        /**
         * @return array
         */
        public function getPositionTitles()
        {
            return $this->position_titles;
        }

        /**
         * @param array $position_titles
         */
        public function setPositionTitles($position_titles)
        {
            if(!is_array($position_titles)){
                throw new \Exception('Position Titles must be an array.');
            }
            $this->position_titles = $position_titles;
            // Allow method chaining
            return $this;
        }
        
        public function getSkills()
        {
            return $this->skills;
        }

        public function setSkills($skills)
        {
            if(!is_array($skills)){
                throw new \Exception('Skills must be an array.');
            }
            $this->skills = $skills;
            // Allow method chaining
            return $this;
        }
        
        public function getKeywords()
        {
            return $this->keywords;
        }

        public function setKeywords($keywords)
        {
            if(!is_array($keywords)){
                throw new \Exception('Keywords must be an array.');
            }
            $this->keywords = $keywords;
            // Allow method chaining
            return $this;
        }
        
        public function getFilters()
        {
            return $this->filters;
        }

        public function setFilters($filters)
        {
            if(!is_array($filters)){
                throw new \Exception('Filters must be an array.');
            }
            $this->filters = $filters;
            // Allow method chaining
            return $this;
        }

        private function constructQuery()
        {
            $position = $this->getPositionTitles();
            $skills = $this->getSkills();
            $extraKeywords = $this->getKeywords();
            $mergedKeywords = array_merge($position, $skills, $extraKeywords);
            $filters = $this->getFilters();
            return [
                'categories'    => [
                    'position_titles' => $position,
                    'skills'          => $skills,
                    'keywords'        => $extraKeywords
                ],
                'faceting_flag' => true,
                'filters'       => $filters,
                'keywords'      => $mergedKeywords
            ];
        }

        public function search()
        {
            $account = $this->getAccount();
            $client = $account->getGuzzle();

            // Build search request
            $requestHeaders = $account->createHeaders();
            $requestBody =  $this->constructQuery();
            // Send request
            $search_response = $client->post(JobStreetUrls::SEARCH_CANDIDATES, [
                'headers' => $requestHeaders,
                'json'    => $requestBody,
                'query'   => [
                    'page' => $this->getPageNumber()
                ]
            ]);

            return $search_response->getBody()->getContents();
        }
    }

?>
