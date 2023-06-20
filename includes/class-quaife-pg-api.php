<?php

/**
 * Called as needed
 *
 * @link       https://framework.tech
 * @since      1.0.0
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 */

/**
 * Check if the given credentials match the Quaife API. Credentials have been already saved to WP Options 
 *
 * @since      1.0.0
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 * @author     Vladimir Eric <Vladimir@framework.tech>
 */

class QuaifeAPI
{
    private $call;

    private $api_key;
    private $api_secret;
    private $api_validated;

    private $api_url;
    private $api_url_success;
    private $api_url_cancel;
    private $api_url_failure;

    public $api_iframe_src;

    public $responseData;
    public $response;

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public function __construct($call = 'check')
    // check, preauthorize, purchase
    {
        $this->call = $call;

        $this->api_key = get_option('quaife_pg_api_key');
        $this->api_secret = get_option('quaife_pg_api_secret');
        $this->api_validated = get_option('quaife_pg_api_validated');

        $this->api_url = get_option('quaife_pg_api_url');
        $this->api_url_success = get_option('quaife_pg_api_url_success');
        $this->api_url_cancel = get_option('quaife_pg_api_url_cancel');
        $this->api_url_failure = get_option('quaife_pg_api_url_failure');

        switch ($call) {
            case 'check':
                $this->response = $this->request();
                break;
            case 'unset_options':
                $this->response = $this->unset_options();
                break;
            default:
                $this->response = false;
        }
    }

    /**
     * API ket and secret need to be encoded.
     *
     * API ket and secret need to be encoded, and authentication string formed in a certain way (Basic auth.).
     *
     * @since    1.0.0
     */
    private function prepare_auth()
    {
        // if connection already confirmed, or any Key missing - get out
        if ($this->api_validated || !$this->api_key || !$this->api_secret) {
            return false;
        }

        // encode keys & form header key
        return 'Authorization:Basic ' . base64_encode($this->api_key . ':' . $this->api_secret);
    }

    /* *
    * API request via cURL
    * */
    private function request()
    {
        $url = $this->api_url;

        // Dummy params for the request to send json via POST.
        // # Quaife POST request authorization requires that basic purchase info is set #
        $payload = array(
            "amount" => 1.26,
            "currency" => "EUR",
            "action" => "Authorize",
            "customer" => array(
                "firstName" =>  "April",
                "lastName" =>  "King",
                "email" =>  "Dsdfsdf@example.net",
                "phone" =>  "+234323423",
                "accountReference" => "acc0002",
                "address" => array(
                    "streetAddress" => "StreetAddress",
                    "city" =>  "Boston",
                    "postcode" => "01010",
                    "country" => "US"
                )
            ),
            "statementDescriptor" => "Purchase my-shop.com",
            "Description" => "DUMMY PRODUCT",
            "Reference" => "ORD24234",
            "ipAddress" => "188.2.20.211",
            "successUrl" => $this->api_url_success,
            "cancelUrl" => $this->api_url_cancel,
            "failureUrl" => $this->api_url_failure
        );
        if ($this->call != 'check') {
            // set actual params for the request
        }

        $payload_req = json_encode($payload);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload_req),
            'User-Agent: PostmanRuntime/7.31.1',
            'Accept: */*',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            $this->prepare_auth()
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->call == 'check') {
            curl_setopt($ch, CURLOPT_HEADER, true);  // we want headers
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        $responseData = curl_exec($ch);
        // $this->responseData = $responseData;

        // $response_json = $this->request();
        $response = json_decode($responseData);
        $this->api_iframe_src = $response->links[0]->href;

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        if ($curl_error > 0) {
            // throw new RuntimeException($curl_error, $curl_errno);
            return false;
        }
        curl_close($ch);

        if ($this->call == 'check') {
            return $httpcode;
        }
        return $responseData;
    }

    /* * 
    * reset all API-related options
    * (entering new API keys was forced)
    * */
    public function unset_options()
    {

        update_option('quaife_pg_api_key', false);
        update_option('quaife_pg_api_secret', false);
        update_option('quaife_pg_api_validated', false);

        update_option('quaife_pg_api_url', false);
        update_option('quaife_pg_api_url_success', false);
        update_option('quaife_pg_api_url_cancel', false);
        update_option('quaife_pg_api_url_failure', false);

        return false;
    }
}
