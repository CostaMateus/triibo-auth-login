<?php
    /**
     * Login Form
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    $login_actived = true;
    if ( ! empty( $_POST ) && isset( $_POST['woocommerce-register-nonce'] ) && ! empty( $_POST['woocommerce-register-nonce'] ) ) {
        $login_actived = false;
    }

    $login_form_layout = martfury_get_option( 'login_register_layout' );
    $login_layout      = 'martfury-login-' . $login_form_layout;
    if ( $login_form_layout == 'promotion' ) {
        $login_layout .= ' martfury-login-tabs';
    }
?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php
    $login_class     = $login_actived   ? 'active' : '';
    $register_class  = ! $login_actived ? 'active' : '';
    $col_login_class = 'col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3';
    if ( $login_form_layout == 'promotion' ) {
        $col_login_class = 'col-md-5 col-sm-12';
    }
?>

    <div class="customer-login">

        <div class="row">

            <div class="<?php echo esc_attr( $col_login_class ); ?> col-login">
                <div class="<?php echo esc_attr( $login_layout ); ?>">
                    <ul class="tabs-nav">
                        <li class="active"><a href="#"
                                            class="<?php echo esc_attr( $login_class ); ?>"><?php esc_html_e( 'Log in', 'martfury' ); ?></a>
                        </li>
                        <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
                            <li><a class="<?php echo esc_attr( $register_class ); ?>"
                                href="#"><?php esc_html_e( 'Register', 'martfury' ); ?></a></li>
                        <?php endif; ?>
                    </ul>
                    <div class="tabs-content">

                        <div class="tabs-panel <?php echo esc_attr( $login_class ); ?>">

                            <h2><?php esc_html_e( 'Log In Your Account', 'martfury' ); ?></h2>

                            <form id="form-default" class="woocommerce-form woocommerce-form-login login" method="post">

                                <?php do_action( 'woocommerce_login_form_start' ); ?>

                                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" required
                                        placeholder="<?php esc_attr_e( 'Username or email address', 'martfury' ); ?>"
                                        name="username" id="username" autocomplete="username"
                                        value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>"/>
                                </p>

                                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide form-row-password">
                                    <input class="woocommerce-Input woocommerce-Input--text input-text" required
                                        placeholder="<?php esc_attr_e( 'Password', 'martfury' ); ?>" type="password"
                                        autocomplete="current-password"
                                        name="password" id="password"/>

                                    <!-- Erro message -->
                                    <p id="error-login" class="bg-transp text-center text-danger mt-20 mb-0 d-none" ></p>

                                </p>

                                <?php do_action( 'woocommerce_login_form' ); ?>

                                <p class="form-row">
                                    <span class="woocommerce-form-row__remember">
                                        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                                            <input class="woocommerce-form__input woocommerce-form__input-checkbox"
                                                name="rememberme" type="checkbox" id="rememberme" value="forever"/>
                                            <span><?php esc_html_e( 'Remember me', 'martfury' ); ?></span>
                                        </label>
                                        <a class="lost-password" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot your password?', 'martfury' ); ?></a>
                                    </span>

                                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                                    <button type="submit" class="woocommerce-Button button" name="login"
                                            value="<?php esc_attr_e( 'Log in', 'martfury' ); ?>"><?php esc_html_e( 'Log in', 'martfury' ); ?></button>

                                    <button type="button" class="woocommerce-Button button " name="triibo-login"
                                            id="btn-triibo-login" >Logar com Triibo</button>
                                </p>

                                <?php do_action( 'woocommerce_login_form_end' ); ?>

                            </form>

                        </div>

                        <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

                            <div class="tabs-panel <?php echo esc_attr( $register_class ); ?>">

                                <h2><?php esc_html_e( 'Register An Account', 'martfury' ); ?></h2>

                                <form method="post" class="register woocommerce-form woocommerce-form-register">

                                    <?php do_action( 'woocommerce_register_form_start' ); ?>

                                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <input type="text" required
                                                class="woocommerce-Input woocommerce-Input--text input-text"
                                                placeholder="<?php esc_attr_e( 'Username', 'martfury' ); ?>"
                                                name="username" id="reg_username" autocomplete="username"
                                                value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>"/>
                                        </p>

                                    <?php endif; ?>

                                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                        <input type="email" required
                                            class="woocommerce-Input woocommerce-Input--text input-text"
                                            placeholder="<?php esc_attr_e( 'Email address', 'martfury' ); ?>"
                                            name="email" id="reg_email" autocomplete="email"
                                            value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( $_POST['email'] ) : ''; ?>"/>
                                    </p>

                                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <input type="password" required
                                                placeholder="<?php esc_attr_e( 'Password', 'martfury' ); ?>"
                                                class="woocommerce-Input woocommerce-Input--text input-text"
                                                autocomplete="new-password"
                                                name="password" id="reg_password"/>
                                        </p>

                                    <?php else : ?>

                                        <p><?php esc_html_e( 'A password will be sent to your email address.', 'martfury' ); ?></p>

                                    <?php endif; ?>

                                    <?php do_action( 'woocommerce_register_form' ); ?>

                                    <p class="woocommerce-form-row form-row">
                                        <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                                        <button type="submit" class="woocommerce-Button button" name="register"
                                                value="<?php esc_attr_e( 'Register', 'martfury' ); ?>"><?php esc_html_e( 'Register', 'martfury' ); ?></button>
                                    </p>

                                    <?php do_action( 'woocommerce_register_form_end' ); ?>


                                </form>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <?php do_action( 'martfury_after_login_form' ); ?>

        </div>
    </div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>


    <div id="modal-form-auth" class="bg-modal-login d-none">
        <div class="bg-modal-login-child">
            <div class="modal-header">
                <h2>Entre com o seu celular</h2>
                <span id="close-form-auth" aria-hidden="true">&times;</span>
            </div>
            <form id="form-auth" method="post" >
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <input type="hidden" name="form-auth">
                    <input id="cellphone" type="text" class="woocommerce-Input woocommerce-Input--text input-text" required
                           name="cellphone" placeholder="+55 (XX) XXXXX-XXXX" autofocus />

                    <!-- Error message -->
                    <p id= "error-auth" class="bg-transp text-center text-danger pt-20 mb-0 d-none" ></p>

                </p>
                <button id="btn-form-auth" type="submit" class="woocommerce-Button button " >Próximo</button>
            </form>
        </div>
    </div>
    <div id="modal-form-validate" class="bg-modal-login d-none">
        <div class="bg-modal-login-child">
            <div class="modal-header">
                <h2>Entre com o código</h2>
                <span id="close-form-validate" aria-hidden="true">&times;</span>
            </div>
            <form id="form-validate" method="post" >
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <input type="hidden" name="form-validate">
                    <!-- <input type="text" id="code" name="code" placeholder="Código"><br> -->
                    <input id="code" type="text" class="woocommerce-Input woocommerce-Input--text input-text" required
                           name="code" placeholder="Código" autofocus />

                    <!-- Error message -->
                    <p id="error-valid" class="bg-transp text-center text-danger pt-20 mb-0 d-none" ></p>

                    <a id="reenviar" class="bg-transp float-right mt-20 mb-0" onclick="myFunction()" >Reenviar código</a>

                </p>
                <button id="btn-form-validate" type="submit" class="woocommerce-Button button " >Entrar</button>
            </form>
        </div>
    </div>

    <style>


        /* Helpers error */
        .bg-transp   { background-color: transparent !important; }
        .d-none      { display: none !important; }

        .text-center { text-align: center !important; }
        .text-danger { color: #d45757 !important; font-weight: bold; }

        .float-right { float: right !important; }

        .pt-20       { padding-top: 20px !important; }
        .mt-20       { margin-top: 20px !important; }
        .mb-0        { margin-bottom: 0 !important; }

        /* css comum entre, .bg-modal-load-next-page e .bg-modal-load-checkout */
        .bg-modal-login { top: 0; left: 0; width: 100vw; height: 100vh; display: flex; position: fixed; overflow: auto; z-index: 99999999; text-align: center; justify-content: space-between; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.7); }

        /* modal de centralização, texto e load-gif - checkout */
        .bg-modal-login-child { width: 80%; margin: auto; border-radius: 5px; background-color: #FEFEFE; border-radius: 5px; background-color: #FFF; padding: 35px 30px; border: 1px solid #E8E8E8; }
        .bg-modal-login-child h2 { text-align: left; margin: 0 0 25px 0 !important; }
        .bg-modal-login-child .modal-header { display: flex; flex-direction: row; justify-content: space-between; }
        .bg-modal-login-child .modal-header span{ font-size: 20px; cursor: pointer; }
        .bg-modal-login-child p { margin-bottom: 0 !important; }
        .bg-modal-login-child .button { width: 100%; font-size: 16px; margin-top: 20px !important; border-radius: 3px; height: 53px; line-height: 53px; }

        /* tamanho do checchout-child de acordo com a tela */
        @media (min-width: 576px) {
            .bg-modal-login-child { width: 60%; }
        }
        @media (min-width: 768px) {
            .bg-modal-login-child { width: 40%; }
        }
        @media (min-width: 992px) {
            .bg-modal-login-child { max-width: 430px; }
        }

        .error-border {
            border-color: #D45757 !important;
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" ></script>
    <script>

        $( '#cellphone' ).mask( '+55 (00) 00000-0000' );
        $( '#cellphone' ).blur( function( event ) {
            if( $(this).val().length == 19 )
            {
                // Celular com 9 dígitos + 2 dígitos DDD e 8 da máscara
                $( '#cellphone' ).mask( '+55 (00) 00000-0009' );
            }
            else
            {
                $( '#cellphone' ).mask( '+55 (00) 0000-00009' );
            }
        });

        let genericErrorText = "Ocorreu um erro, tente novamente.";

        let code          = null;
        let cellphone     = null;
        let transactionId = null;

        let modal_auth    = document.getElementById( 'modal-form-auth'     );
        let modal_valid   = document.getElementById( 'modal-form-validate' );

        let btn_auth      = document.getElementById( 'btn-form-auth'     );
        let btn_valid     = document.getElementById( 'btn-form-validate' );

        let error_login   = document.getElementById( 'error-login' );
        let error_auth    = document.getElementById( 'error-auth'  );
        let error_valid   = document.getElementById( 'error-valid' );

        window.myFunction = function () {
            $( '#btn-form-auth' ).click();

        }

        window.addEventListener( 'click', function ( e ) {

            $( btn_auth ).prop( 'disabled', null );
            $( btn_valid ).prop( 'disabled', null );

            // exibe modal do celular
            if ( e.target.id == 'btn-triibo-login' )
            {
                $( modal_auth ).removeClass('d-none');
            }

            // Fazer botao para "tenho codigo"

            // oculta modal do celular ou modal do código
            // ao clicar fora do modal ou no botão de fechar
            if ( e.target.id == 'modal-form-auth'     ||
                 e.target.id == 'close-form-auth')    ||
                 e.target.id == 'modal-form-validate' ||
                 e.target.id == 'close-form-validate' )
            {
                $( modal_auth ).addClass('d-none');
                $( modal_valid ).addClass('d-none');
            }



            if ( e.target.id == 'btn-form-auth' )
            {
                e.preventDefault();

                $( btn_auth ).prop( 'disabled', 'disabled' );

                cellphone = document.getElementById( 'cellphone' ).value;

                if (cellphone == '') {
                    $( btn_auth ).prop( 'disabled', null );
                    $( error_auth ).removeClass( 'd-none' );
                    $( error_auth ).html( 'Insira um número de celular.' );
                }
                else
                {
                    $( error_auth ).addClass( 'd-none' );
                    callAuthAjax();
                }
            }


            // if ( e.target.id == 'btn-form-validate' )
            // {
            //     e.preventDefault();

            //     btn_valid.setAttribute( 'disabled', 'disabled' );

            //     code      = document.getElementById( 'code' ).value;

            //     let teste = false;

            //     $.ajax({
            //         url : '/wp-admin/admin-ajax.php',
            //         type: 'POST',
            //         data: {
            //             action        : 'validateSMS_v1',
            //             code          : code,
            //             cellphone     : cellphone,
            //             transactionId : transactionId
            //         },
            //         success: function( data ) {
            //             console.log( JSON.parse( data ) );

            //             let result = JSON.parse( data );

            //             if ( result.error != null )
            //             {
            //                 btn_auth.removeAttribute( 'disabled' );
            //                 btn_valid.removeAttribute( 'disabled' );

            //                 switch ( result.error.errorCode )
            //                 {
            //                     case 1006:
            //                         // fecha modal código e abre modal numero
            //                         // modal_auth.style.display = 'flex';
            //                         // modal_valid.style.display = 'none';

            //                         // exibe bloco de erro
            //                         // error_auth.style.display     = 'block';
            //                         error_valid.style.display      = 'block';

            //                         // insere msg de erro no bloco
            //                         // error_auth.innerhtml         = 'Código inválido.';
            //                         error_valid.innerhtml          = 'Código inválido.';

            //                         // adiciona classe de erro ao input
            //                         // jQuery('#cellphone').addClass('error-border');
            //                         jQuery('#code').addClass('error-border');
            //                         break;

            //                     case 1007:
            //                         // fecha modal código e abre modal numero
            //                         // modal_auth.style.display = 'flex';
            //                         // modal_valid.style.display = 'none';

            //                         // exibe bloco de erro
            //                         // error_auth.style.display     = 'block';
            //                         error_valid.style.display      = 'block';

            //                         // insere msg de erro no bloco
            //                         // error_auth.innerhtml         = 'Código expirado.';
            //                         error_valid.innerhtml          = 'Código expirado.';

            //                         // adiciona classe de erro ao input
            //                         // jQuery('#cellphone').addClass('error-border');
            //                         jQuery('#code').addClass('error-border');
            //                         break;

            //                     case 1009:
            //                         // fecha modal código e abre modal numero
            //                         modal_auth.style.display = 'flex';
            //                         modal_valid.style.display = 'none';

            //                         // exibe bloco de erro
            //                         error_auth.style.display     = 'block';
            //                         // error_valid.style.display      = 'block';

            //                         // insere msg de erro no bloco
            //                         error_auth.innerhtml         = 'Celular inválido.';
            //                         // error_valid.innerhtml          = 'Celular inválido.';

            //                         // adiciona classe de erro ao input
            //                         jQuery('#cellphone').addClass('error-border');
            //                         // jQuery('#code').addClass('error-border');
            //                         break;

            //                     case 1004:
            //                     case 1005:
            //                     case 1008:
            //                     default:
            //                         // fecha modal código e abre modal numero
            //                         modal_auth.style.display = 'flex';
            //                         modal_valid.style.display = 'none';

            //                         // exibe bloco de erro
            //                         error_auth.style.display     = 'block';
            //                         // error_valid.style.display      = 'block';

            //                         // insere msg de erro no bloco
            //                         error_auth.innerhtml         = 'Algo de erro, tente novamente.';
            //                         // error_valid.innerhtml          = 'Algo de erro, tente novamente.';

            //                         // adiciona classe de erro ao input
            //                         jQuery('#cellphone').addClass('error-border');
            //                         // jQuery('#code').addClass('error-border');
            //                         break;
            //                 }
            //                 // if ( result.error.errorCode == 1004 ||
            //                 //      result.error.errorCode == 1005 ||
            //                 //      result.error.errorCode == 1008)
            //                 // {
            //                 // }
            //                 // else if ( result.error.errorCode == 1006 )
            //                 // {
            //                 // }
            //                 // else if ( result.error.errorCode == 1007 )
            //                 // {
            //                 // }
            //                 // else if ( result.error.errorCode == 1009 )
            //                 // {
            //                 // }
            //                 // else
            //                 // {
            //                 // }
            //             }
            //             else
            //             {
            //                 modal_load                = document.getElementById( 'modal-load-next-page' );
            //                 modal_load.style.display  = 'flex';
            //                 modal_valid.style.display = 'none';

            //                 $.ajax({
            //                     url : '/wp-admin/admin-ajax.php',
            //                     type: 'POST',
            //                     data: {
            //                         action    : 'userInfo_v1',
            //                         cellphone : cellphone,
            //                     },
            //                     success: function( data ) {
            //                         let user = JSON.parse( data );

            //                         if ( user.error != null )
            //                         {
            //                             console.log( JSON.parse( data ) );

            //                             modal_load.style.display  = 'none';
            //                             modal_auth.style.display = 'flex';

            //                             error_auth.style.display     = 'block';
            //                             // error_auth.innerhtml         = 'Número não cadastrado no Triibo!!!!!!';

            //                             // adicionar mensagem de erro no modal;
            //                         }
            //                         else
            //                         {
            //                             window.location.reload();

            //                             // window.location.href = user.success.redirect;
            //                             // console.log( user );
            //                         }
            //                     },
            //                     error: function( data ) {
            //                         console.log( data );
            //                     }
            //                 });
            //             }
            //         },
            //         error: function( data ) {
            //             console.log( data );
            //             btn_auth.removeAttribute( 'disabled' );
            //             btn_valid.removeAttribute( 'disabled' );
            //         }
            //     });
            // }

        }, false);



        window.callAuthAjax = function () {
            $.ajax({
                url : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action    : 'authSMS_v1',
                    cellphone : cellphone
                },
                success: function( data ) {
                    console.log( JSON.parse(data) );

                    let result = JSON.parse( data );

                    if ( result.error != null)
                    {
                        $( btn_auth ).prop( 'disabled', null );
                        $( error_auth ).removeClass( 'd-none' );
                        $( error_auth ).html( genericErrorText );
                    }
                    else
                    {
                        transactionId = result.success.transactionId;

                        $( modal_auth ).addClass('d-none');
                        $( modal_valid ).removeClass('d-none');
                    }
                },
                error  : function( data ) {
                    console.log( data );
                    $( btn_auth ).prop( 'disabled', null );
                    $( error_auth ).removeClass( 'd-none' );
                    $( error_auth ).html( genericErrorText );
                }
            });
        }

    </script>
