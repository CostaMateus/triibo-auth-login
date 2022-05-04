<?php

/**
 * Login Before Checkout
 */

// don't call the file directly
if ( ! defined( "ABSPATH" ) ) exit;

class WC_Login_Before_Checkout
{
    protected static $_lbc_url    = null;
	protected static $_instance   = null;
    protected static $_disclaimer = "[disclaimer]";

	protected function __construct ()
    {
        if ( get_transient( "_TAL_STATUS" ) == "active" )
        {
            self::$_lbc_url = md5( "redirect_to_checkout" );

            add_action( "template_redirect",                     [ $this, "redirect_to_account_page" ] );

            add_action( "wp_head",                               [ $this, "redirect_to_checkout_via_html" ] );

            add_action( "wc_login_before_checkout_add_modal",    [ $this, "modal_before_checkout" ] );

            add_filter( "woocommerce_registration_redirect",     [ $this, "redirect_to_checkout" ], 100 );
            add_filter( "woocommerce_login_redirect",            [ $this, "redirect_to_checkout" ], 100 );
        }
    }

	public static function get_instance()
    {
		if ( is_null( self::$_instance ) ) self::$_instance = new self();

		return self::$_instance;
	}

    public static function get_disclaimer()
    {
        return self::$_disclaimer;
    }

    public static function has_query_param ()
    {
		return isset( $_GET["lbc"] ) && $_GET["lbc"] === self::$_lbc_url;
	}

    private function get_login_page_url ()
    {
		return apply_filters( "wc_login_before_checkout_login_page_url",
			get_permalink( get_option( "woocommerce_myaccount_page_id" ) )
		);
	}

    public function get_checkout_page_url ()
    {
        return apply_filters( "wc_login_before_checkout_page_url", wc_get_checkout_url() );
	}

	public function redirect_to_account_page ()
    {
        /**
         * validação do celular
         *      -> se houver conta na triibo, prossegue
         *      -> se não, cria conta triibo
         */

        if ( is_checkout() )
        {
            if ( is_user_logged_in() )
            {
                /**
                 * user logado
                 *      -> verifica triiboId_phone
                 *      -> se houver, manda pro checkout
                 *      -> se não, pede celular
                 *      -> envia e valida código sms
                 *      -> pede email e senha
                 *      -> loga e redireciona para checkout
                 */

            }
            else
            {
                /**
                 * usuario não logado
                 *      -> pede celular
                 *      -> verifica se existe salvo no usermeta triiboId_phone
                 *      -> envia e valida código sms
                 *      -> loga e redireciona para checkout
                 */
                wp_safe_redirect( add_query_arg( "lbc", self::$_lbc_url, $this->get_login_page_url() ) );
                die;
            }
        }
	}

	public function redirect_to_checkout ( $redirect )
    {
		return ( self::has_query_param() ) ? $this->get_checkout_page_url() : $redirect;
	}

    public function redirect_to_checkout_via_html ()
    {
		if ( self::has_query_param() && is_user_logged_in() )
        {
			?>
                <meta http-equiv="Refresh" content="0; url='<?= esc_attr( $this->get_checkout_page_url() ); ?>'" />
            <?php

			exit();
		}
	}

    public function modal_before_checkout()
    {
        include_once( plugin_dir_path( TAL_PATH_FILE ) . "includes/pages/modal-before-checkout.php" );
    }
}
