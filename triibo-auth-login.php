<?php
    /**
     * Plugin Name:          #Triibo_Auth_Login
     * Description:          Habilita o login pela Triibo no marketplace.
     * Version:              4.1.0
     * Author:               Mateus Costa
     * Author URI:           https://costamateus.com.br/
     * Text Domain:          triibo-auth-login
     * WC requires at least: 3.6
     * WC tested up to:      5.8
     *
     * @package Triibo_Auth_Login
     **/

    // don't call the file directly
    if ( ! defined( "ABSPATH" ) ) exit;

    if ( ! is_plugin_active( "woocommerce/woocommerce.php" ) ) exit;

    date_default_timezone_set("America/Sao_Paulo");

    define( "TAL_PATH_FILE", __FILE__ );

    /**
     * Verifica o ambiente que está
     * $isDev = strpos(home_url(), "dev-");
     */
    if ( strpos( home_url(), "dev-" ) !== FALSE )
    {
        // $isDev = "TRUE";
        define( "TAL_API_URL",       "https://triibo-api-gateway-hml.triibo.com.br/"                ); // HOMOLOG
        define( "TAL_API_TOKEN",     "7472216240776562417070486d4c6170693139"                       ); // HOMOLOG
        define( "TAL_CHANNEL_ID",    "54522121626f2d416854682d4150312d4368614e4e656c2d776562417070" ); // HOMOLOG
        define( "TAL_CHANNEL_GROUP", "triibo-webapp"                                                ); // HOMOLOG
        define( "TAL_CHANNEL_NAME",  "triibo-webapp"                                                ); // HOMOLOG
        define( "TAL_CHANNEL_TOKEN", "305221624057656241707021403131"                               ); // HOMOLOG
        define( "TAL_UID",            NULL                                                          ); // HOMOLOG
    }
    else
    {
        // $isDev = "FALSE";
        define( "TAL_API_URL",       "https://triibo-api-gateway.triibo.com.br/"                    ); // PROD
        define( "TAL_API_TOKEN",     "57336266417050612750524C4170495029"                           ); // PROD
        define( "TAL_CHANNEL_ID",    "21726962305052442443686E336C6C573342417050"                   ); // PROD
        define( "TAL_CHANNEL_GROUP", "triibo-webapp"                                                ); // PROD
        define( "TAL_CHANNEL_NAME",  "triibo-webapp"                                                ); // PROD
        define( "TAL_CHANNEL_TOKEN", "38522162405733624170505052236368403138"                       ); // PROD
        define( "TAL_UID",            NULL                                                          ); // PROD
    }

    // ====================== Funções internas ====================== //
    include_once( plugin_dir_path( __FILE__ ) . "includes/helpers.php" );

    // Login Before Checkout
    if ( ! class_exists( "WC_Login_Before_Checkout" ) )
    {
        include_once( plugin_dir_path( __FILE__ ) . "includes/login-before-checkout.php" );
        add_action( "plugins_loaded", [ "WC_Login_Before_Checkout", "get_instance" ] );
    }


    // ==================== Acitons adicionadas ==================== //
    /**
     * Registrando variavel para controle de status do plugin | atiivo - inativo
     */
    add_action( "init", "TAL_init_status_global" );
    function TAL_init_status_global()
    {
        if ( get_transient( "_TAL_STATUS" ) === FALSE ) set_transient( "_TAL_STATUS", "inactive" ); // active
    }
    TAL_init_status_global();


    /**
     * Alterna o sttus do plugin | ativo - inativo
     */
    add_action( "wp_ajax_TAL_set_status_global", "TAL_set_status_global" );
    function TAL_set_status_global()
    {
        if ( get_transient( "_TAL_STATUS" ) == "active" )
        {
            set_transient( "_TAL_STATUS", "inactive" );
            $status = "inactive";
        }
        else
        {
            set_transient( "_TAL_STATUS", "active" );
            $status = "active";
        }

        echo $status;
        die();
    }


    /**
     * Adiciona um titulo antes do campos de email e senha, no formulario de login
     */
    add_action( "woocommerce_login_form_start", "TAL_woocommerce_login_form_start" );
    function TAL_woocommerce_login_form_start()
    {
        if ( get_transient( "_TAL_STATUS" ) == "active" )
        {
            echo "<hr><p class='title-optin' >Entrar com Login e Senha</p>";
        }
    }


    /**
     * Altera o titulo do card do formulario de login
     * De   "Entrar em Sua Conta"
     * Para "Escolha como acessar sua conta"
     */
    add_filter( "gettext", "TAL_text_strings", 20, 3 );
    function TAL_text_strings( $translated_text, $text, $domain )
    {
        if ( get_transient( "_TAL_STATUS" ) == "active" )
        {
            switch ( $translated_text )
            {
                case "Entrar em Sua Conta" :
                    $translated_text = __( "Escolha como acessar sua conta", "woocommerce" );
                break;
            }
        }

        return $translated_text;
    }


    /**
     * Adiciona ao formulário de login, campo celular
     * Separa o trecho do login por rede social
     */
    add_action( "woocommerce_login_form_end", "TAL_woocommerce_login_form_end" );
    function TAL_woocommerce_login_form_end()
    {
        if ( get_transient( "_TAL_STATUS" ) == "active" )
        {
            echo "
                <hr>
                <p class='title-optin' >Entrar com Triibo</p>
                <form id='form-auth' method='post' >
                    <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide' >
                        <input type='hidden' name='form-auth'>
                        <input id='cellphone' type='text' class='woocommerce-Input woocommerce-Input--text input-text' name='cellphone' placeholder='+55 (XX) XXXXX-XXXX' />
                        <!-- Error message -->
                        <p id= 'error-auth' class='bg-transp text-center text-danger pt-8 mb-0 d-none' ></p>
                        <!-- Já tem código? -->
                        <!-- <a id='haveCode' class='bg-transp float-right pointer d-none pb-20 mt-20 mb-0' onclick='haveCode()' >Tenho código</a> -->
                    </p>

                    <!-- Já tem código? -->
                    <!-- <p class='form-row text-center'><a id='register-now' class='bg-transp pointer pt-8 mb-0 d-none' >Deseja se cadastrar agora?</a></p> -->

                    <p class='form-row'>
                        <button id='btn-form-auth' type='submit' class='woocommerce-Button button set-spin' >Solicitar código SMS <div class='d-none spin-load'></div></button>
                    </p>
                </form>
                <hr>
                <p class='title-optin' >Entrar por rede social</p>
            ";
        }
    }


    /**
     * Inclui código necessário para o login pela Triibo
     * Página de login e de checkout
     */
    add_action( "martfury_after_login_form",       "TAL_modais_login_sms" );
    add_action( "woocommerce_after_checkout_form", "TAL_modais_login_sms" );
    function TAL_modais_login_sms()
    {
        if ( get_transient( "_TAL_STATUS" ) == "active" )
        {
            include_once( plugin_dir_path( __FILE__ ) . "includes/pages/modal-login.php" );
            include_once( plugin_dir_path( __FILE__ ) . "includes/css/style.css.php"     );
            include_once( plugin_dir_path( __FILE__ ) . "includes/js/script.js.php"      );
        }
    }


    /**
     * Add new menu to the Admin Control Panel
     */
    add_action( "admin_menu", "LPTriibo_add_admin_link" );
    function LPTriibo_add_admin_link()
    {
        add_menu_page(
            "Login pela Triibo",
            "Login Triibo",
            "manage_options",
            "login-pela-triibo",
            "TAL_admin",
            plugin_dir_url( __FILE__ ) . "includes/images/triibo20.png"
        );
    }
    function TAL_admin()
    {
        if ( ! current_user_can( "manage_options" ) ) wp_die( __( "Você não tem permissões suficientes para acessar esta página." ) );

        include_once( plugin_dir_path( __FILE__ ) . "includes/TAL-admin.php" );
    }


    /**
     * Envia códgio por sms
     */
    add_action( "wp_ajax_TAL_auth_sms",        "TAL_auth_sms" );
    add_action( "wp_ajax_nopriv_TAL_auth_sms", "TAL_auth_sms" );
    function TAL_auth_sms()
    {
        $cellphone         = preg_replace( "/\D/", "", $_POST["cellphone"] );

        $channelTokenId    = TAL_get_channel_token( NULL );

        $auth_triiboHeader = [
            "platform"     => "web",
            "cellphone"    => $cellphone,
            "triiboHeader" => [
                "apiToken"       => TAL_API_TOKEN,
                "channelGroup"   => TAL_CHANNEL_GROUP,
                "channelName"    => TAL_CHANNEL_NAME,
                "channelId"      => TAL_CHANNEL_ID,
                "sessionId"      => "001",
                "transactionId"  => "002",
                "channelTokenId" => $channelTokenId["success"]["newToken"]["channelTokenId"],
            ],
        ];

        echo json_encode( TAL_exec( "authSMS_v1", $auth_triiboHeader ) );
        die();
    }


    /**
     * Valida código enviado por sms
     */
    add_action( "wp_ajax_TAL_validate_sms",        "TAL_validate_sms" );
    add_action( "wp_ajax_nopriv_TAL_validate_sms", "TAL_validate_sms" );
    function TAL_validate_sms()
    {
        $cellphone             = preg_replace( "/\D/", "", $_POST["cellphone"] );

        $channelTokenId        = TAL_get_channel_token( NULL );

        $validate_triiboHeader = [
            "code"           => $_POST["code"],
            "cellphone"      => $cellphone,
            "transactionId"  => $_POST["transactionId"],
            "triiboHeader"   => [
                "apiToken"       => TAL_API_TOKEN,
                "channelGroup"   => TAL_CHANNEL_GROUP,
                "channelName"    => TAL_CHANNEL_NAME,
                "channelId"      => TAL_CHANNEL_ID,
                "sessionId"      => "001",
                "transactionId"  => "002",
                "channelTokenId" => $channelTokenId["success"]["newToken"]["channelTokenId"],
            ],
        ];

        echo json_encode( TAL_exec( "validateSMS_v1", $validate_triiboHeader ) );
        die();
    }


    /**
     * Valida código enviado por email
     */
    add_action( "wp_ajax_TAL_validate_code_email",        "TAL_validate_code_email" );
    add_action( "wp_ajax_nopriv_TAL_validate_code_email", "TAL_validate_code_email" );
    function TAL_validate_code_email()
    {
        $code         = $_POST["codeEmail"];
        $_triibo_code = get_transient( "_triibo_code" );

        if ( $_triibo_code === FALSE )
        {
            $result = [
                "success" => NULL,
                "error"   => [
                    "message" => "Não foi possível validar o código.",
                    "code"    => 500,
                ],
                "alert"   => "TAL_validate_code_email",
            ];

            TAL_register_log( "TAL_validate_code_email - err 500 - L289", [ $result ] );
        }
        elseif ($_triibo_code == $code)
        {
            $uId    = TAL_create_or_update_user_triibo( $_POST["cellphone"], $_POST["email"] );
            $result = TAL_create_user_marketplace( $uId, $_POST["cellphone"], $_POST["email"], $_POST["password"] );
            set_transient( "_triibo_code", NULL );
        }
        else
        {
            $result = [
                "success" => NULL,
                "error"   => [
                    "message" => "Código inválido.",
                    "code"    => 401,
                ],
                "alert"   => "TAL_validate_code_email",
            ];

            TAL_register_log( "TAL_validate_code_email - err 401 - L308", [ $result ] );
        }

        echo json_encode( $result );
        die();
    }


    /**
     * Recupera dados do usuário na Triibo
     */
    add_action( "wp_ajax_TAL_user_info",        "TAL_user_info" );
    add_action( "wp_ajax_nopriv_TAL_user_info", "TAL_user_info" );
    function TAL_user_info()
    {
        $cellphone         = preg_replace( "/\D/", "", $_POST["cellphone"] );

        $channelTokenId    = TAL_get_channel_token( NULL );

        $user_triiboHeader = [
            "triiboHeader"    => [
                "apiToken"       => TAL_API_TOKEN,
                "channelGroup"   => TAL_CHANNEL_GROUP,
                "channelName"    => TAL_CHANNEL_NAME,
                "channelId"      => TAL_CHANNEL_ID,
                "sessionId"      => "001",
                "transactionId"  => "002",
                "channelTokenId" => $channelTokenId["success"]["newToken"]["channelTokenId"],
            ],
            "queryPartnerAPI" => [
                "getUserInfo"
            ],
            "userInfo"        => [
                "contactList" => [
                    [
                        "type"  => "cellPhone",
                        "value" => $cellphone
                    ]
                ]
            ],
        ];

        $r_curl = TAL_exec("getUserInfo_v1", $user_triiboHeader);

        if ( empty( $r_curl["error"] ) && empty( $r_curl["success"]["userInfo"] ) )
        {
            $result = [
                "success" => NULL,
                "error"   => [
                    "message" => $r_curl["alert"][0]["errorMsg"],
                    "code"    => 404,
                ],
                "alert"   => "TAL_user_info",
            ];

            TAL_register_log( "TAL_user_info - err 404 - L363", [ $r_curl, $result ] );
        }
        elseif ( ! empty( $r_curl["error"] ) )
        {
            $result = [
                "success" => NULL,
                "error"   => [
                    "message" => "Server error",
                    "code"    => 500,
                ],
                "alert"   => "TAL_user_info",
            ];

            TAL_register_log( "TAL_user_info - err 500 - L376", [ $r_curl, $result ] );
        }
        else
        {
            $uid     = $r_curl["success"]["userInfo"]["id"];
            $cttList = $r_curl["success"]["userInfo"]["contactList"];
            $email   = TAL_email_verified( $cttList );

            if ( $email )
            {
                $user_id = email_exists( $email );

                if ( $user_id )
                {
                    // add triibo_phone e triibo_uid qnd lbc true
                    if ( $_POST["lbc"] )
                    {
                        $triibo_phone = get_user_meta( $user_id, "triiboId_phone", true );
                        $triibo_uid   = get_user_meta( $user_id, "_triibo_id",     true );

                        if ( empty( $triibo_phone ) )
                        {
                            // user sem triiboId_phone
                            update_user_meta( $user_id, "triiboId_phone", "+$cellphone" );
                        }

                        if ( empty( $triibo_uid ) )
                        {
                            // user sem _triibo_id
                            update_user_meta( $user_id, "_triibo_id", $uid );
                        }
                    }

                    $result = TAL_login_by_triibo( "id", $user_id );
                }
                else
                {
                    $result = [
                        "success" => NULL,
                        "error"   => [
                            "message" => "E-mail não encontrado", // pedir email/senha
                            "code"    => 401,
                        ],
                        "alert"   => "TAL_user_info",
                    ];
                }
            }
            else
            {
                $result = [
                    "success" => NULL,
                    "error"   => [
                        "message" => "E-mail não verificado", // pedir email/senha
                        "code"    => 401,
                    ],
                    "alert"   => "TAL_user_info",
                ];
            }
        }

        echo json_encode( $result );
        die();
    }


    /**
     * Reenvia código por email
     */
    add_action( "wp_ajax_TAL_resend_email_code",        "TAL_resend_email_code" );
    add_action( "wp_ajax_nopriv_TAL_resend_email_code", "TAL_resend_email_code" );
    function TAL_resend_email_code()
    {
        if ( TAL_send_email_code( $_POST["email"] ) )
        {
            $result = [
                "success" => [
                    "message" => "Código reenviado",
                    "code"    => 200,
                ],
                "error"   => NULL,
                "alert"   => NULL,
            ];
        }
        else
        {
            $result = [
                "success" => NULL,
                "error"   => [
                    "message" => "Falha ao reenviar do código por e-mail",
                    "code"    => 500,
                ],
                "alert"   => "TAL_resend_email_code",
            ];

            TAL_register_log( "TAL_resend_email_code - err 500 - L462", [ $_POST["email"], $result ] );
        }

        $result["alert"] = "TAL_resend_email_code";

        echo json_encode( $result );
        die();
    }


    /**
     * Cadastra usuário na Triibo
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
                "success" => NULL,
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
                $uid       = TAL_create_or_update_user_triibo( $_POST["cellphone"], $_POST["email"], $user->first_name, $user->nickname );

                $cellphone = "+" . preg_replace( "/\D/", "", $_POST["cellphone"] );
                update_user_meta( $user->ID, "triiboId_phone", $cellphone );
                update_user_meta( $user->ID, "_triibo_id",     $uid       );

                $result = TAL_login_by_triibo( "id", $user->ID );
            }
            else
            {
                $result = [
                    "success" => NULL,
                    "error"   => [
                        "message" => "Senha incorreta",
                        "code"    => 401,
                    ],
                    "alert"   => "TAL_find_user",
                ];
            }
        }

        echo json_encode( $result );
        die();
    }


    /**
     * Cadastra usuário na Triibo
     */
    add_action( "wp_ajax_TAL_add_triiboid", "TAL_add_triiboid" );
    function TAL_add_triiboid()
    {
        $user      = wp_get_current_user();

        $uid       = TAL_create_or_update_user_triibo( $_POST["cellphone"], $user->user_email, $user->first_name, $user->nickname );

        $cellphone = "+" . preg_replace( "/\D/", "", $_POST["cellphone"] );
        update_user_meta( $user->ID, "triiboId_phone", $cellphone );
        update_user_meta( $user->ID, "_triibo_id",     $uid       );

        $result = [
            "success" => [
                "redirect" => "/checkout",
            ],
            "error"   => NULL,
            "alert"   => NULL,
        ];

        echo json_encode( $result );
        die();
    }


    /**
     * Registra logs
     */
    function TAL_register_log( $function, $data )
    {
        $nl = PHP_EOL . "==========================" . PHP_EOL . "Return $function" . PHP_EOL . date("Y-m-d H:i:s") . PHP_EOL;

        file_put_contents(
            plugin_dir_path( __FILE__ ) . "log-" . date("Ymd") . ".log",
            $nl . json_encode( $data, TRUE ),
            FILE_APPEND
        );
    }
