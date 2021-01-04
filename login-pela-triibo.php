<?php
    /**
    * Plugin Name: #Login pela Triibo
    * Description: Habilita o login pela Triibo no marketplace.
    * Version: 1.0
    * Author: Mateus Costa
    * Author URI: https://costamateus.com.br/
    **/

    // don't call the file directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    define( 'LPT_API_URL',       'https://triibo-api-gateway-hml.triibo.com.br/' );
    define( 'LPT_API_TOKEN',     '7472216240776562417070486d4c6170693139' );
    define( 'LPT_CHANNEL_ID',    '54522121626f2d416854682d4150312d4368614e4e656c2d776562417070' );
    define( 'LPT_CHANNEL_GROUP', 'triibo-webapp' );
    define( 'LPT_CHANNEL_NAME',  'triibo-webapp' );
    define( 'LPT_CHANNEL_TOKEN', '305221624057656241707021403131' );
    define( 'LPT_UID',           's6gQlMxJK8RbBSO641my8JdAK0q2' );


    // Override default template
    // themes/martfury/woocommerce/myaccount/form-login.php
    // plugins/woocommerce/templates/myaccount/form-login.php
    add_filter( 'woocommerce_locate_template', 'lptriibo_woocommerce_locate_template', 999, 3 );
    function lptriibo_woocommerce_locate_template( $template, $template_name, $template_path )
    {
        global $woocommerce;
        $_template    = $template;
        if ( ! $template_path ) $template_path = $woocommerce->template_url;

        $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/woocommerce/';

        if ( $template_name == 'myaccount/form-login.php' )
        {
            $template = $plugin_path . $template_name;
        }
        else
        {
            // Look within passed path within the theme - this is priority
            $template = locate_template( array( $template_path . $template_name, $template_name ) );

            // Modification: Get the template from this plugin, if it exists
            if ( ! $template && file_exists( $plugin_path . $template_name ) )
            {
                $template = $plugin_path . $template_name;
            }
            // Use default template
            if ( ! $template )
            {
                $template = $_template;
            }
        }

        // Return what we found
        return $template;
    }



    /*
    * Add new menu to the Admin Control Panel
    */
    // Hook the 'admin_menu' action hook, run the function named 'lptriibo_add_admin_link()'
    add_action( 'admin_menu', 'lptriibo_add_admin_link' );
    // Add a new top level menu link to the ACP
    function lptriibo_add_admin_link()
    {
        add_menu_page(
            'Login pela Triibo', // Title of the page
            'Login Triibo',      // Text to show on the menu link
            'manage_options',    // Capability requirement to see the link
            'login-pela-triibo', // The 'slug' - file to display when clicking the link
            'lptriibo_admin'     //
        );
    }
    // Call the template of admin page
    function lptriibo_admin() {
        if ( ! current_user_can( 'manage_options' ) )  {
            wp_die( __( 'Você não tem permissões suficientes para acessar esta página.' ) );
        }
        include_once( plugin_dir_path( __FILE__ ) . 'includes/lptriibo-admin.php' );
    }



    add_action( 'wp_ajax_authSMS_v1',        'authSMS_v1' );
    add_action( 'wp_ajax_nopriv_authSMS_v1', 'authSMS_v1' );
    function authSMS_v1()
    {
        $cellphone = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId = getChannelToken_v1( null );

        $auth_triiboHeader = [
            'triiboHeader' => [
                'apiToken'       => LPT_API_TOKEN,
                'channelGroup'   => LPT_CHANNEL_GROUP,
                'channelName'    => LPT_CHANNEL_NAME,
                'channelId'      => LPT_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId->success->newToken->channelTokenId
            ],
            'cellphone'   => $cellphone,
            'platform'    => 'web'
        ];

        $auth_ch = curl_init();

		curl_setopt_array( $auth_ch, array(
			CURLOPT_URL            => LPT_API_URL . 'authSMS_v1',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER => false ,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_HTTPHEADER     => array(
                "Content-type: application/json"
            ),
            CURLOPT_POSTFIELDS     => json_encode( $auth_triiboHeader )
        ));

        echo curl_exec( $auth_ch );
        curl_close( $auth_ch );
        die();
    }


    add_action( 'wp_ajax_validateSMS_v1',        'validateSMS_v1' );
    add_action( 'wp_ajax_nopriv_validateSMS_v1', 'validateSMS_v1' );
    function validateSMS_v1()
    {
        $cellphone = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId = getChannelToken_v1( null );

        $validate_triiboHeader = [
            'triiboHeader' => [
                'apiToken'       => LPT_API_TOKEN,
                'channelGroup'   => LPT_CHANNEL_GROUP,
                'channelName'    => LPT_CHANNEL_NAME,
                'channelId'      => LPT_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId->success->newToken->channelTokenId
            ],
            'transactionId' => $_POST['transactionId'],
            'cellphone'     => $cellphone,
            'code'          => $_POST['code']
        ];

        $validate_ch = curl_init();

		curl_setopt_array( $validate_ch, array(
			CURLOPT_URL            => LPT_API_URL . 'validateSMS_v1',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_HTTPHEADER     => array(
                "Content-type: application/json"
            ),
            CURLOPT_POSTFIELDS     => json_encode( $validate_triiboHeader )
        ));

        echo curl_exec( $validate_ch );
        curl_close( $validate_ch );
        die();
    }


    add_action( 'wp_ajax_userInfo_v1',        'userInfo_v1' );
    add_action( 'wp_ajax_nopriv_userInfo_v1', 'userInfo_v1' );
    function userInfo_v1()
    {
        $cellphone         = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId    = getChannelToken_v1( null );

        $user_triiboHeader = [
            'triiboHeader'    => [
                'apiToken'       => LPT_API_TOKEN,
                'channelGroup'   => LPT_CHANNEL_GROUP,
                'channelName'    => LPT_CHANNEL_NAME,
                'channelId'      => LPT_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId->success->newToken->channelTokenId
            ],
            'queryPartnerAPI' => [
                'getUserInfo'
            ],
            'userInfo'        => [
                'contactList' => [
                    [
                        'type'  => 'cellphone',
                        'value' => $cellphone
                    ]
                ]
            ],
        ];

        $user_ch  = curl_init();

		curl_setopt_array( $user_ch, array(
			CURLOPT_URL            => LPT_API_URL . 'getUserInfo_v1',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER => false ,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_HTTPHEADER     => array(
                "Content-type: application/json"
            ),
            CURLOPT_POSTFIELDS     => json_encode( $user_triiboHeader )
        ));

        $r_curl   = curl_exec( $user_ch );
        curl_close( $user_ch );

        $r_curl   = json_decode( $r_curl, true );
        $r_curl   = $r_curl['success']['userInfo'];

        $username = $r_curl['uId'];
        $name     = $r_curl['name'];
        $nickname = $r_curl['name'];

        if ( isset( $r_curl['contactList'] ) )
        {
            foreach( $r_curl['contactList'] as $temp )
            {
                foreach ( $temp as $key => $value )
                {
                    if ( $key == 'type' && $value == 'email')
                    {
                        $email = $temp['value'];
                    }
                }
            }
        }
        else
        {
            $email = str_replace( ',', '.', $r_curl['triiboId'] );
        }

        $r_signin = signIn( $username, $email, $name, $nickname );

        if ( ! $r_signin['error'] )
        {
            $result   = [
                'success' => [
                    'username' => $username,
                    'email'    => $email,
                    'name'     => $name,
                    'nickname' => $nickname,
                    'redirect' => $r_signin['url'],
                    'user'     => $r_curl
                ],
                'error'   => null,
                'alert'   => null,
            ];
        }
        else
        {
            $result   = [
                'success' => null,
                'error'   => [
                    'message' => $r_signin['message']
                ],
                'alert'   => 'login-pela-triibo.php',
            ];
        }

        echo json_encode( $result );
        die();
    }


    function getChannelToken_v1( $uId )
    {
        $passPhrase         = floor( rand( 1000000000, 9999999999 ) ) . $uId . floor( rand( 1000000000, 9999999999 ) );
        $challenge          = hashTriibo1( LPT_API_TOKEN, $passPhrase, LPT_CHANNEL_TOKEN );
        $token_triiboHeader = [
            'triiboHeader' => [
                'apiToken'      => LPT_API_TOKEN,
                'channelGroup'  => LPT_CHANNEL_GROUP,
                'channelName'   => LPT_CHANNEL_NAME,
                'channelId'     => LPT_CHANNEL_ID,
                'sessionId'     => '001',
                'transactionId' => '002',
                'uId'           => $uId,
                'passPhrase'    => $passPhrase,
                'challenge'     => $challenge
            ],
        ];

        $token_ch = curl_init();

		curl_setopt_array( $token_ch, array(
			CURLOPT_URL            => LPT_API_URL . 'getChannelToken_v1',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER => false ,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_HTTPHEADER     => array(
                "Content-type: application/json"
            ),
            CURLOPT_POSTFIELDS     => json_encode( $token_triiboHeader )
        ));

        $result = curl_exec( $token_ch );
        curl_close( $token_ch );

        return json_decode( $result );
    }

    function hashTriibo1( $apiToken, $passPhrase, $channelToken )
    {
        $challenge1 = md5( $passPhrase . $apiToken );
        $challenge2 = md5( $challenge1 . $channelToken );
        return $challenge2;
    }

    function signIn( $username, $email, $name, $nickname )
    {
        if ( ! username_exists( $username ) )
        {
            $email_ext = email_exists( $email );

            if ( ! $email_ext )
            {
                $auxN = explode( ' ', $name );

                $user_id = wp_insert_user([
                    'user_login'    => $username,
                    'user_pass'     => md5( $username . 'triibo20' . $email ),
                    'user_nicename' => str_replace( ' ', '-', $nickname ),
                    'user_email'    => ( strpos( $email, 'sms.triibo.com.br' ) === false ) ? $email : '',
                    'display_name'  => $name,
                    'first_name'    => $auxN[0],
                    'last_name'     => (count($auxN) > 1) ? end($auxN) : '',
                ]);

                if ( ! is_wp_error( $user_id ) )
                {
                    $user = get_user_by( 'id', $user_id );

                    $user->set_role( 'subscriber' );

                    if ( class_exists( 'WooCommerce' ) )
                    {
                        $user->set_role( 'customer' );
                    }

                    // add_user_meta( 'int $user_id', 'string $meta_key', 'mixed $meta_value', 'bool $unique = false' );
                    add_user_meta( $user_id, 'billing_first_name',   'TestFirstName');
                    add_user_meta( $user_id, 'billing_last_name',    'TestLastName');
                    add_user_meta( $user_id, 'billing_cpf',          '123.456.789-00');
                    add_user_meta( $user_id, 'billing_address_1',    'Rua Teste');
                    add_user_meta( $user_id, 'billing_number',       '123');
                    add_user_meta( $user_id, 'billing_address_2',    '000');
                    add_user_meta( $user_id, 'billing_neighborhood', 'Bairro Teste');
                    add_user_meta( $user_id, 'billing_city',         'São Paulo');
                    add_user_meta( $user_id, 'billing_postcode',     '01234-123');
                    add_user_meta( $user_id, 'billing_country',      'BR');                          // fixo
                    add_user_meta( $user_id, 'billing_state',        'SP');
                    add_user_meta( $user_id, 'billing_phone',        '(11) 1234-5678');
                    add_user_meta( $user_id, 'billing_cellphone',    '11912345678');
                    add_user_meta( $user_id, 'billing_email',        'teste@example.com');
                }
                else
                {
                    // throw new Exception('Error');
                    return [
                        'error'   => true,
                        'message' => 'L367'
                    ];
                }
            }
            else
            {
                $user = get_user_by( 'id', $email_ext );
            }
        }
        else
        {
            $user = get_user_by( 'login', $username );
        }

        if ( ! is_wp_error( $user ) )
        {
            wp_clear_auth_cookie();
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );

            // return home_url('/my-account');
            return [
                'error' => false,
                'url'   => home_url( '/my-account' ),
                'user'  => $user
            ];
        }
        else
        {
            // throw new Exception('Error');
            return [
                'error'   => true,
                'message' => 'L398'
            ];
        }
    }
