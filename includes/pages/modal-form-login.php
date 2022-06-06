
    <div id="modal-form-auth-lbc" class="bg-modal-lbc <?php echo ( WC_Login_Before_Checkout::has_query_param() ) ? "" : "d-none"; ?>">
        <div class="bg-modal-login-child">
            <img id="tal_logo" style="width:40%;vertical-align:middle;" alt="" loading="lazy" class="mx-auto" src="<?php echo apply_filters( "martfury_site_logo", martfury_get_option( "logo" )); ?>" >
            <div class="modal-header" >
                <h5 style="font-weight:400 !important; font-size: 15px !important;" >
                    <?php echo WC_Login_Before_Checkout::get_disclaimer(); ?>
                </h5>
            </div>
            <!-- <div class="modal-header" >
                <h5>Informe seu celular para continuar:</h5>
            </div> -->
            <form id="form-auth-lbc" method="post">
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-0" >
                    <input type="hidden" name="form-auth-lbc">
                    <input id="cellphone-lbc" type="text" class="woocommerce-Input woocommerce-Input--text input-text"
                        name="cellphone" placeholder="+55 (XX) XXXXX-XXXX" maxlength="19">
                    <!-- Error message -->
                    <p id="error-auth-lbc" class="bg-transp text-center text-danger pt-8 mb-0 d-none" ></p>
                </p>
                <p></p>
                <button id="btn-form-auth-lbc" type="submit" class="woocommerce-Button button set-spin">Solicitar código <div class="d-none spin-load"></div></button>
            </form>
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
