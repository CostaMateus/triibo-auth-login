<?php
    $_LPT_ENV = get_transient('_LPT_ENV');

    $_LPT_ENV_CHANNEL_ID     = get_transient( '_LPT_ENV_CHANNEL_ID'     );
    $_LPT_ENV_CHANNEL_GROUP  = get_transient( '_LPT_ENV_CHANNEL_GROUP'  );
    $_LPT_ENV_CHANNEL_NAME   = get_transient( '_LPT_ENV_CHANNEL_NAME'   );
    $_LPT_ENV_UID            = get_transient( '_LPT_ENV_UID'            );

    $_LPT_PROD_API_URL       = get_transient( '_LPT_PROD_API_URL'       );
    $_LPT_PROD_API_TOKEN     = get_transient( '_LPT_PROD_API_TOKEN'     );
    $_LPT_PROD_CHANNEL_TOKEN = get_transient( '_LPT_PROD_CHANNEL_TOKEN' );

    $_LPT_HML_API_URL        = get_transient( '_LPT_HML_API_URL'        );
    $_LPT_HML_API_TOKEN      = get_transient( '_LPT_HML_API_TOKEN'      );
    $_LPT_HML_CHANNEL_TOKEN  = get_transient( '_LPT_HML_CHANNEL_TOKEN'  );


    $_STATUS_PLUGIN = get_transient('_STATUS_PLUGIN');

    if ($_STATUS_PLUGIN == 'active') {
        $stt_txt = 'ATIVO';
        $btn_msg = 'Desativar login Triibo';
    } else {
        $stt_txt = 'INATIVO';
        $btn_msg = 'Ativar login Triibo';
    }
?>

<style>
    #msg-status {
        margin-bottom: 8px !important;
    }

    #msg-status span {
        font-weight: bold !important;
    }

    #btn-triibo-status {
        margin-bottom: 20px;
    }

    .d-none {
        display: none !important;
    }

    .set-spin {
        display: flex !important;
        justify-content: center;
    }

    .spin-load {
        width: 8px;
        height: 8px;
        margin: auto 5px;
        border: 3px solid #AEAEAE;
        border-top: 3px solid #0071A1;
        border-radius: 50%;
        -webkit-animation: spin 1s linear infinite;
        /* Safari */
        animation: spin 1s linear infinite;
    }

    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 782px) {
        table.lpt-form-width td {
            padding: 10px 21px 0 17px;
        }
        table tr:last-child  td {
            padding-bottom: 20px;
            margin-bottom: 0;
        }
    }
    @media screen and (max-width: 782px) {
        .form-table td {
            margin-bottom: 0;
            padding: 4px 0 6px 0;
        }
    }
    .form-table, .form-table td, .form-table td p, .form-table th {
        font-size: 14px;
    }
    .form-table td {
        margin-bottom: 9px;
        padding: 15px 10px;
        line-height: 1.3;
        vertical-align: middle;
    }
    .form-table button {
        margin-bottom: 20px !important;
    }
    .lpt-field-width {
        width: 400px;
    }
    .lpt-form-width {
        width: 100%;
    }
    .lpt-th-width {
        width: 25%;
        padding: 20px 20px 0 !important;
    }
</style>

<script>
    jQuery('.notice, .error').css("display", "none");

    window.onload = function() {
        jQuery('#btn-triibo-status').on('click', function(e) {

            jQuery('#btn-triibo-status').prop('disabled', 'disabled').find('div').removeClass('d-none');

            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'lpt_set_status_global'
                },
                success: function(data) {
                    // console.log( data );
                    // window.location.reload();

                    switch (data) {
                        case 'active':
                            jQuery('#txt-triibo-status').html('ATIVO');
                            jQuery('#btn-triibo-status').html('Desativar login Triibo <div class="d-none spin-load">');
                            break;
                        case 'inactive':
                            jQuery('#txt-triibo-status').html('INATIVO');
                            jQuery('#btn-triibo-status').html('Ativar login Triibo <div class="d-none spin-load">');
                            break;
                    }

                    jQuery('#btn-triibo-status').prop('disabled', null).find('div').addClass('d-none');
                },
                error: function(data) {
                    console.log(data);
                    jQuery('#btn-triibo-status').prop('disabled', null).find('div').addClass('d-none');
                }
            });
        });



    }
</script>

<div class="wrap">
    <h1>Login pela Triibo</h1>
    <h4 id="msg-status">O plugin está: <span id="txt-triibo-status"><?= $stt_txt ?></span></h4>
    <button type="button" id="btn-triibo-status" class="woocommerce-Button button set-spin"><?= $btn_msg ?> <div class="d-none spin-load"></div></button>

    <p>O plugin tem por finalidade habilitar no marketplace a posibilidade de se realizar o login pela Triibo.</p>
    <p>Quando ativado, o plugin irá inserir o botão "Entrar com Triibo" na página de login, como mostra a imagem.</p>
    <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'includes/images/exemplo.png'; ?>" width="325px">

    <hr>

    <h2>Configurações</h2>

    <table class="wp-list-table widefat fixed lpt-form-width form-table">
        <tbody>
            <form id="tokens">
                <tr>
                    <th class="lpt-th-width">Usar credenciais</th>
                    <td>
                        <select name="env" class="lpt-field-width" >
                            <option value="hml"  <?php if ( $_LPT_ENV == 'hml'  ) echo 'selected'; ?> >Homologação</option>
                            <option value="prod" <?php if ( $_LPT_ENV == 'prod' ) echo 'selected'; ?> >Produção</option>
                        </select>
                    </td>
                </tr>

                <?php
                    if ( $_LPT_ENV == 'hml' ) {
                ?>
                    <tr>
                        <th class="lpt-th-width">API Token</th>
                        <td>
                            <input type="text" name="hml-api-token" value="<?=$_LPT_HML_API_TOKEN?>" class="lpt-field-width" >
                        </td>
                    </tr>
                    <tr>
                        <th class="lpt-th-width">Channel Token</th>
                        <td>
                            <input type="text" name="hml-channel-token" value="<?=$_LPT_HML_CHANNEL_TOKEN?>" class="lpt-field-width" >
                        </td>
                    </tr>
                <?php
                    } else {
                ?>
                    <tr>
                        <th class="lpt-th-width">API Token</th>
                        <td>
                            <input type="text" name="prod-api-token" value="<?=$_LPT_PROD_API_TOKEN?>" class="lpt-field-width" >
                        </td>
                    </tr>
                    <tr>
                        <th class="lpt-th-width">Channel Token</th>
                        <td>
                            <input type="text" name="prod-channel-token" value="<?=$_LPT_PROD_CHANNEL_TOKEN?>" class="lpt-field-width" >
                        </td>
                    </tr>
                <?php
                    }
                ?>

                <tr>
                    <th class="lpt-th-width" colspan="2">
                        <button type="submit" id="btn-tokens" class="woocommerce-Button button set-spin">Salvar alterações</button>
                    </th>
                </tr>
            </form>
        </tbody>
    </table>
</div>
