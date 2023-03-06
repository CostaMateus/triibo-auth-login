
( function ( $ ) {

    "use strict";

    $( "#cellphone" ).mask( "+55 (00) 00000-0000" );
    $( "#cellphone" ).blur( function( event ) {
        // Celular com 9 dígitos + 2 dígitos DDD e 8 da máscara
        if ( $( this ).val().length == 19 )
            $( "#cellphone" ).mask( "+55 (00) 00000-0009" );
        else
            $( "#cellphone" ).mask( "+55 (00) 0000-00009" );
    });

    $( "#cellphone-lbc" ).mask( "+55 (00) 00000-0000" );
    $( "#cellphone-lbc" ).blur( function( event ) {
        // Celular com 9 dígitos + 2 dígitos DDD e 8 da máscara
        if ( $( this ).val().length == 19 )
            $( "#cellphone-lbc" ).mask( "+55 (00) 00000-0009" );
        else
            $( "#cellphone-lbc" ).mask( "+55 (00) 0000-00009" );
    });

    $( "#cellphone-logged" ).mask( "+55 (00) 00000-0000" );
    $( "#cellphone-logged" ).blur( function( event ) {
        // Celular com 9 dígitos + 2 dígitos DDD e 8 da máscara
        if ( $( this ).val().length == 19 )
            $( "#cellphone-logged" ).mask( "+55 (00) 00000-0009" );
        else
            $( "#cellphone-logged" ).mask( "+55 (00) 0000-00009" );
    });

    let genericError1  = "Ocorreu um erro, tente novamente.";
    let genericError2  = "Algo deu errado, tente novamente.";

    // vars parametros ajax
    let url_red       = TAL_script_.url_red;
    let lbc           = false;
    let logged        = false;
    let code          = null;
    let codeEmail     = null;
    let email         = null;
    let pass          = null;
    let cellphone     = null;
    let transactionId = null;


    // vars AUTH
    let error_auth     = document.getElementById( "error-auth"    );
    let btn_auth       = document.getElementById( "btn-form-auth" );


    // vars AUTH-LBC
    let modal_lbc      = document.getElementById( "modal-form-auth-lbc" );
    let error_auth_lbc = document.getElementById( "error-auth-lbc"      );
    let btn_auth_lbc   = document.getElementById( "btn-form-auth-lbc"   );


    // vars AUTH-LOGGED
    let modal_logged   = document.getElementById( "modal-form-auth-logged" );
    let error_logged   = document.getElementById( "error-auth-logged"      );
    let btn_logged     = document.getElementById( "btn-form-auth-logged"   );


    // vars validate
    let modal_valid    = document.getElementById( "modal-form-validate" );
    let error_valid    = document.getElementById( "error-valid"         );
    let btn_valid      = document.getElementById( "btn-form-validate"   );


    // vars validate email
    let modal_v_email  = document.getElementById( "modal-form-validate-email" );
    let error_v_email  = document.getElementById( "error-valid-email"         );
    let btn_v_email    = document.getElementById( "btn-form-validate-email"   );


    // vars register
    let modal_regis    = document.getElementById( "modal-form-register" );
    let error_regis    = document.getElementById( "error-register"      );
    let btn_regis      = document.getElementById( "btn-form-register"   );

    let cd             = 0;
    let intervalId     = null;

    /**
     * Monitora todos os clicks na página
     * Verifica IDs especificos
     */
    window.addEventListener( "click", function ( e ) {
        switch ( e.target.id )
        {
            case "close-form-auth":
            case "close-form-validate":
            case "close-form-validate-email":
            case "close-form-register":
                window.closeModal();
            break;

            case "btn-form-auth":
                e.preventDefault();
                window.submitAuth( "#cellphone", error_auth );
            break;

            case "btn-form-auth-lbc":
                e.preventDefault();
                lbc = true;
                window.submitAuth( "#cellphone-lbc", error_auth_lbc );
            break;

            case "btn-form-auth-logged":
                e.preventDefault();
                logged = true;
                window.submitAuth( "#cellphone-logged", error_logged );
            break;

            case "btn-form-validate":
                e.preventDefault();
                window.submitValidate();
            break;

            case "btn-form-validate-email":
                e.preventDefault();
                window.submitValidateEmail();
            break;

            case "btn-form-register":
                e.preventDefault();
                window.submitRegister();
            break;
        }
    }, false);

    /**
     * Contagem regressiva para reenviar código
     */
    window.countdown = function()
    {
        if ( cd == 0 )
        {
            clearInterval( intervalId );
            $( "#resend-count" ).addClass( "d-none" );
            $( "#resend"       ).removeClass( "d-none" );
            $( "#resend-count" ).html( "Reenviar código em 40" );
        }
        else
        {
            $( "#resend-count" ).html( `Reenviar código em ${cd}` );
            cd -= 1;
        }
    }

    /**
     * Reenvia codigo por sms
     */
    window.resendCode = function ()
    {
        $( "#resend" ).addClass( "d-none" );

        if ( lbc && ! logged )
        {
            $( btn_auth_lbc ).click();
        }
        else if ( ! lbc && logged )
        {
            $( btn_logged ).click();
        }
        else
        {
            $( btn_auth ).click();
        }

        disableBtns();

        $( error_valid    ).addClass( "d-none" );
        $( "#code"        ).removeClass( "error-border" );
        $( "#code-resend" ).removeClass( "d-none" );

        setTimeout( function() {
            enableBtns();
            $( "#code-resend" ).addClass( "d-none" );
        }, 3000 );
    }

    /**
     * Reenvia codigo por email
     */
    window.resendCodeEmail = function ()
    {
        disableBtns();

        $( error_v_email        ).addClass( "d-none" );
        $( "#code-email"        ).removeClass( "error-border" );

        email = $( "#form-register-email" ).val();

        $.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action : "TAL_resend_email_code",
                email  : email
            },
            success: function( data ) {
                let result = JSON.parse( data );

                enableBtns();

                if ( result.error != null)
                {
                    $( error_v_email ).removeClass( "d-none" ).html( genericError1 );
                }
                else
                {
                    $( "#code-email" ).focus();
                    $( "#code-resend-email" ).removeClass( "d-none" );

                    setTimeout( function() {
                        $( "#code-resend-email" ).addClass( "d-none" );
                    }, 3000 );
                }
            },
            error: function( err ) {
                ajaxError( err );
            }
        });
    }

    /**
     * Fecha modais
     */
    window.closeModal = function()
    {
        if ( ! lbc && ! logged )
        {
            enableBtns();
            hideModals();
            hideErros();
        }
    }


    /**
     * Valida celular e chama função para enviar código por SMS
     */
    window.submitAuth = function( id, err )
    {
        disableBtns();

        cellphone = $( id ).val();

        if ( cellphone != "" )
        {
            if ( cellphone.length == 19 || cellphone.length == 18 )
            {
                $( err ).addClass( "d-none" );
                $( id  ).removeClass( "error-border" );

                // envia o código por sms
                callAuthAjax( id, err );
            }
            else
            {
                enableBtns();
                $( err ).removeClass( "d-none" ).html( "Insira um número válido." );
            }
        }
        else
        {
            enableBtns();
            $( err ).removeClass( "d-none" ).html( "Insira um número de celular." );
        }
    }


    /**
     * Envia código por sms e abre modal de validação
     */
    window.callAuthAjax = function ( id, err )
    {
        $.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action    : "TAL_auth_sms",
                cellphone : cellphone
            },
            success: function( data ) {
                let result = JSON.parse( data );

                enableBtns();

                if ( result.error != null)
                {
                    let errorMsg = "";

                    if ( result.error.errorCode == 1005 )
                    {
                        errorMsg = "Quantidade de tentativas excedida, tente novamente em 15min.";
                        $( id ).addClass( "error-border" );
                    }
                    else
                    {
                        errorMsg = genericError2;
                    }

                    $( err ).removeClass( "d-none" ).html( errorMsg );
                }
                else
                {
                    transactionId = result.success.transactionId;

                    if ( lbc || logged )
                        $( modal_valid ).addClass( "bg-modal-white" );
                    else
                        $( modal_valid ).removeClass( "bg-modal-white" );

                    if ( lbc    ) $( modal_lbc    ).addClass( "d-none" );
                    if ( logged ) $( modal_logged ).addClass( "d-none" );

                    $( modal_valid ).removeClass( "d-none" );
                    $( "#code" ).focus();

                    cd         = 40;
                    intervalId = setInterval( countdown, 1000 );
                    $( "#resend-count" ).removeClass( "d-none" );
                }
            },
            error: function( err ) {
                ajaxError( err );
            }
        });
    }


    /**
     * Valida se foi informado um código SMS e chama função de validação do código SMS
     */
    window.submitValidate = function()
    {
        disableBtns();

        code = $( "#code" ).val();

        if ( code != "" && code.length == 6 )
        {
            $( error_valid ).addClass( "d-none" );
            $( "#code" ).removeClass( "error-border" );

            // valida código informado
            callValidateSMS();
        }
        else
        {
            enableBtns();
            errorMsg = ( code == "" ) ? "Insira código recebido." : "O código tem 6 dígitos." ;
            $( error_valid ).removeClass( "d-none" ).html( errorMsg );
        }
    }

    /**
     * Valida o código do sms e chama função que busca o usuário na Triibo e no Mkt
     */
    window.callValidateSMS = function ()
    {
        let id  = null;
        let err = null;

        if ( lbc && ! logged )
        {
            id  = "#cellphone-lbc";
            err = error_auth_lbc;
        }
        else if ( ! lbc && logged )
        {
            id  = "#cellphone-logged";
            err = error_logged;
        }
        else
        {
            id  = "#cellphone";
            err = error_auth;
        }

        $.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action        : "TAL_validate_sms",
                code          : code,
                cellphone     : cellphone,
                transactionId : transactionId
            },
            success: function( data ) {
                let result = JSON.parse( data );

                if ( result.error != null )
                {
                    enableBtns();

                    switch ( result.error.errorCode )
                    {
                        case 1006:
                            $( error_valid ).removeClass( "d-none" ).html( "Código inválido." );
                            $( "#code"     ).addClass( "error-border" );
                        break;

                        case 1007:
                            $( error_valid ).removeClass( "d-none" ).html( "Código expirado." );
                            $( "#code"     ).addClass( "error-border" );
                        break;

                        case 1009:
                            $( modal_valid ).addClass( "d-none" );
                            $( err         ).removeClass( "d-none" ).html( "Código expirado." );
                            $( id          ).addClass( "error-border" );
                        break;

                        case 1005:
                            $( modal_valid ).addClass( "d-none" );
                            $( err         ).removeClass( "d-none" ).html( "Quantidade de tentativas excedida, tente novamente em 15min." );
                            $( id          ).addClass( "error-border" );
                        break;

                        case 1004:
                        case 1008:
                        default:
                            $( modal_valid ).addClass( "d-none" );
                            $( err         ).removeClass( "d-none" ).html( "Algo de erro, tente novamente." );
                        break;
                    }
                }
                else
                {
                    disableBtns();

                    $( "#modal-form-load" ).removeClass( "d-none" );

                    if ( logged )
                    {
                        // add cellphone ao meta triiboId_phone
                        callAddTriiboId();
                    }
                    else
                    {
                        // busca usuário
                        callUserInfo();
                    }

                }
            },
            error: function( err ) {
                ajaxError( err );
            }
        });
    }

    /**
     * Busca usuário na Triibo.
     * Caso não exista, abre modal de email e senha.
     * Caso exista, ??? // redireciona para página do perfil.
     */
    window.callUserInfo = function()
    {
        $.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action    : "TAL_user_info",
                cellphone : cellphone,
                lbc       : lbc,
            },
            success: function( data ) {
                let result = JSON.parse( data );

                if ( result.error != null )
                {
                    enableBtns();

                    switch ( result.error.code )
                    {
                        // tem conta na Triibo, mas não tem conta mkt
                        case 401:
                            let t1 = "Complete seu cadastro";
                            let p1 = "Notamos que sua conta Triibo ainda não está associada a uma conta Marketplace.";
                            setRegistertexts( t1, p1 );

                            $( "#modal-form-load" ).addClass( "d-none" );
                            $( modal_valid ).addClass( "d-none" );
                            $( modal_regis ).removeClass( "d-none" );
                        break;

                        // não encontrou número na Triibo
                        case 404:
                            let t4 = "Complete seu cadastro";
                            let p4 = "Notamos que seu número ainda não está associado a uma conta Triibo.";
                            setRegistertexts( t4, p4 );

                            $( "#modal-form-load" ).addClass( "d-none" );
                            $( modal_valid ).addClass( "d-none" );
                            $( modal_regis ).removeClass( "d-none" );
                        break;

                        // erro na consulta a Triibo
                        case 500:
                        default:
                            $( "#modal-form-load" ).addClass( "d-none" );
                            $( modal_valid ).addClass( "d-none" );

                            var err = lbc ? error_auth_lbc : error_auth;

                            $( err ).removeClass( "d-none" ).html( genericError1 );
                        break;
                    }
                }
                else
                {
                    disableBtns();
                    hideModals();
                    $( "#modal-form-load" ).addClass( "bg-modal-white" ).removeClass( "d-none" );

                    if ( lbc )
                        window.location.href = url_red;
                    else
                        window.location.reload();
                }
            },
            error: function( err ) {
                ajaxError( err );
            }
        });
    }

    /**
     *
     */
    window.callAddTriiboId = function ()
    {
        $.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action    : "TAL_add_triiboid",
                cellphone : cellphone
            },
            success: function( data ) {
                let result = JSON.parse( data );

                if ( result.error != null )
                {
                    enableBtns();

                    $( "#modal-form-load" ).addClass( "d-none" );

                    console.log( result );
                }
                else
                {
                    disableBtns();
                    hideModals();

                    $( "#modal-form-load" ).addClass( "bg-modal-white" ).removeClass( "d-none" );

                    window.location.href = url_red;
                }
            },
            error: function( err ) {
                ajaxError( err );
            }
        });

    }


    /**
     * Verifica email e senha informados, chama função de validação conta Mkt
     */
    window.submitRegister = function()
    {
        disableBtns();

        email = $( "#form-register-email" ).val();
        pass  = $( "#form-register-password" ).val();

        if ( email != "" && ( pass != "" && pass.length >= 8 ) )
        {
            $( error_regis ).addClass( "d-none" );
            $( "#form-register-email"    ).removeClass( "error-border" );
            $( "#form-register-password" ).removeClass( "error-border" );
            // $( "#modal-form-load"   ).removeClass( "d-none" );

            // Busca usuário
            callFindUser();
        }
        else
        {
            enableBtns();
            $( error_regis ).removeClass( "d-none" ).html( "E-mail/Senha não podem ser em branco. A senha deve ter no mínimo 8 caracteres." );
        }
    }

    /**
     * Verifica se email existe no Mkt.
     * Caso não exista, envia código por email, abre modal de validação de código.
     * Caso exista, valida senha. Se estiver correta, cria usuário na Triibo, se não, exibe erro.
     */
    window.callFindUser = function()
    {
        $.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action    : "TAL_find_user",
                cellphone : cellphone,
                email     : email,
                password  : pass,
            },
            success: function( data ) {
                let result = JSON.parse( data );

                enableBtns();

                if ( result.error != null )
                {
                    $( "#modal-form-load" ).addClass( "d-none" );

                    switch ( result.error.code )
                    {
                        // email não cadastrado no marketplace
                        case 301:
                            $( modal_regis   ).addClass( "d-none" );
                            $( modal_v_email ).removeClass( "d-none" );
                            $( "#code-email" ).focus();
                        break;

                        // email/senha não conferem
                        case 401:
                        default:
                            $( error_regis ).removeClass( "d-none" ).html( "E-mail e/ou senha incorreta" );
                            $( "#form-register-email"    ).addClass( "error-border" );
                            $( "#form-register-password" ).addClass( "error-border" );
                        break;
                    }
                }
                else
                {
                    $( "#modal-form-load" ).addClass( "bg-modal-white" ).removeClass( "d-none" );

                    hideModals();
                    disableBtns();

                    if ( lbc )
                        window.location.href = url_red;
                    else
                        window.location.reload();
                }
            },
            error: function( err ) {
                ajaxError( err );
            }
        });
    }


    /**
     * Valida se foi informado um código EMAIL e chama função de validação do código EMAIL
     */
    window.submitValidateEmail = function()
    {
        disableBtns();

        codeEmail = $( "#code-email" ).val();

        if ( code != "" && code.length == 6 )
        {
            $( error_v_email ).addClass( "d-none" );
            $( "#code-email" ).removeClass( "error-border" );

            // valida código informado
            callValidateEmail();
        }
        else
        {
            enableBtns();

            errorMsg = ( codeEmail == "" ) ? "Insira código recebido." : "O código tem 6 dígitos." ;
            $( error_v_email ).removeClass( "d-none" ).html( errorMsg );
        }
    }

    /**
     * Valida o código do email.
     * Caso valide, cria conta na Triibo e no Mkt, e redireciona para o página do perfil do usuário
     * Caso não, exibe erro.
     */
    window.callValidateEmail = function ()
    {
        $.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action    : "TAL_validate_code_email",
                codeEmail : codeEmail,
                cellphone : cellphone,
                email     : email,
                password  : pass
            },
            success: function( data ) {
                let result = JSON.parse( data );

                if ( result.error != null )
                {
                    enableBtns();

                    switch ( result.error.code )
                    {
                        // código informado incorreto
                        case 401:
                            $( error_v_email ).removeClass( "d-none" ).html( "Código inválido." );
                            $( "#code-email" ).addClass( "error-border" );
                        break;

                        // erro interno
                        case 500:
                        default:
                            $( modal_v_email ).addClass("d-none");
                            $( error_auth    ).removeClass( "d-none" ).html( genericError2 );
                        break;
                    }
                }
                else
                {
                    $( "#modal-form-load" ).addClass( "bg-modal-white" ).removeClass( "d-none" );

                    hideModals();
                    disableBtns();

                    if ( lbc )
                        window.location.href = url_red;
                    else
                        window.location.reload();
                }
            },
            error: function( err ) {
                ajaxError( err );
            }
        });
    }



    /**
     * Desabilita os botões dos modais
     */
    window.disableBtns = function ()
    {
        $( btn_auth     ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        $( btn_auth_lbc ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        $( btn_logged   ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );

        $( btn_valid    ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        $( btn_v_email  ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        $( btn_regis    ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
    }

    /**
     * Habilita os botões dos modais
     */
    window.enableBtns = function ()
    {
        $( btn_auth     ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        $( btn_auth_lbc ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        $( btn_logged   ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );

        $( btn_valid    ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        $( btn_v_email  ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        $( btn_regis    ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
    }

    /**
     * Esconde os modais
     */
    window.hideModals = function ()
    {
        $( modal_lbc     ).addClass( "d-none" );
        $( modal_logged  ).addClass( "d-none" );
        $( modal_valid   ).addClass( "d-none" );
        $( modal_v_email ).addClass( "d-none" );
        $( modal_regis   ).addClass( "d-none" );
    }

    /**
     * Esconde os pagagrafos de erros
     */
    window.hideErros = function ()
    {
        $( error_auth     ).addClass( "d-none" ).html( "" );
        $( error_auth_lbc ).addClass( "d-none" ).html( "" );
        $( error_logged   ).addClass( "d-none" ).html( "" );
        $( error_valid    ).addClass( "d-none" ).html( "" );
        $( error_v_email  ).addClass( "d-none" ).html( "" );
        $( error_regis    ).addClass( "d-none" ).html( "" );
    }

    /**
     * Exibe erro no console quando há falha no ajax.
     */
    window.ajaxError = function ( err )
    {
        console.log( err );
        hideModals();
        enableBtns();
        $( "#modal-form-load" ).addClass( "d-none" );

        if ( lbc && ! logged )
        {
            $( error_auth_lbc ).removeClass( "d-none" ).html( genericError1 );
        }
        else if ( ! lbc && logged )
        {
            $( error_logged ).removeClass( "d-none" ).html( genericError1 );
        }
        else
        {
            $( error_auth ).removeClass( "d-none" ).html( genericError1 );
        }
    }

    window.setRegistertexts = function ( t, p )
    {
        $( "#register-title" ).html( t );
        $( "#register-p1"    ).html( p );
        $( "#register-p2"    ).html( "Informe o e-mail e a senha da sua conta Marketplace para completarmos seu cadastro." );
        $( "#register-p3"    ).html( "Caso não tenha uma conta no Marketplace, criaremos uma para você." );
    }

} )( jQuery );
