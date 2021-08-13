<?php
    /**
    * Plugin Name: #Login pela Triibo
    * Description: Habilita o login pela Triibo no marketplace.
    * Version: 2.0
    * Author: Mateus Costa
    * Author URI: https://costamateus.com.br/
    **/

    // don't call the file directly
    if ( ! defined( 'ABSPATH' ) ) exit;

    /**
     * Verifica o ambiente que está
     * $isDev = strpos(home_url(), "dev-");
     */
    if (strpos(home_url(), "dev-") !== false)
    {
        // $isDev = "TRUE";
        define( 'LPT_API_URL',       'https://triibo-api-gateway-hml.triibo.com.br/'                ); // HOMOLOG
        define( 'LPT_API_TOKEN',     '7472216240776562417070486d4c6170693139'                       ); // HOMOLOG
        define( 'LPT_CHANNEL_ID',    '54522121626f2d416854682d4150312d4368614e4e656c2d776562417070' ); // HOMOLOG
        define( 'LPT_CHANNEL_GROUP', 'triibo-webapp'                                                ); // HOMOLOG
        define( 'LPT_CHANNEL_NAME',  'triibo-webapp'                                                ); // HOMOLOG
        define( 'LPT_CHANNEL_TOKEN', '305221624057656241707021403131'                               ); // HOMOLOG
        define( 'LPT_UID',            null                                                          ); // HOMOLOG
    }
    else
    {
        // $isDev = "FALSE";
        define( 'LPT_API_URL',       'https://triibo-api-gateway.triibo.com.br/'                    ); // PROD
        define( 'LPT_API_TOKEN',     '57336266417050612750524C4170495029'                           ); // PROD
        define( 'LPT_CHANNEL_ID',    '21726962305052442443686E336C6C573342417050'                   ); // PROD
        define( 'LPT_CHANNEL_GROUP', 'triibo-webapp'                                                ); // PROD
        define( 'LPT_CHANNEL_NAME',  'triibo-webapp'                                                ); // PROD
        define( 'LPT_CHANNEL_TOKEN', '38522162405733624170505052236368403138'                       ); // PROD
        define( 'LPT_UID',            null                                                          ); // PROD
    }

    // UID = "C4kNtsVlN1fKi4AFV4tIjt7vQID3"; // Mateus hml
    // UID = "K4uLXGE8diaOr70n9abQoLt9tmA3"; // Mateus prod

    /**
     * Registrando variavel para controle de status do plugin | atiivo - inativo
     */
    add_action( 'init', 'lpt_init_status_global' );
    function lpt_init_status_global()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN === false ) set_transient( '_STATUS_PLUGIN', 'inactive' ); // active
    }
    lpt_init_status_global();


    /**
     * Alterna o sttus do plugin | ativo - inativo
     */
    add_action( 'wp_ajax_lpt_set_status_global', 'lpt_set_status_global' );
    function lpt_set_status_global()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN == 'active' )
        {
            set_transient( '_STATUS_PLUGIN', 'inactive' );
            $_STATUS_PLUGIN = 'inactive';
        }
        else
        {
            set_transient( '_STATUS_PLUGIN', 'active' );
            $_STATUS_PLUGIN = 'active';
        }

        echo $_STATUS_PLUGIN;
        die();
    }


    /**
     * Adiciona o botão "Entrar com Triibo" ao formulário de login
     */
    add_action( 'woocommerce_login_form_end', 'lpt_woocommerce_login_form_end' );
    function lpt_woocommerce_login_form_end()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN == 'active' )
        {
            echo '
                <p class="form-row">
                    <button type="button" class="woocommerce-Button button set-spin"
                        style="margin-top:0px!important;" name="triibo-login"
                        id="btn-triibo-login" >
                        Entrar com Triibo <div class="d-none spin-load"></div>
                    </button>
                </p>
            ';
        }
    }


    /**
     * Inclui o trecho de código necessário para fazer o login com Triibo
     */
    add_action( 'martfury_after_login_form', 'lpt_martfury_after_login_form' );
    function lpt_martfury_after_login_form()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN == 'active' ) include_once( plugin_dir_path( __FILE__ ) . 'includes/pages/modal-login.html' );
    }


    /**
     * Add new menu to the Admin Control Panel
     */
    add_action( 'admin_menu', 'lptriibo_add_admin_link' );
    function lptriibo_add_admin_link()
    {
        add_menu_page(
            'Login pela Triibo',
            'Login Triibo',
            'manage_options',
            'login-pela-triibo',
            'lptriibo_admin',
            plugin_dir_url( __FILE__ ) . 'includes/images/triibo20.png'
        );
    }
    function lptriibo_admin()
    {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( __( 'Você não tem permissões suficientes para acessar esta página.' ) );

        include_once( plugin_dir_path( __FILE__ ) . 'includes/lptriibo-admin.php' );
    }


    /**
     * Envia códgio por sms
     */
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


    /**
     * valida código recebido por sms
     */
    add_action( 'wp_ajax_validateSMS_v1',        'validateSMS_v1' );
    add_action( 'wp_ajax_nopriv_validateSMS_v1', 'validateSMS_v1' );
    function validateSMS_v1()
    {
        $cellphone      = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId = getChannelToken_v1( null );

        $validate_triiboHeader = [
            'triiboHeader'   => [
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


    /**
     * Recupera dados do usuario na Triibo
     */
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

        $auxName  = explode( ' ', $r_curl['name'] );
        $auxPhone = getPhone( $r_curl['triiboId'] );
        $auxAddr  = ( isset( $r_curl['addressList'] ) ? $r_curl['addressList'] : null );

        $data     = [
            'username'     => $r_curl['uId'],
            'nicename'     => str_replace( ' ', '-', $r_curl['name'] ),
            'nickname'     => str_replace( ' ', '.', $r_curl['name'] ),
            'display_name' => $r_curl['name'],
            'first_name'   => $auxName[0],
            'last_name'    => ( count( $auxName ) > 1 ) ? end( $auxName ) : '',
            'cpf'          => ( isset( $r_curl['documentList'] ) ? getCpf( $r_curl['documentList'] ) : '' ),
            'country'      => 'BR',
            'phone'        => $auxPhone,
            'cellphone'    => $auxPhone,
            'email'        => ( isset( $r_curl['contactList'] ) ? getEmail( $r_curl['contactList'] ) : str_replace( ',', '.', $r_curl['triiboId'] ) ),
        ];

        $r_signin = signIn( $data );

        if ( count( $r_signin['metaDataAddrs'] ) > 0 )
        {
			$ql  = PHP_EOL . "==========================" . PHP_EOL . "Return setUserAddress" . PHP_EOL;
            $set = setUserAddress( $r_curl, $r_signin['metaDataAddrs'] );
            file_put_contents(ABSPATH . 'zLog_LPT.txt', $ql . print_r($set, true), FILE_APPEND);

			$ql  = PHP_EOL . "==========================" . PHP_EOL . "Return setPreferAddress" . PHP_EOL;
            $adr = setPreferAddress( $r_curl['uId'], $r_signin['metaDataAddrs'] );
            file_put_contents(ABSPATH . 'zLog_LPT.txt', $ql . print_r($adr, true), FILE_APPEND);
        }

        if ( ! $r_signin['error'] )
        {
            $result = [
                'success' => [
                    'username' => $data['username'],
                    'email'    => $data['email'],
                    'name'     => $data['display_name'],
                    'nickname' => $data['nickname'],
                    'redirect' => $r_signin['url'],
                ],
                'error'   => null,
                'alert'   => null,
            ];
        }
        else
        {
            $result = [
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

        $ql  = PHP_EOL . "==========================" . PHP_EOL . "Return channel" . PHP_EOL;
        file_put_contents(ABSPATH . 'zLog_LPT_channel.txt', $ql . print_r( json_decode( $result ), true), FILE_APPEND);

        return json_decode( $result );
    }

    function hashTriibo1( $apiToken, $passPhrase, $channelToken )
    {
        $challenge1 = md5( $passPhrase . $apiToken     );
        $challenge2 = md5( $challenge1 . $channelToken );
        return $challenge2;
    }

    /**
     * Efetua login do usuário no wordpress
     */
    function signIn( $data )
    {
        if ( ! username_exists( $data['username'] ) )
        {
            $email_ext = email_exists( $data['email'] );

            if ( ! $email_ext )
            {
                $data['email'] = ( ( strpos( $data['email'], 'sms.triibo.com.br' ) === false ) && ( $data['email'] != null ) ) ? $data['email'] : '';

                $user_id = wp_insert_user([
                    'user_login'    => $data['username'],
                    'user_pass'     => md5( $data['username'] . 'triibo20' . $data['email'] ),
                    'user_nicename' => $data['nicename'],
                    'user_email'    => $data['email'],
                    'display_name'  => $data['display_name'],
                    'nickname'      => $data['nickname'],
                    'first_name'    => $data['first_name'],
                    'last_name'     => $data['last_name'],
                ]);

                if ( ! is_wp_error( $user_id ) )
                {
                    $user = get_user_by( 'id', $user_id );

                    $user->set_role( 'subscriber' );

                    if ( class_exists( 'WooCommerce' ) )
                    {
                        $user->set_role( 'customer' );
                    }
                }
                else
                {
                    // throw new Exception('Error');
                    return [
                        'error'   => true,
                        'message' => 'L358'
                    ];
                }
            }
            else
            {
                $id   = $email_ext;

                $user = get_user_by( 'id', $id );
            }
        }
        else
        {
            $user = get_user_by( 'login', $data['username'] );
        }

        if ( ! is_wp_error( $user ) )
        {
            wp_clear_auth_cookie();
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );

            $metaDataAddrs = getBillingShipping( $user->ID );

            return [
                'error'         => false,
                'url'           => home_url( '/my-account' ),
                'metaDataAddrs' => $metaDataAddrs,
            ];
        }
        else
        {
            return [
                'error'   => true,
                'message' => 'L404'
            ];
        }
    }

    /**
     * Manda os endereços do usuário para a Triibo
     */
    function setUserAddress( $info, $addrs )
    {
        $channelTokenId   = getChannelToken_v1( $info['uId'] );

        $set_triiboHeader = [
            'triiboHeader'    => [
                'apiToken'       => LPT_API_TOKEN,
                'channelGroup'   => LPT_CHANNEL_GROUP,
                'channelName'    => LPT_CHANNEL_NAME,
                'channelId'      => LPT_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId->success->newToken->channelTokenId,
                'uId'            => $info['uId']
            ],
            'queryPartnerAPI' => [
                'setUserInfo'
            ],
            'userInfo'        => [
                // 'addressList' => [],
                'triiboId'   => $info['triiboId'],
                'gender'     => $info['gender'],
                'syncType'   => 'merge',
                'updateDate' => 1610387795564
            ],
        ];

        $i = 0;
        foreach ($addrs as $key => $value)
        {
            if (empty($value['postcode']) &&
                empty($value['address_1']) &&
                empty($value['address_2']) &&
                empty($value['number']) &&
                empty($value['neighborhood']) &&
                empty($value['city']) &&
                empty($value['state']) &&
                empty($value['country'])
            ) {
                continue;
            }

            $set_triiboHeader['userInfo']['addressList'][$i++] = [
                'zipCode'      => $value['postcode'],
                'streetAve'    => $value['address_1'],
                'number'       => $value['number'],
                'extra'        => $value['address_2'],
                'neighborhood' => $value['neighborhood'],
                'city'         => $value['city'],
                'state'        => $value['state'],
                'country'      => $value['country'],
                'googleAddr'   => $value['address_1'] . ', ' . $value['number'] . ' - ' . $value['neighborhood'] . ', ' . $value['city'] . ' - ' . $value['state'] . ', ' . $value['postcode'],
                'type'         => $key,          // comercial, residencial, billing, shipping
                'origin'       => 'marketplace', // marketplace,
            ];
        }

        $ql  = PHP_EOL . "==========================" . PHP_EOL . "Header setUserInfo_v1" . PHP_EOL;
        file_put_contents(ABSPATH . 'zLog_LPT_header.txt', $ql . print_r($set_triiboHeader, true), FILE_APPEND);

        $set_ch = curl_init();
		curl_setopt_array( $set_ch, array(
			CURLOPT_URL            => LPT_API_URL . 'setUserInfo_v1',
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
            CURLOPT_POSTFIELDS     => json_encode( $set_triiboHeader )
        ));

        $set_curl = curl_exec( $set_ch );
        curl_close( $set_ch );

        $set_curl = json_decode( $set_curl, true );

        return $set_curl;
    }

    function setPreferAddress( $uId, $addrs )
    {
        $channelTokenId    = getChannelToken_v1( $uId );

        $pref_triiboHeader = [
            'triiboHeader'    => [
                'apiToken'       => LPT_API_TOKEN,
                'channelGroup'   => LPT_CHANNEL_GROUP,
                'channelName'    => LPT_CHANNEL_NAME,
                'channelId'      => LPT_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId->success->newToken->channelTokenId,
                'uId'            => $uId
            ],
            'uId'          => $uId,

            'addresses'    => [
                // [
                //     'origin'  => 'marketplace',
                //     'zipCode' => '04024-020',
                //     'type'    => 'billing',
                // ]
            ]
        ];

        $result = [];

        foreach ($addrs as $key => $value)
        {
            if (empty($value['postcode'])) continue;

            $pref_triiboHeader['addresses'][] = [
                'origin'  => 'marketplace',
                'zipCode' => $value['postcode'],
                'type'    => $key
            ];
        }

        $ql  = PHP_EOL . "==========================" . PHP_EOL . "Header preferentialAddress" . PHP_EOL;
        file_put_contents(ABSPATH . 'zLog_LPT_header.txt', $ql . print_r($pref_triiboHeader, true), FILE_APPEND);

        $pref_ch = curl_init();
        curl_setopt_array( $pref_ch, array(
            // CURLOPT_URL            => 'https://us-central1-triibo-homol.cloudfunctions.net/preferentialAddress',
            CURLOPT_URL            => LPT_API_URL . 'preferentialAddress',
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
            CURLOPT_POSTFIELDS     => json_encode( $pref_triiboHeader )
        ));

        $pref_curl = curl_exec( $pref_ch );
        curl_close( $pref_ch );

        $result[] = json_decode( $pref_curl, true );

        return $result;
    }




    /**
     * Funções internas
     */
    function getEmail( $data )
    {
        $email = null;

        foreach( $data as $temp )
        {
            foreach ( $temp as $key => $value )
            {
                if ( $key == 'type' && $value == 'email' )
                {
                    $email = $temp['value'];
                }
            }
        }

        return $email;
    }

    function getPhone( $data )
    {
        $phone = explode( '@', $data )[0];

        return substr( $phone, 3 );
    }

    function getCpf( $data )
    {
        $cpf = '';

        foreach( $data as $temp )
        {
            foreach ( $temp as $key => $value )
            {
                if ( $key == 'type' && $value == 'cpf' )
                {
                    $cpf = $temp['value'];
                }
            }
        }

        return $cpf;
    }

    function getUserMeta( $user_id )
    {
        $data = array_map( function( $a ) {
            return $a[0];
        }, get_user_meta( $user_id ) );

        return $data;
    }

    function getBillingShipping( $user_id )
    {
        $data = getUserMeta( $user_id );
        $meta = [];

        $bill = 'billing_';
        $ship = 'shipping_';

        foreach ( $data as $key => $value )
        {
            if ( ( substr( $key, 0, strlen( $bill ) ) === $bill ) )
            {
                $temp = explode( $bill, $key )[1];
                $meta['billing'][$temp] = $value;
            }

            if ( ( substr( $key, 0, strlen( $ship ) ) === $ship ) )
            {
                $temp = explode( $ship, $key )[1];
                $meta['shipping'][$temp] = $value;
            }
        }

        return $meta;
    }
