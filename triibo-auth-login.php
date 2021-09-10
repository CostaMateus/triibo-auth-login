<?php
    /**
    * Plugin Name: #Triibo Auth Login
    * Description: Habilita o login pela Triibo no marketplace.
    * Version: 3.0
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

    include_once( plugin_dir_path( __FILE__ ) . 'includes/helpers.php' );

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
                <p class="title-optin" >Entrar com Triibo</p>
                <form id="form-auth" method="post" >
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide" >
                        <input type="hidden" name="form-auth">
                        <input id="cellphone" type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="cellphone" placeholder="+55 (XX) XXXXX-XXXX" />
                        <!-- Error message -->
                        <p id= "error-auth" class="bg-transp text-center text-danger pt-8 mb-0 d-none" ></p>
                        <!-- Já tem código? -->
                        <!-- <a id="haveCode" class="bg-transp float-right pointer d-none pb-20 mt-20 mb-0" onclick="haveCode()" >Tenho código</a> -->
                    </p>

                    <!-- Já tem código? -->
                    <!-- <p class="form-row text-center"><a id="register-now" class="bg-transp pointer pt-8 mb-0 d-none" >Deseja se cadastrar agora?</a></p> -->

                    <p class="form-row">
                        <button id="btn-form-auth" type="submit" class="woocommerce-Button button set-spin" >Solicitar código SMS <div class="d-none spin-load"></div></button>
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
    add_action( 'wp_ajax_TAL_auth_sms',        'TAL_auth_sms' );
    add_action( 'wp_ajax_nopriv_TAL_auth_sms', 'TAL_auth_sms' );
    function TAL_auth_sms()
    {
        $cellphone = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId = TAL_get_channel_token( null );

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
    add_action( 'wp_ajax_TAL_validate_sms',        'TAL_validate_sms' );
    add_action( 'wp_ajax_nopriv_TAL_validate_sms', 'TAL_validate_sms' );
    function TAL_validate_sms()
    {
        $cellphone      = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId = TAL_get_channel_token( null );

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
     * valida código recebido por email
     */
    add_action( "wp_ajax_TAL_validate_code_email",        "TAL_validate_code_email" );
    add_action( "wp_ajax_nopriv_TAL_validate_code_email", "TAL_validate_code_email" );
    function TAL_validate_code_email()
    {
        $code         = $_POST["codeEmail"];
        $_triibo_code = get_transient( "_triibo_code" );

        if ( $_triibo_code === false )
        {
            $result = [
                "success" => null,
                "error"   => [
                    "code"    => 500,
                    "message" => "Não foi possível validar o código.",
                ],
            ];
            TAL_register_log( "TAL_validate_code_email", [ $result ] );
        }
        elseif ($_triibo_code == $code)
        {
            $uId    = TAL_create_or_update_user_triibo( $_POST["cellphone"], $_POST["email"] );
            $result = TAL_create_user_marketplace( $uId, $_POST["cellphone"], $_POST["email"], $_POST["password"] );
            set_transient( "_triibo_code", null );
        }
        else
        {
            $result = [
                "success" => null,
                "error"   => [
                    "code"    => 401,
                    "message" => "Código inválido.",
                ],
            ];
            TAL_register_log( "TAL_validateCodeEmail", [ $result ] );
        }

        echo  json_encode( $result );
        die();
    }


    /**
     * Recupera dados do usuario na Triibo
     */
    add_action( 'wp_ajax_TAL_user_info',        'TAL_user_info' );
    add_action( 'wp_ajax_nopriv_TAL_user_info', 'TAL_user_info' );
    function TAL_user_info()
    {
        $cellphone         = preg_replace( '/\D/', '', $_POST['cellphone'] );

        $channelTokenId    = TAL_get_channel_token( null );

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

        $r_curl = TAL_exec("getUserInfo_v1", $user_triiboHeader);

        if ( empty( $r_curl["error"] ) && empty( $r_curl["success"]["userInfo"] ) )
        {
            $result = [
                "success" => null,
                "error"   => [
                    "code"    => 404,
                    "message" => $r_curl["alert"][0]["errorMsg"],
                ],
            ];
        }
        elseif ( ! empty( $r_curl["error"] ) )
        {
            $result = [
                "success" => null,
                "error"   => [
                    "code"    => 500,
                    "message" => "Server error",
                ],
            ];

            TAL_register_log( "TAL_user_info", [ $r_curl, $result ] );
        }
        else
        {
            $cttList = $r_curl["success"]["userInfo"]["contactList"];
            $email   = TAL_email_verified( $cttList );

            if ( $email )
            {
                $user_id = email_exists( $email );

                if ( $user_id )
                {
                    $result = TAL_login_by_triibo( "id", $user_id );
                }
                else
                {
                    $result = [
                        "success" => null,
                        "error"   => [
                            "message" => "E-mail não encontrado", // pedir email/senha
                            "code"    => 401,
                        ],
                        "alert"   => "TAL_user_info",
                    ];
                    TAL_register_log( "TAL_user_info", [ $r_curl, $result ] );
                }
            }
            else
            {
                $result = [
                    "success" => null,
                    "error"   => [
                        "message" => "E-mail não verificado", // pedir email/senha
                        "code"    => 401,
                    ],
                    "alert"   => "TAL_user_info",
                ];
                TAL_register_log( "TAL_user_info", [ $r_curl, $result ] );
            }
        }

        echo json_encode( $result );
        die();
    }


    /**
     * Cadastra usuario na Triibo
     */
    add_action( "wp_ajax_TAL_resend_email_code",        "TAL_resend_email_code" );
    add_action( "wp_ajax_nopriv_TAL_resend_email_code", "TAL_resend_email_code" );
    function TAL_resend_email_code()
    {
        if ( TAL_send_email_code( $_POST["email"] ) )
        {
            $result = [
                "success" => [
                    "code"      => 200,
                    "message"   => "Código reenviado",
                ],
                "error"   => null,
            ];
        }
        else
        {
            $result = [
                "success" => null,
                "error"   => [
                    "code"    => 500,
                    "message" => "Falha ao reenviar do código por e-mail",
                ],
            ];
            TAL_register_log( "TAL_resend_email_code", [ $_POST["email"], $result ] );
        }

        $result["alert"] = "TAL_resend_email_code";

        echo json_encode( $result );
        die();
    }

    /**
     * Cadastra usuario na Triibo
     */
    add_action( "wp_ajax_TAL_find_user",        "TAL_find_user" );
    add_action( "wp_ajax_nopriv_TAL_find_user", "TAL_find_user" );
    function TAL_find_user()
    {
        $user = get_user_by( "email", $_POST["email"] );

        if ( ! $user )
        {
            // enviar email com código
            TAL_send_email_code( $_POST["email"] );

            $result = [
                "success" => null,
                "error"   => [
                    "message" => "Usuário inexistente",
                    "code"    => 301,
                ],
                "alert"   => "TAL_find_user",
            ];
        }
        else
        {
            if ( $user && wp_check_password( $_POST["password"], $user->data->user_pass, $user->ID ) )
            {
                TAL_create_or_update_user_triibo( $_POST["cellphone"], $_POST["email"], $user->first_name, $user->nickname );

                $cellphone = "+" . preg_replace( '/\D/', "", $_POST["cellphone"] );
                update_user_meta( $user->ID, "triiboId_phone", $cellphone );

                $result = TAL_login_by_triibo( "id", $user->ID );
            }
            else
            {
                $result = [
                    "success" => null,
                    "error"   => [
                        "message" => "Senha incorreta",
                        "code"    => 401,
                    ],
                    "alert"   => "TAL_find_user",
                ];

                TAL_register_log( "TAL_find_user", [ $user, $result ] );
            }
        }

        echo json_encode( $result );
        die();
    }


    /**
     * Funções internas
     */
    function TAL_create_user_marketplace( $uId, $cellphone, $email, $pass )
    {
        $cellphone = "+" . preg_replace( '/\D/', "", $cellphone );
        $name      = explode("@", $email)[0];

        $user_id   = wp_insert_user([
            "user_login"    => $uId,
            "user_pass"     => wp_hash_password( "Triibo2021" ),
            "user_nicename" => $name,
            "user_email"    => $email,
            "display_name"  => $name,
            "nickname"      => $name,
            "first_name"    => $name,
            "last_name"     => "",
        ]);

        if ( ! is_wp_error( $user_id ) )
        {
            wp_set_password( $pass, $user_id );

            // campo padronizado com o plugin triibo-integration-user
            add_user_meta( $user_id, "triiboId_phone", $cellphone );

            $user = get_user_by( "id", $user_id );

            $user->set_role( "subscriber" );
            if ( class_exists( "WooCommerce" ) ) $user->set_role( "customer" );
        }

        return TAL_login_by_triibo( "id", $user_id );
    }

    function TAL_create_or_update_user_triibo( $cellphone, $email, $name = null, $nick = null )
    {
        $cellphone = "+" . preg_replace( '/\D/', "", $cellphone );
        $name      = $name ? $name : explode("@", $email)[0];
        $nick      = $nick ? $nick : $name;
        $emails    = [
            [
                "type"     => "email",
                "value"    => $email,
                "verified" => true,
            ], [
                "type"     => "emailMarketplace",
                "value"    => $email,
                "verified" => true,
            ]
        ];

        $channelTokenId_1 = TAL_get_channel_token( null );
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

        if ( $resp1["success"] )
        {
            $user        = $resp1["user"];
            $uId         = $user["uId"];
            $contactList = $user["contactList"];

            if ( count( $contactList ) > 1 )
            {
                foreach ( $contactList as &$c )
                {
                    if ( $c["type"] == "email" ) $c["value"] = $emails[0]["value"];
                }
            }
            else
            {
                $contactList = array_merge( $contactList, $emails );
            }
        }

        $channelTokenId_2 = TAL_get_channel_token( $uId );
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
                "name"        => ( $user["name"] && $user["name"] != "Triiber" ) ? $user["name"]      : $name,
                "aliasName"   => ( $user["aliasName"] )                          ? $user["aliasName"] : $nick,
                "contactList" => [
                    ... $contactList
                ],
            ],
        ];

        // atualiza informações do usuario
        $resp2 = TAL_exec("setUserInfo_v1", $setUserInfo);

        return $uId;
    }

    /**
     * Envia código de validação por email
     */
    function TAL_send_email_code( $email )
    {
        $validCode = str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);

        set_transient( "_triibo_code", $validCode );

        $to      = $email;
        $subject = "Seu código de ativação";
        $body    = "<html lang='pt-br'><body><p>Seu código de ativação do Marketplace Triibo é:</p><p style='font-size:18px;margin:0'><b>$validCode</b></p></body></html>";
        $headers = array("Content-Type: text/html; charset=UTF-8",'From: Marketplace Triibo <equipemarketplace@triibo.com.br>');

        return wp_mail( $to, $subject, $body, $headers );
    }

    function TAL_email_verified( $data )
    {
        $email = null;

        foreach ( $data as $c )
        {
            if ( $c["type"] == "emailMarketplace" && $c["verified"] ) $email = $c["value"];
        }

        return $email;
    }

    function TAL_login_by_triibo( $field, $value )
    {
        $user = get_user_by( $field, $value );

        return TAL_login( $user, "Login by field_$field" );
    }

    function TAL_login( $user, $alert )
    {
        if ( ! is_wp_error( $user ) )
        {
            wp_clear_auth_cookie();
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );

            $result = [
                "success" => [
                    "redirect" => home_url( "/my-account" ),
                ],
                "error"   => null,
                "alert"   => null,
            ];
        }
        else
        {
            $result = [
                "success" => null,
                "error"   => [
                    "code"    => 500,
                    "message" => "Error login",
                ],
                "alert"   => $alert,
            ];
            TAL_register_log( "TAL_login", [ $user, $result ] );
        }

        return $result;
    }

    function TAL_register_log( $function, $data )
    {
        $nl = PHP_EOL . "==========================" . PHP_EOL . "Return $function" . PHP_EOL . date("Y-m-d H:i:s") . PHP_EOL;
        file_put_contents( plugin_dir_path( __FILE__ ) . "log.txt", $nl . print_r( $data, true ), FILE_APPEND );
    }
