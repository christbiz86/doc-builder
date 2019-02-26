<?php
    namespace App\Http\Handlers\JobBoard;
    
    // Allows creating posts in JobStreet.
    class JobStreetPostHandler extends JobStreetAccountRequiredHandler
    {
        const YEARS_EXPERIENCE_CLIP = 25;
        // Maybe load this data from a DB table?
        private static $work_country_lookup = [
            'IN' => ['id' => 100, 'name' => 'India'],
            'ID' => ['id' => 97, 'name' => 'Indonesia'],
            'MY' => ['id' => 150, 'name' => 'Malaysia'],
            'PH' => ['id' => 170, 'name' => 'Philippines'],
            'SG' => ['id' => 190, 'name' => 'Singapore'],
            'TH' => ['id' => 208, 'name' => 'Thailand'],
            'VN' => ['id' => 231, 'name' => 'Vietnam'],
            'OT' => ['id' => 0, 'name' => 'Other']
        ];
        public static function getWorkCountryID($loc)
        {
            return self::$work_country_lookup[$loc]['id'];
        }
        public static function getWorkCountryName($loc)
        {
            return self::$work_country_lookup[$loc]['name'];
        }
        public static function workCountryIsOther($loc) {
            return $loc == 'OT';
        }
        public static function getWorkCountries($include_other = true)
        {
            $result = [];
            foreach (self::$work_country_lookup as $key => $value) {
                if ($include_other || !workCountryIsOther($key)) {
                    $result[] = $key;
                }
            }
            return $result;
        }
        private $postID = -1; // Negative = no post ID assigned
        private $position_title = 'Position Title 1';
        private $salary_currency = 2;
        private $min_monthly_salary = 1000;
        private $max_monthly_salary = 1400;
        private $position_level = 2; // e.g. Senior Manager
        private $years_experience = 7;
        private $job_description = '<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>';
        private $map_address = '33 Ubi Avenue 3 Singapore 408868';
        private $youtube_url = '';
        private $map_latitude = 0;
        private $map_longitude = 0;
        private $profile_id = 62321;
        private $hide_company_details = false;
        private $fields_of_study = [12]; // Array of study fields, or [0] for any field
        private $specialization = 103; // e.g. Actuarial Science/Statistics
        private $job_role = 1361; // e.g. Compensation & Benefits
        private $qualifications = [2]; // Array of e.g. Primary/Secondary School/"O" Level
        private $show_salary = true;
        private $skills = ['skill1', 'skill2', 'Python']; // Array of skills, as strings
        private $post_to_sites = [2]; // Array of posting sites. 2 = Singapore
        private $languages = []; // Array of required language IDs.
        private $employment_type = 1;
        private $work_locations = [
            'SG' => [70000]
        ];
        private $work_location_specific_area = '';
        public function getPostID()
        {
            return $this->postID;
        }
        public function setPostID($val)
        {
            if ($val < 0) {
                $val = -1;
            }
            $this->postID = $val;
            return $this;
        }
        
        private function setPostData($data)
        {
            // TODO: Write accessor for all of the above variables
        }
        
        private function constructRequestBody()
        {
            // Assemble most of the request body with an array literal:
            $requestBody = [
                'position_title' => $this->position_title,
                'salary_currency_code' => $this->salary_currency,
                'min_monthly_salary' => $this->min_monthly_salary,
                'max_monthly_salary' => $this->max_monthly_salary,
                'position_level_code' => $this->position_level,
                'years_of_experience_code' => $this->years_experience,
                // 'work_locations_*' handled later
                'work_location_specific_area' => $this->work_location_specific_area,
                'job_description' => $this->job_description,
                'map_address' => $this->map_address,
                'youtube_url' => $this->youtube_url,
                'map' => [
                    'address' => $this->map_address,
                    'latitude' => $this->map_latitude,
                    'longitude' => $this->map_longitude,
                ],
                'map_longitude' => $this->map_longitude,
                'map_latitude' => $this->map_latitude,
                'profile_id' => $this->profile_id,
                'blind_flag' => $this->hide_company_details,
                'field_of_studies' => $this->fields_of_study,
                'specialization' => $this->specialization,
                'role' => $this->job_role, 
                'qualifications' => $this->qualifications,
                'position_level' => $this->position_level,
                'years_of_experience' => $this->years_experience,
                'salary_currency' => $this->salary_currency,
                'job_requirements' => $this->job_description, 
                'salary_display_flag' => $this->show_salary,
                'skills' => $this->skills,
                'sites' => $this->post_to_sites,
                'language_requirements' => $this->languages,
                'employment_types' => $this->employment_type,
                
                // I don't know what any of the below parameters do:
                '1000' => '1',
                '1001' => '4',
                '1002' => '7',
                '1003' => '21',
                'replace_map_flag' => true,
                'header' => '',
                'work_auth_relevant_flag' => true,
                'has_rr_flag' => false,
                'rr_reference_id' => '',
                'realert_flag' => false
            ];
            // Add job ID if present
            if ($this->getPostID() >= 0) {
                $requestBody['job_id'] = $this->getPostID();
            }
            /*
                Handle the 'work_location*' keys:
                * work_locations_countryXX = 'on' if country XX's box is ticked, absent if not ticked (including OT)
                * work_locations = Array of IDs of work locations across all countries (including OT)
                * work_locationsXX = Array of IDs of work locations, only in country XX (excluding OT)
            */
            $work_locations_combined = [];
            foreach (self::getWorkCountries() as $country) {
                if (isset($this->work_locations[$country])) {
                    $work_locations_current = $this->work_locations[$country];
                    $requestBody['work_locations_country' . $country] = 'on';
                    if (!self::workCountryIsOther($country)) {
                        $requestBody['work_locations' . $country] = $work_locations_current;
                    }
                    foreach ($work_locations_current as $locID) {
                        $work_locations_combined[] = $locID;
                    }
                }
            }
            $requestBody['work_locations'] = $work_locations_combined;
            return $requestBody;
        }
        
        public function postJob() {
            $account = $this->getAccount();
            $client = $account->getGuzzle();

            // Build request
            $requestBody =  $this->constructRequestBody();
            
            $response = $client->post(JobStreetUrls::POST_JOBS, [
                'headers' => [
                    'Referer' => JobStreetUrls::FAKE_REFERER,
                ],
                'json'    => $requestBody
            ]);
            $raw_response = $response->getBody()->getContents();
            $decoded_response = json_decode($raw_response);
            $status_code = $response->getStatusCode();
            if ($status_code != '200') {
                $http_error = new \Exception('Bad status code: '. $status_code);
                throw $http_error;
            }
            if (!isset($decoded_response)) {
                $http_error = new \Exception('Invalid JSON response from job post.');
                throw $http_error;
            }
            if (!isset($decoded_response->success) || !$decoded_response->success) {
                $http_error = new \Exception('Job post was not successful. ');
                throw $http_error;
            }
            $this->setPostID(intval($decoded_response->job_id));
        }
    }
?>
