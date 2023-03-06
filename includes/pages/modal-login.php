
    <div id="modal-form-validate" class="bg-modal-login d-none">
        <div class="bg-modal-login-child">
            <div class="modal-header">
                <h2>Entre com o código</h2>

                <?php
                    if ( class_exists( "WC_Login_Before_Checkout" ) && ! WC_Login_Before_Checkout::has_query_param() )
                    {
                        echo "<span id=\"close-form-validate\" aria-hidden=\"true\">&times;</span>";
                    }
                ?>
            </div>
            <form id="form-validate" method="post" >
                <p class="text-left mb-15" >Enviamos um SMS com seu código de acesso, informe-o abaixo.</p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-0">
                    <input type="hidden" name="form-validate">
                    <input id="code" type="text" class="woocommerce-Input woocommerce-Input--text input-text" maxlength="6"
                           name="code" placeholder="Código" pattern="[0-9]{6}" title="O código tem seis (6) números" required />

                    <!-- Error message -->
                    <p id="error-valid"  class="bg-transp text-center text-danger pt-8 mb-0 d-none" ></p>
                    <p id="code-resend"  class="bg-transp float-left text-dark pb-20 mt-20 mb-0 d-none" >Código reenviado</p>
                    <a id="resend"       class="bg-transp float-right pointer pb-20 mt-20 mb-0 d-none" onclick="resendCode()" >Reenviar código</a>
                    <p id="resend-count" class="bg-transp float-right pb-20 mt-20 mb-0" >Reenviar código em 40</p>
                </p>
                <button id="btn-form-validate" type="submit" class="woocommerce-Button button set-spin" >Entrar <div class="d-none spin-load"></div></button>
            </form>
        </div>
    </div>

    <div id="modal-form-validate-email" class="bg-modal-login d-none">
        <div class="bg-modal-login-child">
            <div class="modal-header">
                <h2>Entre com o código</h2>

                <?php
                    if ( class_exists( "WC_Login_Before_Checkout" ) && ! WC_Login_Before_Checkout::has_query_param() )
                    {
                        echo "<span id=\"close-form-validate-email\" aria-hidden=\"true\">&times;</span>";
                    }
                ?>
            </div>
            <form id="form-validate-email" method="post" >
                <p class="text-left mb-15" >Enviamos um e-mail com seu código de acesso, informe-o abaixo.</p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-0">
                    <input type="hidden" name="form-validate-email">
                    <input id="code-email" type="text" class="woocommerce-Input woocommerce-Input--text input-text" maxlength="6"
                           name="code" placeholder="Código" pattern="[0-9]{6}" title="O código tem seis (6) números" required />

                    <!-- Error message -->
                    <p id="error-valid-email" class="bg-transp text-center text-danger pt-8 mb-0 d-none" ></p>
                    <p id="code-resend-email" class="bg-transp float-left text-dark pb-20 mt-20 mb-0 d-none" >Código reenviado</p>
                    <a id="resend-email"      class="bg-transp float-right pointer pb-20 mt-20 mb-0" onclick="resendCodeEmail()" >Reenviar código</a>
                </p>
                <button id="btn-form-validate-email" type="submit" class="woocommerce-Button button set-spin" >Entrar <div class="d-none spin-load"></div></button>
            </form>
        </div>
    </div>

    <div id="modal-form-register" class="bg-modal-login d-none">
        <div class="bg-modal-login-child">
            <div class="modal-header">
                <h2 id="register-title" ></h2>

                <?php
                    if ( class_exists( "WC_Login_Before_Checkout" ) && ! WC_Login_Before_Checkout::has_query_param() )
                    {
                        echo "<span id=\"close-form-register\" aria-hidden=\"true\">&times;</span>";
                    }
                ?>
            </div>
            <form id="form-register" method="post" >
                <p id="register-p1" class="text-left mb-5" ></p>
                <p id="register-p2" class="text-left mb-5" ></p>
                <p id="register-p3" class="text-left mb-15" ></p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <input type="hidden" name="form-register">
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" requered placeholder="Endereço de e-mail"
                        name="email" id="form-register-email" autocomplete="email" >
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" requered placeholder="Senha"
                        name="password" id="form-register-password" autocomplete="password" >
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-0">
                    <!-- Error message -->
                    <p id="error-register" class="bg-transp text-center text-danger pt-20 mb-0 d-none" ></p>
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mb-0">
                    <a id="lost-pass"   class="bg-transp float-right pointer pb-20 mb-0" onclick="closeModal()"
                        href="<?= esc_url( home_url( "/minha-conta/lost-password/" ) ); ?>" >Esqueceu a senha?</a>
                </p>

                <button id="btn-form-register" type="submit" class="woocommerce-Button button set-spin" >Entrar <div class="d-none spin-load"></div></button>
            </form>
        </div>
    </div>

    <?php do_action( "wc_login_before_checkout_add_modal" ); ?>

    <div id="modal-form-load" class="bg-modal-login d-none">
        <div class="bg-modal-form-load-child">
            <div class="spin-load-lg"></div>
        </div>
    </div>
