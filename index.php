<?php

// LOAD ENVIRONMENT VARIABLES FROM .ENV
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

// INCLUDE A HELPER CLASS FOR THE API.
class CopperHelper {
    private $email;
    private $token;

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    private function getEmail() {
        return $this->email;
    }

    private function getToken() {
        return $this->token;
    }

    private function getHeaders() {

        $copperToken = self::getToken();
        $copperEmail = self::getEmail();
        
        $headers = array( 
            "Content-Type: application/json",
            "X-PW-AccessToken: $copperToken", 
            "X-PW-Application: developer_api",
            "X-PW-UserEmail: $copperEmail",
        );

        return $headers;
    }

    public function postToCopper($url, $data) {

        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl); 

        if (!$response) 
        { 
            echo 'CURL Error: ' . curl_error($curl); 
        } else { 
            echo 'Success: ' . '<pre>'.$response.'</pre>';
            return $response;
        }
        curl_close($curl);
    }


    public function putToCopper($url, $data) {

        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl); 

        if (!$response) 
        { 
            echo 'CURL Error: ' . curl_error($curl); 
        } else { 
            echo 'Success: ' . '<pre>'.$response.'</pre>';
            return $response;
        }
        curl_close($curl);
    }

    public function getCompanies() {

        $data_to_post = array(
            'sort_direction' => 'desc'
        );
        $data_string = json_encode($data_to_post); 

        // Start Curl.
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, 'https://api.prosperworks.com/developer_api/v1/companies/search'); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // send request
        echo '<pre>';
        $response = curl_exec($curl); 
        echo '</pre>';

        if (!$response) 
        { 
            echo 'CURL Error: ' . curl_error($curl); 
        } else { 
            return json_decode($response);
        }
        curl_close($curl);
    }

    public function getPeople() {

        $data_to_post = array(
            'sort_direction' => 'desc'
        );
        $data_string = json_encode($data_to_post); 

        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, 'https://api.prosperworks.com/developer_api/v1/people/search'); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl); 

        if (!$response) 
        { 
            echo 'CURL Error: ' . curl_error($curl); 
        } else { 
            return json_decode($response);
        }
        curl_close($curl);
    }

    public function findCompanyIdBySearch($search) {
        $companies = self::getCompanies();
        
        foreach($companies as $company)
        {
            if( strpos( $company->name, $search) !== false) {
                return $company->id;
            }
        }
    }

    public function findPersonIdByName($search) {
        $people = self::getPeople();
        foreach($people as $person) {
            if( strpos( $person->name, $search) !== false) {
                return $person->id;
            }
        }
    }
}


// CREATE COPPER HELPER OBJ.
$copperHelper = new CopperHelper();
$copperHelper->setEmail(getenv('COPPER_USER_EMAIL'));
$copperHelper->setToken(getenv('COPPER_ACCESS_TOKEN'));


/********************************************************************************************************
 * TASK ONE.
 * 
 * Create new person "Pam Beesley" associated with company Dunder Mifflin. 
 * Use generic search result to find company, *not* passing criteria but looping through results instead. 
 ********************************************************************************************************/
$copperHelper->postToCopper(
    $url = 'https://api.prosperworks.com/developer_api/v1/people', 
    $data = json_encode(
        array(
            'name' => 'Pam Beesly',
            'company_id' => $copperHelper->findCompanyIdBySearch(
                $search = 'Dunder Mifflin'
            )
        )
    )
);

/********************************************************************************************************
 * TASK TWO
 * 
 * Update new record's name to "Pam Halpert"
 ********************************************************************************************************/
$copperHelper->putToCopper(
    $url = 'https://api.prosperworks.com/developer_api/v1/people/' . $copperHelper->findPersonIdByName(
        $search = 'Pam Beesly'
    ),
    $data = json_encode(
        array(
            'name' => 'Pam Halpert',
        )
    )
);

/********************************************************************************************************
 * TASK THREE
 * 
 * Create an opportunity to sell 20,000 post-it notes to Pam.
 ********************************************************************************************************/
$copperHelper->postToCopper(
    $url = 'https://api.prosperworks.com/developer_api/v1/opportunities', 
    $data = json_encode(
        array(
            'name' => 'sell secratary supplies',
            'primary_contact_id' => $copperHelper->findPersonIdByName(
                $search = 'Pam'
            )
        )
    )
);