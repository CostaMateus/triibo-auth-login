<?php
    if ( get_transient( "_TAL_STATUS" ) == "active" )
    {
        $stt_txt = "ATIVO";
        $btn_msg = "Desativar login Triibo";
    }
    else
    {
        $stt_txt = "INATIVO";
        $btn_msg = "Ativar login Triibo";
    }
?>

<style>
    #msg-status        { margin-bottom: 8px !important; }
    #msg-status span   { font-weight: bold !important; }
    #btn-triibo-status { margin-bottom: 20px; }

    .d-none    { display: none !important; }
    .set-spin  { display: flex !important; justify-content: center; }
    .spin-load { width: 8px; height: 8px; margin: auto 5px; border: 3px solid #AEAEAE; border-top: 3px solid #0071A1; border-radius: 50%; -webkit-animation: spin 1s linear infinite; /* Safari */ animation: spin 1s linear infinite; }

    @-webkit-keyframes spin {
        0%   { -webkit-transform: rotate(0deg);   }
        100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes spin {
        0%   { transform: rotate(0deg);   }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    jQuery( '.notice, .error' ).css( 'display', 'none' );

    window.onload = function() {
        jQuery( '#btn-triibo-status' ).on( 'click', function ( e ) {

            jQuery( '#btn-triibo-status' ).prop( 'disabled', 'disabled' ).find( 'div' ).removeClass( 'd-none' );

            jQuery.ajax({
                url : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'TAL_set_status_global'
                },
                success: function( data ) {
                    switch ( data )
                    {
                        case 'active':
                            jQuery('#txt-triibo-status').html('ATIVO');
                            jQuery('#btn-triibo-status').html('Desativar login Triibo <div class="d-none spin-load">');
                        break;
                        case 'inactive':
                            jQuery('#txt-triibo-status').html('INATIVO');
                            jQuery('#btn-triibo-status').html('Ativar login Triibo <div class="d-none spin-load">');
                        break;
                    }

                    jQuery( '#btn-triibo-status' ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                },
                error: function( data ) {
                    console.log( data );
                    jQuery( '#btn-triibo-status' ).prop( 'disabled', null ).find( 'div' ).addClass( 'd-none' );
                }
            });
        });
    }
</script>

<div class="wrap">
    <h1>Login pela Triibo</h1>
    <h4 id="msg-status" >O plugin está: <span id="txt-triibo-status" ><?=$stt_txt?></span></h4>
    <button type="button" id="btn-triibo-status" class="woocommerce-Button button set-spin" ><?=$btn_msg?> <div class="d-none spin-load" ></div></button>

    <p>O plugin tem por finalidade habilitar no marketplace a posibilidade de se realizar o login pela Triibo.</p>
    <p>Quando ativado, o plugin irá inserir o botão "Entrar com Triibo" na página de login, como mostra a imagem.</p>
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'includes/images/exemplo.png'; ?>" width="325px" >

    <p>Exemplo dos modais de login</p>
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'includes/images/modal_cod_sms.png'; ?>" width="325px" >
    <br>
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'includes/images/modal_cod_email.png'; ?>" width="325px" >
    <br>
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'includes/images/modal_contas_nao_associadas.png'; ?>" width="325px" >
    <br>
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'includes/images/modal_nao_cadastrado_na_triibo.png'; ?>" width="325px" >

    <hr>
    <?php echo TAL_API_URL; ?>
</div>
