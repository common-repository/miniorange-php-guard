<?php

if(! defined( 'ABSPATH' )) exit;

/**
 * This class denotes all the cURL related functions to make API calls
 * to the miniOrange server. You can read about cURL here : {@link https://curl.haxx.se/}
 * and read how it is implemented in PHP here: {@link http://php.net/manual/en/book.curl.php}
 *
 * cURL is required by the plugin to work. Without
 * cURL the plugin is as good as useless.
 */
class MoCURLPHPG
{
    public static function submit_contact_us(  $q_email, $q_phone, $query  )
    {
        if ( !MoCURLPHPG::is_curl_installed() ){
            $message = 'Please enable curl extension.';
            return json_encode( array( "status" => 'ERROR', "message" => $message ) );
        }

        $current_user 	= wp_get_current_user();
        $url    	  	= get_option( 'mo_host_name' ) . "/moas/rest/customer/contact-us";
        $query        = '[WordPress PHP Guard Plugin V: '.MO_PHP_VERSION.']' . $query;
        
        $fields = array(
            'firstName'  => $user->user_firstname,
            'lastName'   => $user->user_lastname,
            'company'    => $_SERVER['SERVER_NAME'],
            'email'      => $q_email,
            'phone'      => $q_phone,
            'query'      => $query
        );
        
        $field_string = json_encode( $fields );
        $authHeader  = self::createAuthHeader();
        $response 	 = self::callAPI($url, $field_string, $authHeader);
        return true;
    }

    public static function createAuthHeader()
    {
        $header = [
            "Content-Type"  => "application/json",
            "charset"  =>  "UTF-8",
            "Authorization" => "Basic"
        ];
        return $header;
    }

    /**
     *  Uses WordPress HTTP API to make cURL calls to miniOrange server
     *  <br/>Arguments that you can pass
     * <ol>
     *  <li>'timeout'     => 5,</li>
     *  <li>'redirection' => 5,</li>
     *  <li>'httpversion' => '1.0',</li>
     *  <li>'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),</li>
     *  <li>'blocking'    => true,</li>
     *  <li>'headers'     => array(),</li>
     *  <li>'cookies'     => array(),</li>
     *  <li>'body'        => null,</li>
     *  <li>'compress'    => false,</li>
     *  <li>'decompress'  => true,</li>
     *  <li>'sslverify'   => true,</li>
     *  <li>'stream'      => false,</li>
     *  <li>'filename'    => null</li>
     * </ol>
     *
     * @param string $url URL to post to
     * @param string $json_string json encoded post data
     * @param array $headers headers to be passed in the call
     * @param string $method GET or POST or PUT HTTP Method
     * @return string
     */
  
    public static function callAPI($url, $json_string, $headers = ["Content-Type" => "application/json"],$method='POST')
    {
        $args = [
            'method'        => $method,
            'body'          => $json_string,
            'timeout'       => '10000',
            'redirection'   => '10',
            'httpversion'   => '1.0',
            'blocking'      => true,
            'headers'       => $headers,
            'sslverify'     => false,
               ];

        $response = wp_remote_post( $url, $args );
        if ( is_wp_error( $response ) ) {
            wp_die("Something went wrong: <br/> {$response->get_error_message()}");
        }
        return wp_remote_retrieve_body($response);
    }

    public static function is_curl_installed() {
        if ( in_array( 'curl', get_loaded_extensions() ) ) 
        {
            return 1;
        } else {
            return 0;
        }
     }
    
}