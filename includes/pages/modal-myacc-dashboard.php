
    <div id="modal-form-auth-logged" class="bg-modal-lbc <?php echo ( WC_Login_Before_Checkout::has_query_param() ) ? "" : "d-none"; ?>">
        <div class="bg-modal-login-child">
            <img id="tal_logo" style="width:40%;vertical-align:middle;" alt="" loading="lazy" class="mx-auto" src="<?php echo apply_filters( "martfury_site_logo", martfury_get_option( "logo" )); ?>" >
            <div class="modal-header" >
                <h5 style="font-weight:400 !important; font-size: 15px !important;" >
                    <?php echo WC_Login_Before_Checkout::get_disclaimer( true ); ?>
                </h5>
            </div>
            <!-- <div class="modal-header" >
                <h5>Informe seu celular para continuar:</h5>
            </div> -->
            <form id="form-auth-logged" method="post">
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-0" >
                    <input type="hidden" name="form-auth-logged">
                    <input id="cellphone-logged" type="text" class="woocommerce-Input woocommerce-Input--text input-text"
                        name="cellphone" placeholder="+55 (XX) XXXXX-XXXX" maxlength="19">
                    <!-- Error message -->
                    <p id="error-auth-logged" class="bg-transp text-center text-danger pt-8 mb-0 d-none" ></p>
                </p>
                <p></p>
                <button id="btn-form-auth-logged" type="submit" class="woocommerce-Button button set-spin">Solicitar código <div class="d-none spin-load"></div></button>
            </form>

            <a class="float-left mt-20" href="javascript:history.go(-1)">Retornar para o site</a>
        </div>
    </div>

    <div id="modal-form-validate" class="bg-modal-login d-none">
        <div class="bg-modal-login-child">
            <div class="modal-header">
                <h2>Entre com o código</h2>
            </div>
            <form id="form-validate" method="post" >
                <p class="text-left mb-15" >Enviamos um SMS com seu código de acesso, informe-o abaixo.</p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-0">
                    <input type="hidden" name="form-validate">
                    <input id="code" type="text" class="woocommerce-Input woocommerce-Input--text input-text" maxlength="6"
                           name="code" placeholder="Código" pattern="[0-9]{6}" title="O código tem seis (6) números" required autofocus />

                    <!-- Error message -->
                    <p id="error-valid" class="bg-transp text-center text-danger pt-8 mb-0 d-none" ></p>
                    <p id="code-resend" class="bg-transp float-left text-dark pb-20 mt-20 mb-0 d-none" >Código reenviado</p>
                    <a id="resend"      class="bg-transp float-right pointer pb-20 mt-20 mb-0" onclick="resendCode()" >Reenviar código</a>
                </p>
                <button id="btn-form-validate" type="submit" class="woocommerce-Button button set-spin" >Validar <div class="d-none spin-load"></div></button>
            </form>
        </div>
    </div>

    <div id="modal-form-load" class="bg-modal-login d-none">
        <div class="bg-modal-form-load-child">
            <div class="spin-load-lg"></div>
        </div>
    </div>

    <?php
        if ( WC_Login_Before_Checkout::has_query_param() )
        {
    ?>
        <style>
            html, body {
                margin:   0      !important;
                height:   100%   !important;
                overflow: hidden !important;
            }
        </style>
    <?php
        }
    ?>
