
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" ></script>
<script>
    jQuery( "#cellphone,#cellphone-lbc" ).mask( "+55 (00) 00000-0000" );
    jQuery( "#cellphone,#cellphone-lbc" ).blur( function( event ) {
        // Celular com 9 dígitos + 2 dígitos DDD e 8 da máscara
        if ( jQuery( this ).val().length == 19 )
            jQuery( "#cellphone,#cellphone-lbc" ).mask( "+55 (00) 00000-0009" );
        else
            jQuery( "#cellphone,#cellphone-lbc" ).mask( "+55 (00) 0000-00009" );
    });

    let genericError1  = "Ocorreu um erro, tente novamente.";
    let genericError2  = "Algo deu errado, tente novamente.";

    // vars parametros ajax
    let lbc           = null;
    let code          = null;
    let codeEmail     = null;
    let email         = null;
    let pass          = null;
    let cellphone     = null;
    let transactionId = null;


    // vars AUTH
    let error_auth    = document.getElementById( "error-auth"    );
    let btn_auth      = document.getElementById( "btn-form-auth" );

    // vars AUTH-lbc
    let modal_lbc      = document.getElementById( "modal-form-auth-lbc" );
    let error_auth_lbc = document.getElementById( "error-auth-lbc"      );
    let btn_auth_lbc   = document.getElementById( "btn-form-auth-lbc"   );


    // vars validate
    let modal_valid   = document.getElementById( "modal-form-validate" );
    let error_valid   = document.getElementById( "error-valid"         );
    let btn_valid     = document.getElementById( "btn-form-validate"   );


    // vars validate email
    let modal_v_email = document.getElementById( "modal-form-validate-email" );
    let error_v_email = document.getElementById( "error-valid-email"         );
    let btn_v_email   = document.getElementById( "btn-form-validate-email"   );


    // vars register
    let modal_regis   = document.getElementById( "modal-form-register" );
    let error_regis   = document.getElementById( "error-register"      );
    let btn_regis     = document.getElementById( "btn-form-register"   );


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
                window.submitAuth();
            break;

            case "btn-form-auth-lbc":
                e.preventDefault();
                lbc = true;
                window.submitAuth();
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
     * Reenvia codigo por sms
     */
    window.resendCode = function ()
    {
        jQuery( btn_auth ).click();

        disableBtns();

        jQuery( error_valid    ).addClass( "d-none" );
        jQuery( "#code"        ).removeClass( "error-border" );
        jQuery( "#code-resend" ).removeClass( "d-none" );

        setTimeout( function() {
            enableBtns();
            jQuery( "#code-resend" ).addClass( "d-none" );
        }, 3000 );
    }

    /**
     * Reenvia codigo por email
     */
    window.resendCodeEmail = function ()
    {
        disableBtns();

        jQuery( error_v_email        ).addClass( "d-none" );
        jQuery( "#code-email"        ).removeClass( "error-border" );

        email = jQuery( "#form-register-email" ).val();

        jQuery.ajax({
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
                    jQuery( error_v_email ).removeClass( "d-none" ).html( genericError1 );
                }
                else
                {
                    jQuery( "#code-email" ).focus();
                    jQuery( "#code-resend-email" ).removeClass( "d-none" );

                    setTimeout( function() {
                        jQuery( "#code-resend-email" ).addClass( "d-none" );
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
        enableBtns();
        hideModals();
        hideErros();
    }


    /**
     * Valida celular e chama função para enviar código por SMS
     */
    window.submitAuth = function()
    {
        disableBtns();

        let id  = "";
        let err = "";

        if ( lbc )
        {
            id  = "#cellphone-lbc";
            err = error_auth_lbc;
        }
        else
        {
            id  = "#cellphone";
            err = error_auth;
        }

        cellphone = jQuery( id ).val();

        if ( cellphone != "" )
        {
            jQuery( err ).addClass( "d-none" );
            jQuery( id  ).removeClass( "error-border" );

            // envia o código por sms
            callAuthAjax();
        }
        else
        {
            enableBtns();
            jQuery( err ).removeClass( "d-none" ).html( "Insira um número de celular." );
        }
    }


    /**
     * Envia código por sms e abre modal de validação
     */
    window.callAuthAjax = function ()
    {
        let id  = "";
        let err = "";

        if ( lbc )
        {
            id  = "#cellphone-lbc";
            err = error_auth_lbc;
        }
        else
        {
            id  = "#cellphone";
            err = error_auth;
        }

        jQuery.ajax({
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
                        jQuery( id ).addClass( "error-border" );
                    }
                    else
                    {
                        errorMsg = genericError2;
                    }

                    jQuery( err ).removeClass( "d-none" ).html( errorMsg );
                }
                else
                {
                    transactionId = result.success.transactionId;

                    if ( lbc )
                    {
                        jQuery( modal_valid ).addClass( "bg-modal-white" );
                        jQuery( modal_lbc   ).addClass( "d-none"         );
                    }

                    jQuery( modal_valid ).removeClass( "d-none" );
                    jQuery( "#code" ).focus();
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

        code = jQuery( "#code" ).val();

        if ( code != "" && code.length == 6 )
        {
            jQuery( error_valid ).addClass( "d-none" );
            jQuery( "#code" ).removeClass( "error-border" );

            // valida código informado
            callValidateSMS();
        }
        else
        {
            enableBtns();
            errorMsg = ( code == "" ) ? "Insira código recebido." : "O código tem 6 dígitos." ;
            jQuery( error_valid ).removeClass( "d-none" ).html( errorMsg );
        }
    }

    /**
     * Valida o código do sms e chama função que busca o usuário na Triibo e no Mkt
     */
    window.callValidateSMS = function ()
    {
        if ( lbc )
        {
            id  = "#cellphone-lbc";
            err = error_auth_lbc;
        }
        else
        {
            id  = "#cellphone";
            err = error_auth;
        }

        jQuery.ajax({
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
                            jQuery( error_valid ).removeClass( "d-none" ).html( "Código inválido." );
                            jQuery( "#code"     ).addClass( "error-border" );
                        break;

                        case 1007:
                            jQuery( error_valid ).removeClass( "d-none" ).html( "Código expirado." );
                            jQuery( "#code"     ).addClass( "error-border" );
                        break;

                        case 1009:
                            jQuery( modal_valid ).addClass( "d-none" );
                            jQuery( err         ).removeClass( "d-none" ).html( "Código expirado." );
                            jQuery( id          ).addClass( "error-border" );
                        break;

                        case 1005:
                            jQuery( modal_valid ).addClass( "d-none" );
                            jQuery( err         ).removeClass( "d-none" ).html( "Quantidade de tentativas excedida, tente novamente em 15min." );
                            jQuery( id          ).addClass( "error-border" );
                        break;

                        case 1004:
                        case 1008:
                        default:
                            jQuery( modal_valid ).addClass( "d-none" );
                            jQuery( err         ).removeClass( "d-none" ).html( "Algo de erro, tente novamente." );
                        break;
                    }
                }
                else
                {
                    disableBtns();

                    jQuery( "#modal-form-load" ).removeClass( "d-none" );

                    // busca usuário
                    callUserInfo();
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
        jQuery.ajax({
            url : "/wp-admin/admin-ajax.php",
            type: "POST",
            data: {
                action    : "TAL_user_info",
                cellphone : cellphone,
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
                            jQuery( "#modal-load-next-page" ).addClass( "bgml-d-none" );
                            jQuery( modal_valid ).addClass( "d-none" );
                            jQuery( modal_regis ).removeClass( "d-none" );

                            let t1 = "Complete seu cadastro";
                            let p1 = "Notamos que sua conta Triibo ainda não está associada a uma conta Marketplace.";
                            setRegistertexts( t1, p1 );
                        break;

                        // não encontrou número na Triibo
                        case 404:
                            jQuery( "#modal-load-next-page" ).addClass( "bgml-d-none" );
                            jQuery( modal_valid ).addClass( "d-none" );
                            jQuery( modal_regis ).removeClass( "d-none" );

                            let t4 = "Complete seu cadastro";
                            let p4 = "Notamos que seu número ainda não está associado a uma conta Triibo.";
                            setRegistertexts( t4, p4 );
                        break;

                        // erro na consulta a Triibo
                        case 500:
                        default:
                            jQuery( "#modal-load-next-page" ).addClass( "bgml-d-none" );
                            jQuery( modal_valid ).addClass( "d-none" );
                            jQuery( error_auth  ).removeClass( "d-none" ).html( genericError1 );
                        break;
                    }
                }
                else
                {
                    disableBtns();
                    hideModals();
                    jQuery( "#modal-load-next-page" ).removeClass( "bgml-d-none" );
                    window.location.reload();
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

        email = jQuery( "#form-register-email" ).val();
        pass  = jQuery( "#form-register-password" ).val();

        if ( email != "" && ( pass != "" && pass.length >= 8 ) )
        {
            jQuery( error_regis ).addClass( "d-none" );
            jQuery( "#form-register-email"    ).removeClass( "error-border" );
            jQuery( "#form-register-password" ).removeClass( "error-border" );
            jQuery( "#modal-load-next-page"   ).removeClass( "bgml-d-none" );

            // Busca usuário
            callFindUser();
        }
        else
        {
            enableBtns();
            jQuery( error_regis ).removeClass( "d-none" ).html( "E-mail/Senha não podem ser em branco. A senha deve ter no mínimo 8 caracteres." );
        }
    }

    /**
     * Verifica se email existe no Mkt.
     * Caso não exista, envia código por email, abre modal de validação de código.
     * Caso exista, valida senha. Se estiver correta, cria usuário na Triibo, se não, exibe erro.
     */
    window.callFindUser = function()
    {
        jQuery.ajax({
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
                    jQuery( "#modal-load-next-page" ).addClass( "bgml-d-none" );

                    switch ( result.error.code )
                    {
                        // email não cadastrado no marketplace
                        case 301:
                            jQuery( modal_regis   ).addClass( "d-none" );
                            jQuery( modal_v_email ).removeClass( "d-none" );
                            jQuery( "#code-email" ).focus();
                        break;

                        // email/senha não conferem
                        case 401:
                        default:
                            jQuery( error_regis ).removeClass( "d-none" ).html( "E-mail e/ou senha incorreta" );
                            jQuery( "#form-register-email"    ).addClass( "error-border" );
                            jQuery( "#form-register-password" ).addClass( "error-border" );
                        break;
                    }
                }
                else
                {
                    disableBtns();
                    hideModals();
                    jQuery( "#modal-load-next-page" ).removeClass( "bgml-d-none" );
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

        codeEmail = jQuery( "#code-email" ).val();

        if ( code != "" && code.length == 6 )
        {
            jQuery( error_v_email ).addClass( "d-none" );
            jQuery( "#code-email" ).removeClass( "error-border" );

            // valida código informado
            callValidateEmail();
        }
        else
        {
            enableBtns();

            errorMsg = ( codeEmail == "" ) ? "Insira código recebido." : "O código tem 6 dígitos." ;
            jQuery( error_v_email ).removeClass( "d-none" ).html( errorMsg );
        }
    }

    /**
     * Valida o código do email.
     * Caso valide, cria conta na Triibo e no Mkt, e redireciona para o página do perfil do usuário
     * Caso não, exibe erro.
     */
        window.callValidateEmail = function ()
    {
        jQuery.ajax({
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
                            jQuery( error_v_email ).removeClass( "d-none" ).html( "Código inválido." );
                            jQuery( "#code-email" ).addClass( "error-border" );
                        break;

                        // erro interno
                        case 500:
                        default:
                            jQuery( modal_v_email ).addClass("d-none");
                            jQuery( error_auth    ).removeClass( "d-none" ).html( genericError2 );
                        break;
                    }
                }
                else
                {
                    disableBtns();
                    hideModals();
                    jQuery( "#modal-load-next-page" ).removeClass( "bgml-d-none" );
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
        jQuery( btn_auth     ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        jQuery( btn_auth_lbc ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        jQuery( btn_valid    ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        jQuery( btn_v_email  ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
        jQuery( btn_regis    ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "d-none" );
    }

    /**
     * Habilita os botões dos modais
     */
    window.enableBtns = function ()
    {
        jQuery( btn_auth     ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        jQuery( btn_auth_lbc ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        jQuery( btn_valid    ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        jQuery( btn_v_email  ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
        jQuery( btn_regis    ).prop( "disabled", null ).find( "div" ).addClass( "d-none" );
    }

    /**
     * Esconde os modais
     */
    window.hideModals = function ()
    {
        jQuery( modal_valid   ).addClass( "d-none" );
        jQuery( modal_v_email ).addClass( "d-none" );
        jQuery( modal_regis   ).addClass( "d-none" );
    }

    /**
     * Esconde os pagagrafos de erros
     */
    window.hideErros = function ()
    {
        jQuery( error_auth    ).addClass( "d-none" ).html( "" );
        jQuery( error_valid   ).addClass( "d-none" ).html( "" );
        jQuery( error_v_email ).addClass( "d-none" ).html( "" );
        jQuery( error_regis   ).addClass( "d-none" ).html( "" );
    }

    /**
     * Exibe erro no console quando há falha no ajax.
     */
    window.ajaxError = function ( err )
    {
        console.log( err );
        hideModals();
        enableBtns();
        jQuery( "#modal-load-next-page" ).addClass( "bgml-d-none" );
        jQuery( error_auth ).removeClass( "d-none" ).html( genericError1 );
    }

    window.setRegistertexts = function ( t, p )
    {
        jQuery( "#register-title" ).html( t );
        jQuery( "#register-p1"    ).html( p );
        jQuery( "#register-p2"    ).html( "Informe o e-mail e a senha da sua conta Marketplace para completarmos seu cadastro." );
        jQuery( "#register-p3"    ).html( "Caso não tenha uma conta no Marketplace, criaremos uma para você." );
    }

</script>
