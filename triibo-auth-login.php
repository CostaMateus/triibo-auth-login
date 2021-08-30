<?php
    /**
    * Plugin Name: #Triibo Auth Login
    * Description: Habilita o login pela Triibo no marketplace.
    * Version: 2.3
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
        define( 'TAL_API_URL',       'https://triibo-api-gateway-hml.triibo.com.br/'                ); // HOMOLOG
        define( 'TAL_API_TOKEN',     '7472216240776562417070486d4c6170693139'                       ); // HOMOLOG
        define( 'TAL_CHANNEL_ID',    '54522121626f2d416854682d4150312d4368614e4e656c2d776562417070' ); // HOMOLOG
        define( 'TAL_CHANNEL_GROUP', 'triibo-webapp'                                                ); // HOMOLOG
        define( 'TAL_CHANNEL_NAME',  'triibo-webapp'                                                ); // HOMOLOG
        define( 'TAL_CHANNEL_TOKEN', '305221624057656241707021403131'                               ); // HOMOLOG
        define( 'TAL_UID',            null                                                          ); // HOMOLOG
    }
    else
    {
        // $isDev = "FALSE";
        define( 'TAL_API_URL',       'https://triibo-api-gateway.triibo.com.br/'                    ); // PROD
        define( 'TAL_API_TOKEN',     '57336266417050612750524C4170495029'                           ); // PROD
        define( 'TAL_CHANNEL_ID',    '21726962305052442443686E336C6C573342417050'                   ); // PROD
        define( 'TAL_CHANNEL_GROUP', 'triibo-webapp'                                                ); // PROD
        define( 'TAL_CHANNEL_NAME',  'triibo-webapp'                                                ); // PROD
        define( 'TAL_CHANNEL_TOKEN', '38522162405733624170505052236368403138'                       ); // PROD
        define( 'TAL_UID',            null                                                          ); // PROD
    }

    /**
     * Registrando variavel para controle de status do plugin | atiivo - inativo
     */
    add_action( 'init', 'TAL_init_status_global' );
    function TAL_init_status_global()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN === false ) set_transient( '_STATUS_PLUGIN', 'inactive' ); // active
    }
    TAL_init_status_global();


    /**
     * Alterna o sttus do plugin | ativo - inativo
     */
    add_action( 'wp_ajax_TAL_set_status_global', 'TAL_set_status_global' );
    function TAL_set_status_global()
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


    add_action( 'woocommerce_login_form_start', 'TAL_woocommerce_login_form_start' );
    function TAL_woocommerce_login_form_start()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN == 'active' )
        {
            echo '<hr><p class="title-optin" >Entrar com Login e Senha</p>';
        }
    }


    /**
     * Altera o titulo do card do formulario de login
     * De   "Entrar em Sua Conta"
     * Para "Escolha como acessar sua conta"
     */
    add_filter( 'gettext', 'TAL_text_strings', 20, 3 );
    function TAL_text_strings( $translated_text, $text, $domain )
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN == 'active' )
        {
            switch ( $translated_text )
            {
                case 'Entrar em Sua Conta' :
                    $translated_text = __( 'Escolha como acessar sua conta', 'woocommerce' );
                break;
            }
        }

        return $translated_text;
    }


    /**
     *
     */
    add_action( 'woocommerce_login_form_end', 'TAL_woocommerce_login_form_end' );
    function TAL_woocommerce_login_form_end()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN == 'active' )
        {
            echo '
                <hr>
                <p class="title-optin" >Entrar por código SMS</p>
                <form id="form-auth" method="post" >
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide" >
                        <input type="hidden" name="form-auth">
                        <input id="cellphone" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="cellphone" placeholder="+55 (XX) XXXXX-XXXX" />
                        <!-- Error message -->
                        <p id= "error-auth" class="bg-transp text-center text-danger pt-8 mb-0 d-none" ></p>
                        <!-- Já tem código? -->
                        <a id="haveCode" class="bg-transp float-right pointer d-none pb-20 mt-20 mb-0" onclick="haveCode()" >Tenho código</a>
                    </p>

                    <!-- Já tem código? -->
                    <p class="form-row text-center">
                        <a id="register-now" class="bg-transp pointer pt-8 mb-0 d-none" >Deseja se cadastrar agora?</a>
                    </p>

                    <p class="form-row">
                        <button id="btn-form-auth" type="submit" class="woocommerce-Button button set-spin" >Enviar código <div class="d-none spin-load"></div></button>
                    </p>
                </form>
                <hr>
                <p class="title-optin" >Entrar por rede social</p>
            ';
        }
    }


    /**
     * Inclui o trecho de código necessário para fazer o login com Triibo
     */
    add_action( 'martfury_after_login_form', 'TAL_martfury_after_login_form' );
    function TAL_martfury_after_login_form()
    {
        $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

        if ( $_STATUS_PLUGIN == 'active' ) include_once( plugin_dir_path( __FILE__ ) . 'includes/pages/modal-login.html' );
    }


    /**
     * Add new menu to the Admin Control Panel
     */
    add_action( 'admin_menu', 'LPTriibo_add_admin_link' );
    function LPTriibo_add_admin_link()
    {
        add_menu_page(
            'Login pela Triibo',
            'Login Triibo',
            'manage_options',
            'login-pela-triibo',
            'TAL_admin',
            plugin_dir_url( __FILE__ ) . 'includes/images/triibo20.png'
        );
    }
    function TAL_admin()
    {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( __( 'Você não tem permissões suficientes para acessar esta página.' ) );

        include_once( plugin_dir_path( __FILE__ ) . 'includes/TAL-admin.php' );
    }


    /**
     * Envia códgio por sms
     */
    add_action( 'wp_ajax_TAL_authSMS_v1',        'TAL_authSMS_v1' );
    add_action( 'wp_ajax_nopriv_TAL_authSMS_v1', 'TAL_authSMS_v1' );
    function TAL_authSMS_v1()
    {
        $cellphone = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId = TAL_getChannelToken_v1( null );

        $auth_triiboHeader = [
            'triiboHeader' => [
                'apiToken'       => TAL_API_TOKEN,
                'channelGroup'   => TAL_CHANNEL_GROUP,
                'channelName'    => TAL_CHANNEL_NAME,
                'channelId'      => TAL_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId["success"]["newToken"]["channelTokenId"],
            ],
            'cellphone'   => $cellphone,
            'platform'    => 'web'
        ];

        echo json_encode( TAL_exec("authSMS_v1", $auth_triiboHeader) );
        die();
    }


    /**
     * valida código recebido por sms
     */
    add_action( 'wp_ajax_TAL_validateSMS_v1',        'TAL_validateSMS_v1' );
    add_action( 'wp_ajax_nopriv_TAL_validateSMS_v1', 'TAL_validateSMS_v1' );
    function TAL_validateSMS_v1()
    {
        $cellphone      = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId = TAL_getChannelToken_v1( null );

        $validate_triiboHeader = [
            'triiboHeader'   => [
                'apiToken'       => TAL_API_TOKEN,
                'channelGroup'   => TAL_CHANNEL_GROUP,
                'channelName'    => TAL_CHANNEL_NAME,
                'channelId'      => TAL_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId["success"]["newToken"]["channelTokenId"],
            ],
            'transactionId' => $_POST['transactionId'],
            'cellphone'     => $cellphone,
            'code'          => $_POST['code']
        ];

        echo  json_encode( TAL_exec( "validateSMS_v1", $validate_triiboHeader ) );
        die();
    }


    /**
     * Recupera dados do usuario na Triibo
     */
    add_action( 'wp_ajax_TAL_userInfo_v1',        'TAL_userInfo_v1' );
    add_action( 'wp_ajax_nopriv_TAL_userInfo_v1', 'TAL_userInfo_v1' );
    function TAL_userInfo_v1()
    {
        $cellphone         = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId    = TAL_getChannelToken_v1( null );

        $user_triiboHeader = [
            'triiboHeader'    => [
                'apiToken'       => TAL_API_TOKEN,
                'channelGroup'   => TAL_CHANNEL_GROUP,
                'channelName'    => TAL_CHANNEL_NAME,
                'channelId'      => TAL_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId["success"]["newToken"]["channelTokenId"],
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

        $r_curl   = TAL_exec("getUserInfo_v1", $user_triiboHeader);
        $r_curl   = $r_curl['success']['userInfo'];

        $auxName  = explode( ' ', $r_curl['name'] );
        $auxPhone = explode( '@', $r_curl['triiboId'] )[0];
        $auxAddr  = ( isset( $r_curl['addressList'] ) ? $r_curl['addressList'] : null );

        $data     = [
            'username'     => $r_curl['uId'],
            'nicename'     => str_replace( ' ', '-', $r_curl['name'] ),
            'nickname'     => str_replace( ' ', '.', $r_curl['name'] ),
            'display_name' => $r_curl['name'],
            'first_name'   => $auxName[0],
            'last_name'    => ( count( $auxName ) > 1 ) ? end( $auxName ) : '',
            'cpf'          => ( isset( $r_curl['documentList'] ) ? TAL_getCpf( $r_curl['documentList'] ) : '' ),
            'country'      => 'BR',
            'cellphone'    => $auxPhone,
            'email'        => ( isset( $r_curl['contactList'] ) ? TAL_getEmail( $r_curl['contactList'] ) : str_replace( ',', '.', $r_curl['triiboId'] ) ),
        ];

        $r_signin = TAL_signIn( $data );

        if ( count( $r_signin['metaDataAddrs'] ) > 0 )
        {
            $set = TAL_setUserAddress( $r_curl, $r_signin['metaDataAddrs'] );
            $adr = TAL_setPreferAddress( $r_curl['uId'], $r_signin['metaDataAddrs'] );
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


    /**
     * Cadastra usuario na Triibo
     */
    add_action( 'wp_ajax_TAL_registerUserTriibo',        'TAL_registerUserTriibo' );
    add_action( 'wp_ajax_nopriv_TAL_registerUserTriibo', 'TAL_registerUserTriibo' );
    function TAL_registerUserTriibo()
    {
        $cellphone = "+" . preg_replace( '/\D/', '', $_POST['cellphone'] );
        $name      = explode("@", $_POST['email'])[0];
        $email     = [
            "type"     => "email",
            "value"    => $_POST['email'],
            "verified" => false,
        ];

        $channelTokenId_1 = TAL_getChannelToken_v1( null );
        $registerUser     = [
            "triiboHeader"    => [
                "apiToken"       => TAL_API_TOKEN,
                "channelGroup"   => TAL_CHANNEL_GROUP,
                "channelName"    => TAL_CHANNEL_NAME,
                "channelId"      => TAL_CHANNEL_ID,
                "sessionId"      => "001",
                "transactionId"  => "002",
                "channelTokenId" => $channelTokenId_1["success"]["newToken"]["channelTokenId"]
            ],
            "linkedAccount" => null,
            "cellphone"     => $cellphone,
        ];

        // cria usuário se não existir e retorna as informações
        $resp1 = TAL_exec("login", $registerUser);

        if ($resp1["success"])
        {
            $uId         = $resp1["user"]["uId"];
            $contactList = $resp1["user"]["contactList"];

            if ( count( $contactList ) > 1 )
            {
                foreach ( $contactList as &$c )
                {
                    if ( $c["type"] == "email" ) $c["value"] = $email["value"];
                }
            }
            else
            {
                $contactList = array_merge( $contactList, [ $email ] );
            }
        }

        $channelTokenId_2 = TAL_getChannelToken_v1( $uId );
        $setUserInfo    = [
            "triiboHeader"    => [
                "apiToken"       => TAL_API_TOKEN,
                "channelGroup"   => TAL_CHANNEL_GROUP,
                "channelName"    => TAL_CHANNEL_NAME,
                "channelId"      => TAL_CHANNEL_ID,
                "sessionId"      => "001",
                "transactionId"  => "002",
                "channelTokenId" => $channelTokenId_2["success"]["newToken"]["channelTokenId"],
                'uId'            => $uId
            ],
            "queryPartnerAPI" => [
                "setUserInfo"
            ],
            "userInfo"        => [
                "syncType"    => "merge",
                'uId'         => $uId,
                "name"        => $name,
                "aliasName"   => $name,
                "contactList" => [
                    ... $contactList
                ],
            ],
        ];

        // atualiza informações do usuario
        $resp2 = TAL_exec("setUserInfo_v1", $setUserInfo);

        $data  = [
            'username'     => $uId,
            'nicename'     => $name,
            'nickname'     => $name,
            'display_name' => $name,
            'first_name'   => $name,
            'last_name'    => "",
            'cpf'          => "",
            'country'      => "BR",
            'cellphone'    => $cellphone,
            'email'        => $_POST['email'],
        ];

        $r_signin = TAL_signIn( $data );

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



    function TAL_getChannelToken_v1( $uId )
    {
        $passPhrase         = floor( rand( 1000000000, 9999999999 ) ) . $uId . floor( rand( 1000000000, 9999999999 ) );
        $challenge          = TAL_hashTriibo1( TAL_API_TOKEN, $passPhrase, TAL_CHANNEL_TOKEN );
        $token_triiboHeader = [
            'triiboHeader' => [
                'apiToken'      => TAL_API_TOKEN,
                'channelGroup'  => TAL_CHANNEL_GROUP,
                'channelName'   => TAL_CHANNEL_NAME,
                'channelId'     => TAL_CHANNEL_ID,
                'sessionId'     => '001',
                'transactionId' => '002',
                'uId'           => $uId,
                'passPhrase'    => $passPhrase,
                'challenge'     => $challenge
            ],
        ];

        return TAL_exec("getChannelToken_v1", $token_triiboHeader);
    }

    function TAL_hashTriibo1( $apiToken, $passPhrase, $channelToken )
    {
        $challenge1 = md5( $passPhrase . $apiToken     );
        $challenge2 = md5( $challenge1 . $channelToken );
        return $challenge2;
    }

    /**
     * Efetua login do usuário no wordpress
     */
    function TAL_signIn( $data )
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
                    // campo padronizado com o plugin triibo-integration-user
                    add_user_meta( $user_id, 'triiboId_phone', $data['cellphone'] );

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

            $metaDataAddrs = TAL_getBillingShipping( $user->ID );

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
    function TAL_setUserAddress( $info, $addrs )
    {
        $channelTokenId   = TAL_getChannelToken_v1( $info['uId'] );

        $set_triiboHeader = [
            'triiboHeader'    => [
                'apiToken'       => TAL_API_TOKEN,
                'channelGroup'   => TAL_CHANNEL_GROUP,
                'channelName'    => TAL_CHANNEL_NAME,
                'channelId'      => TAL_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId["success"]["newToken"]["channelTokenId"],
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

        return TAL_exec("setUserInfo_v1", $set_triiboHeader);
    }

    function TAL_setPreferAddress( $uId, $addrs )
    {
        $channelTokenId    = TAL_getChannelToken_v1( $uId );

        $pref_triiboHeader = [
            'triiboHeader'    => [
                'apiToken'       => TAL_API_TOKEN,
                'channelGroup'   => TAL_CHANNEL_GROUP,
                'channelName'    => TAL_CHANNEL_NAME,
                'channelId'      => TAL_CHANNEL_ID,
                'sessionId'      => '001',
                'transactionId'  => '002',
                'channelTokenId' => $channelTokenId["success"]["newToken"]["channelTokenId"],
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


        foreach ($addrs as $key => $value)
        {
            if (empty($value['postcode'])) continue;

            $pref_triiboHeader['addresses'][] = [
                'origin'  => 'marketplace',
                'zipCode' => $value['postcode'],
                'type'    => $key
            ];
        }

        return TAL_exec("preferentialAddress", $pref_triiboHeader);
    }

    function TAL_exec($api, $params)
    {
        $ch = curl_init();

		curl_setopt_array( $ch, [
			CURLOPT_URL            => TAL_API_URL . $api,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER => false ,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_HTTPHEADER     => [ "Content-type: application/json" ],
            CURLOPT_POSTFIELDS     => json_encode( $params )
        ]);

        $result = curl_exec( $ch );
        curl_close( $ch );

        return json_decode( $result, true );
    }




    /**
     * Funções internas
     */
    function TAL_getEmail( $data )
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

    function TAL_getCpf( $data )
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

    function TAL_getUserMeta( $user_id )
    {
        $data = array_map( function( $a ) {
            return $a[0];
        }, get_user_meta( $user_id ) );

        return $data;
    }

    function TAL_getBillingShipping( $user_id )
    {
        $data = TAL_getUserMeta( $user_id );
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
