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

                                    <button type="button" class="woocommerce-Button button set-spin" name="triibo-login"
                                            id="btn-triibo-login" >Entrar com Triibo <div class="d-none spin-load"></div></button>
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

                    <a id="haveCode" class="bg-transp float-right pointer d-none pb-20 mt-20 mb-0" onclick="haveCode()" >Tenho código</a>
                    <!-- Já tem código?  -->

                </p>
                <button id="btn-form-auth" type="submit" class="woocommerce-Button button set-spin" >Próximo <div class="d-none spin-load"></div></button>
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
                    <input id="code" type="number" class="woocommerce-Input woocommerce-Input--text input-text" required
                           name="code" placeholder="Código" pattern="[0-9]{6}" min=0 max=999999 title="O código tem seis (6) números" autofocus />

                    <!-- Error message -->
                    <p id="error-valid" class="bg-transp text-center text-danger pt-20 mb-0 d-none" ></p>

                    <a id="resend" class="bg-transp float-right pointer pb-20 mt-20 mb-0" onclick="resendCode()" >Reenviar código</a>

                </p>
                <button id="btn-form-validate" type="submit" class="woocommerce-Button button set-spin" >Entrar <div class="d-none spin-load"></div></button>
            </form>
        </div>
    </div>

    <style>

        /* Helpers error */
        .error-border { border-color: #D45757 !important; }

        .bg-transp    { background-color: transparent !important; }
        .d-none       { display: none !important; }
        .d-flex       { display: flex !important; }

        .text-center  { text-align: center !important; }
        .text-danger  { color: #d45757 !important; font-weight: bold; }

        .float-right  { float: right !important; }

        .pt-20        { padding-top: 20px !important; }
        .pb-20        { padding-bottom: 20px !important; }

        .mt-20        { margin-top: 20px !important; }
        .mb-0         { margin-bottom: 0 !important; }

        .pointer      { cursor: pointer; }

        /* css comum entre, .bg-modal-load-next-page e .bg-modal-load-checkout */
        .bg-modal-login { top: 0; left: 0; width: 100vw; height: 100vh; display: flex; position: fixed; overflow: auto; z-index: 99999999; text-align: center; justify-content: space-between; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.7); }

        /* modal de centralização, texto e load-gif - checkout */
        .bg-modal-login-child { width: 80%; margin: auto; border-radius: 5px; background-color: #FEFEFE; border-radius: 5px; background-color: #FFF; padding: 35px 30px; border: 1px solid #E8E8E8; }
        .bg-modal-login-child h2 { text-align: left; margin: 0 0 25px 0 !important; }
        .bg-modal-login-child .modal-header { display: flex; flex-direction: row; justify-content: space-between; }
        .bg-modal-login-child .modal-header span { font-size: 20px; cursor: pointer; }
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

        .set-spin {
            display: flex!important;
            justify-content: center;
        }
        .spin-load {
            width: 20px;
            height: 20px;
            margin: auto 5px;
            border: 3px solid #F2F2F2;
            border-top: 3px solid #AEAEAE;
            border-radius: 50%;
            -webkit-animation: spin 1s linear infinite; /* Safari */
            animation: spin 1s linear infinite;
        }
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

        let genericError  = "Ocorreu um erro, tente novamente.";

        // variaveis de controle entre as chamadas ajax
        let code          = null;
        let cellphone     = null;
        let transactionId = null;
        let codeSent      = false;

        // vars de controle dos modais
        let modal_auth    = document.getElementById( 'modal-form-auth'     );
        let modal_valid   = document.getElementById( 'modal-form-validate' );

        // vars de controle do envio do celular e do cod
        let btn_auth      = document.getElementById( 'btn-form-auth'     );
        let btn_valid     = document.getElementById( 'btn-form-validate' );

        // vars de controle de erros
        let error_login   = document.getElementById( 'error-login' );
        let error_auth    = document.getElementById( 'error-auth'  );
        let error_valid   = document.getElementById( 'error-valid' );



        /**
         * Monitora todos os clicks na página
         * Verifica IDs especificos
         */
        window.addEventListener( 'click', function ( e ) {
            // habilita botões de envio
            switch ( e.target.id )
            {
                // verifica se click foi fora do modal ou no X do modal, fecha o respectivo
                // click fora do modal, desativado
                // case 'modal-form-auth':
                // case 'modal-form-validate':
                case 'close-form-auth':
                case 'close-form-validate':
                    window.closeModal();
                    break;

                // verifica se o click foi no botão triibo
                case 'btn-triibo-login':
                    window.openModalAuth();
                    break;

                // verifica se o click foi no botão do modal do celular
                case 'btn-form-auth':
                    e.preventDefault();
                    window.submitAuth();
                    break;

                // verifica se o click foi no botão do modal do cod
                case 'btn-form-validate':
                    e.preventDefault();
                    window.submitValidate();
                    break;
            }
        }, false);


        /**
         * Fecha modal do numero e abre modal do cod
         *
         */
        window.haveCode = function () {
            $( modal_auth  ).addClass( 'd-none' );
            $( modal_valid ).removeClass( 'd-none' );
        }


        /**
         * Reenvia cod
         */
        window.resendCode = function () {
            $( btn_auth  ).click();
            $( btn_valid ).prop( 'disabled', 'disabled' ).find( 'div' ).removeClass( 'd-none' );

            $( error_valid ).addClass( 'd-none' );
            $( '#code' ).removeClass( 'error-border' );

            setTimeout( function() {
                $( btn_valid ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
            }, 4000 );
        }


        /**
         *
         */
        window.closeModal = function()
        {
            $( btn_auth    ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
            $( btn_valid   ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );

            $( error_auth  ).addClass( 'd-none' ).html( '' );
            $( error_valid ).addClass( 'd-none' ).html( '' );

            $( modal_auth  ).addClass( 'd-none' );
            $( modal_valid ).addClass( 'd-none' );
        }


        /**
         *
         */
        window.openModalAuth = function ()
        {
            // exibe modal para informar o celular
            $( modal_auth ).removeClass( 'd-none' );

            // verifica variaveis de controle
            // exibe botão para mudar para o modal de informar o cod
            if ( cellphone != null && codeSent )
            {
                $( '#haveCode' ).removeClass( 'd-none' );
            }
        }


        /**
         *
         */
        window.submitAuth = function()
        {
            // desabilita o botão
            $( btn_auth ).prop( 'disabled', 'disabled' ).find( 'div' ).removeClass( 'd-none' );

            // recupera o numero informado
            cellphone = document.getElementById( 'cellphone' ).value;

            // valida se o numero não é vazio.
            if ( cellphone == '' )
            {
                $( btn_auth     ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                $( error_auth   ).removeClass( 'd-none' );
                // exibe o erro
                $( error_auth   ).html( 'Insira um número de celular.' );
            }
            else
            {
                $( error_auth   ).addClass( 'd-none' );
                $( '#cellphone' ).removeClass( 'error-border' );
                // executa o ajax para o envio do sms
                callAuthAjax();
            }
        }


        /**
         *
         */
        window.submitValidate = function()
        {
            // desabilita o botão
            $( btn_valid ).prop( 'disabled', 'disabled' ).find( 'div' ).removeClass( 'd-none' );

            // recupera cod informado
            code = document.getElementById( 'code' ).value;

            // valida se cod não é vazio
            if ( code == '' )
            {
                $( btn_valid   ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                $( error_valid ).removeClass( 'd-none' );
                // exibe erro
                $( error_valid ).html( 'Insira código recebido.' );
            }
            // valida se o cod é menor ou maior que 6
            else if ( code.length != 6 )
            {
                $( btn_valid   ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                $( error_valid ).removeClass( 'd-none' );
                // exibe erro
                $( error_valid ).html( 'O código tem 6 dígitos.' );
            }
            else
            {
                $( error_valid ).addClass( 'd-none' );
                $( '#code' ).removeClass( 'error-border' );
                // executa o ajax para validar o cod informado
                callValidateSMS();
            }
        }


        /**
         * Função de envio do cod para o celular
         */
        window.callAuthAjax = function () {
            $.ajax({
                url : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action    : 'authSMS_v1',
                    cellphone : cellphone
                },
                success: function( data ) {
                    // console.log( JSON.parse(data) );

                    let result = JSON.parse( data );

                    // valida se o retorno da função tem erros
                    if ( result.error != null)
                    {
                        $( btn_auth   ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                        $( error_auth ).removeClass( 'd-none' );
                        // exibe erro generico
                        $( error_auth ).html( genericError );
                    }
                    else
                    {
                        // recupera o id da transação
                        transactionId = result.success.transactionId;
                        // var de controle, cod enviado
                        codeSent      = true;

                        // fecha modal atual e abre o modal do cod
                        $( modal_auth  ).addClass('d-none');
                        $( modal_valid ).removeClass('d-none');

                        $( btn_auth ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                    }
                },
                error  : function( data ) {
                    console.log( data );

                    $( btn_auth   ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                    $( error_auth ).removeClass( 'd-none' );
                    // exibe erro generico, caso problema na chamada
                    $( error_auth ).html( genericError );
                }
            });
        }


        /**
         * Função de validação do cod
         */
        window.callValidateSMS = function () {
            $.ajax({
                url : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action        : 'validateSMS_v1',
                    code          : code,
                    cellphone     : cellphone,
                    transactionId : transactionId
                },
                success: function( data ) {
                    // console.log( JSON.parse( data ) );

                    let result = JSON.parse( data );

                    // valida se o retorno da função tem erros
                    if ( result.error != null )
                    {
                        // habilita os botões de envio
                        $( btn_auth  ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                        $( btn_valid ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );

                        // valida qual o cod do erro
                        switch ( result.error.errorCode )
                        {
                            case 1006:
                                $( error_valid  ).removeClass( 'd-none' );
                                // exibe erro
                                $( error_valid  ).html( 'Código inválido.' );
                                $( '#code'      ).addClass( 'error-border' );
                                break;

                            case 1007:
                                $( error_valid  ).removeClass( 'd-none' );
                                // exibe erro
                                $( error_valid  ).html( 'Código expirado.' );
                                $( '#code'      ).addClass( 'error-border' );
                                break;

                            case 1009:
                                $( modal_auth   ).removeClass('d-none');
                                $( modal_valid  ).addClass('d-none');
                                $( error_auth   ).removeClass( 'd-none' );
                                // exibe erro
                                $( error_auth   ).html( 'Código expirado.' );
                                $( '#cellphone' ).addClass( 'error-border' );
                                break;

                            case 1005:
                                $( modal_auth   ).removeClass('d-none');
                                $( modal_valid  ).addClass('d-none');
                                $( error_auth   ).removeClass( 'd-none' );
                                // exibe erro
                                $( error_auth   ).html( 'Quantidade de tentativas excedida, tente novamente em 15min.' );
                                $( '#cellphone' ).addClass( 'error-border' );
                            case 1004:
                            case 1008:
                            default:
                                $( modal_auth   ).removeClass('d-none');
                                $( modal_valid  ).addClass('d-none');
                                $( error_auth   ).removeClass( 'd-none' );
                                // exibe erro
                                $( error_auth   ).html( 'Algo de erro, tente novamente.' );
                                $( '#cellphone' ).addClass( 'error-border' );
                                break;
                        }
                    }
                    else
                    {
                        $( '#btn-triibo-login'     ).prop( 'disabled', 'disabled' ).find( 'div' ).removeClass( 'd-none' );

                        $( '#modal-load-next-page' ).addClass( 'd-flex' );

                        $( modal_valid             ).addClass( 'd-none' );

                        // executa o ajax para recuperar as informações do usuário
                        callUserInfo();
                    }
                },
                error: function( data ) {
                    console.log( data );

                    $( btn_auth    ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                    $( btn_valid   ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );

                    $( modal_valid ).addClass('d-none');
                    $( modal_auth  ).removeClass('d-none');

                    $( error_auth  ).removeClass( 'd-none' );
                    // exibe erro generico, caso problema na chamada
                    $( error_auth  ).html( genericError );
                }
            });
        }


        /**
         * Função de recuperação de dados dos usuário e redirecionamento
         */
        window.callUserInfo = function() {
            $.ajax({
                url : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action    : 'userInfo_v1',
                    cellphone : cellphone,
                },
                success: function( data ) {
                    // console.log( JSON.parse( data ) );

                    let user = JSON.parse( data );

                    // valida se o retorno da função tem erros
                    if ( user.error != null )
                    {
                        $( '#btn-triibo-login'     ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );

                        $( '#modal-load-next-page' ).removeClass( 'd-flex' );

                        $( btn_auth     ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                        $( modal_auth   ).removeClass( 'd-none' );

                        $( error_auth   ).removeClass( 'd-none' );
                        // exibe erro
                        $( error_auth   ).html( 'Número não cadastrado no Triibo!' );
                        $( '#cellphone' ).addClass( 'error-border' );
                    }
                    else
                    {
                        // recarrega a página com o login efetuado
                        window.location.reload();
                    }
                },
                error: function( data ) {
                    console.log( data );

                    $( '#btn-triibo-login'     ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                    $( '#modal-load-next-page' ).removeClass( 'd-flex' );

                    // habilita botoes modais
                    $( btn_auth    ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                    $( btn_valid   ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );

                    // esconde modais
                    $( modal_valid ).addClass( 'd-none' );
                    $( modal_auth  ).addClass( 'd-none' );

                    $( error_login ).removeClass( 'd-none' );
                    // exibe erro generico, caso problema na chamada
                    $( error_login ).html( genericError );
                }
            });
        }

    </script>
