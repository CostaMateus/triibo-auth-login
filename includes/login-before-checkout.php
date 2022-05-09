<?php

/**
 * Login Before Checkout
 */

// don't call the file directly
if ( ! defined( "ABSPATH" ) ) exit;

class WC_Login_Before_Checkout
{
    protected static $_lbc_url      = null;
	protected static $_instance     = null;
    protected static $_disclaimer_1 = "[disclaimer]";
    protected static $_disclaimer_2 = "[disclaimer]";

	protected function __construct ()
    {
        if ( get_transient( "_TAL_STATUS" ) == "active" )
        {
            self::$_lbc_url = md5( "redirect_to_checkout" );

            // valida se o redirect é checkout,
            // valida se o usuário está logado e não tem triibo_id,
            // valida se o usuário não está logado.
            add_action( "template_redirect",                  [ $this, "redirect_to_account_page" ], 100 );

            // redireciona para tela de chackout
            add_filter( "woocommerce_registration_redirect",  [ $this, "redirect_to_checkout"     ], 100 );
            add_filter( "woocommerce_login_redirect",         [ $this, "redirect_to_checkout"     ], 100 );

            // add modal na página minha conta - tela login
            add_action( "wc_login_before_checkout_add_modal", [ $this, "modal_form_login"      ] );
            // add modal na página minha conta - tela dashboard
            add_action( "woocommerce_account_content",        [ $this, "modal_myacc_dashboard" ] );
        }
    }

	public static function get_instance()
    {
		if ( is_null( self::$_instance ) ) self::$_instance = new self();

		return self::$_instance;
	}

    public static function get_disclaimer( $user_logged = false )
    {
        return ( ! $user_logged ) ? self::$_disclaimer_1 : self::$_disclaimer_2;
    }

    public static function get_checkout_url()
    {
        global $woocommerce;
        return $woocommerce->cart->get_checkout_url();
    }

    public static function has_query_param()
    {
		return isset( $_GET["lbc"] ) && $_GET["lbc"] === self::$_lbc_url;
	}

    private function get_login_page_url()
    {
		return apply_filters( "wc_login_before_checkout_login_page_url",
			get_permalink( get_option( "woocommerce_myaccount_page_id" ) )
		);
	}

	public function redirect_to_account_page()
    {
        if ( is_checkout() )
        {
            if ( is_user_logged_in() )
            {
                // user logado
                $user_id   = get_current_user_id();
                $triibo_id = get_user_meta( $user_id, "triiboId_phone", true );

                if ( empty( $triibo_id ) )
                {
                    // user sem triibo_id
                    $this->safe_redirect();
                }
            }
            else
            {
                // user nao logado
                $this->safe_redirect();
            }
        }
	}

    private function safe_redirect()
    {
        wp_safe_redirect( add_query_arg( "lbc", self::$_lbc_url, $this->get_login_page_url() ) );
        die();
    }

	public function redirect_to_checkout( $redirect )
    {
		return ( self::has_query_param() ) ? wc_get_checkout_url() : $redirect;
	}

    public function modal_form_login()
    {
        include_once( plugin_dir_path( TAL_PATH_FILE ) . "includes/pages/modal-form-login.php" );
    }

    public function modal_myacc_dashboard()
    {
        include_once( plugin_dir_path( TAL_PATH_FILE ) . "includes/pages/modal-myacc-dashboard.php" );
        include_once( plugin_dir_path( TAL_PATH_FILE ) . "includes/css/style.css.php"               );
        include_once( plugin_dir_path( TAL_PATH_FILE ) . "includes/js/script.js.php"                );
    }
}
