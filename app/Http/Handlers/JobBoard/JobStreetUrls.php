<?php
    namespace App\Http\Handlers\JobBoard;
    
    class JobStreetUrls
    {
        const BASE_DOMAIN = 'https://siva.jobstreet.com.sg';
        const API_DOMAIN = 'https://api.jobstreet.com/v3';
        const FAKE_REFERER = self::BASE_DOMAIN.'/';
        const GET_LOGIN = self::BASE_DOMAIN.'/#/login';
        const POST_LOGIN = self::BASE_DOMAIN.'/login';
        const GET_LOGOUT = self::BASE_DOMAIN.'/logout?ajax=true';
        const BROWSER_API_KEY = self::BASE_DOMAIN.'/browser-api-key';
        const ACCESS_TOKEN = self::BASE_DOMAIN.'/access-token';
        
        const SEARCH_CANDIDATES = self::API_DOMAIN.'/resumes/search-candidates';
        
        const POST_JOBS = self::BASE_DOMAIN.'/job';
        const REFERENCES = self::API_DOMAIN.'/references';
        const LANGUAGES = self::REFERENCES.'/languages?country=0';
        // Usage: $requestUrl = sprintf(JobStreetUrls::CONSTANT, $languageCode, $countryCode);
        const WORK_LOCATIONS = self::REFERENCES.'/locations?country=%2$d&fields=children&language=%1$d&system_type=siva';
        const OTHER_LOCATIONS = self::REFERENCES.'/other-locations?language=%1$d&system_type=siva';
        const EMPLOYMENT_TYPES = self::REFERENCES.'/employment-types?language=%1$d&system_type=siva';
        const POSITION_LEVELS = self::REFERENCES.'/position-levels?country=%2$d&fields=siva_code&language=%1$d&system_type=siva';
        const QUALIFICATIONS = self::REFERENCES.'/qualification-groups?country=%2$d&language=%1$d&system_type=siva';
        const YEARS_OF_EXPERIENCE = self::REFERENCES.'/years-of-experiences?language=%1$d&system_type=siva';
        const SPECIALIZATIONS = self::REFERENCES.'/specializations?country=%2$d&fields=children&language=%1$d&system_type=siva';
        const FIELDS_OF_STUDY = self::REFERENCES.'/field-of-study-groups?country=%2$d&fields=children&language=%1$d&system_type=siva';
        const CURRENCIES = self::REFERENCES.'/currencies?country=%2$d&language=%1$d&system_type=siva';
        // Usage: $requestUrl = sprintf(JobStreetUrls::CONSTANT, $itemID);
        const READ_JOB = self::API_DOMAIN.'/jobs/me/%1$d';
        const READ_PROFILE = self::API_DOMAIN.'/advertisers/me/company-profiles/%1$d'; 
    }
?>
