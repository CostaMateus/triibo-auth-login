<?php
    $_STATUS_PLUGIN = get_transient( '_STATUS_PLUGIN' );

    if ( $_STATUS_PLUGIN == 'active' )
    {
        // $status  = false;
        $stt_txt = 'ATIVO';
        $btn_msg = 'Desativar login Triibo';
    }
    else
    {
        // $status  = true;
        $stt_txt = 'INATIVO';
        $btn_msg = 'Ativar login Triibo';
    }
?>

<style>
    .msg-status { font-weight: bold !important; }
    h4 { margin-bottom: 8px !important; }
    #btn-triibo-status { margin-bottom: 20px; }
</style>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script> -->
<script>
    jQuery( '.notice, .error' ).css( "display", "none" );

    console.log('<?=get_transient( '_STATUS_PLUGIN' )?>');

    window.onload = function() {
        jQuery( '#btn-triibo-status' ).on( 'click', function ( e ) {

            jQuery.ajax({
                url : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action : 'lpt_set_status_global'
                },
                success: function( data ) {
                    console.log( data );
                    window.location.reload();
                },
                error: function( data ) {
                    console.log( data );
                }
            });
        });
    }
</script>

<div class="wrap">
    <h1>Login pela Triibo</h1>
    <h4>O plugin está: <span class="msg-status" ><?=$stt_txt?></span></h4>
    <button type="button" id="btn-triibo-status" class="woocommerce-Button button" ><?=$btn_msg?></button>

    <p>O plugin tem por finalidade habilitar no marketplace a posibilidade de se realizar o login pela Triibo.</p>
    <p>Quando ativado, o plugin irá inserir o botão "Entrar com Triibo" na página de login, como mostra a imagem.</p>
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'includes/images/exemplo.png'; ?>" width="325px" >
</div>
