<?php

    /**
     * Cria usuario no Mkt
     */
    function TAL_create_user_marketplace( $uId, $cellphone, $email, $pass )
    {
        $cellphone = "+" . preg_replace( "/\D/", "", $cellphone );
        $name      = explode("@", $email)[0];

        $user_id   = wp_insert_user([
            "user_login"    => $uId,
            "user_pass"     => wp_hash_password( "Triibo2021" ),
            "user_nicename" => $name,
            "user_email"    => $email,
            "display_name"  => $name,
            "nickname"      => $name,
            "first_name"    => $name,
            "last_name"     => "",
        ]);

        if ( ! is_wp_error( $user_id ) )
        {
            wp_set_password( $pass, $user_id );

            // campo padronizado com o plugin triibo-integration-user
            add_user_meta( $user_id, "triiboId_phone", $cellphone );

            $user = get_user_by( "id", $user_id );

            $user->set_role( "subscriber" );
            if ( class_exists( "WooCommerce" ) ) $user->set_role( "customer" );
        }

        return TAL_login_by_triibo( "id", $user_id );
    }

    /**
     * Cria ou atualiza usuario na Triibo
     */
    function TAL_create_or_update_user_triibo( $cellphone, $email, $name = null, $nick = null )
    {
        $cellphone = "+" . preg_replace( "/\D/", "", $cellphone );
        $name      = $name ? $name : explode("@", $email)[0];
        $nick      = $nick ? $nick : $name;
        $emails    = [
            [
                "type"     => "email",
                "value"    => $email,
                "verified" => true,
            ], [
                "type"     => "emailMarketplace",
                "value"    => $email,
                "verified" => true,
            ]
        ];

        $channelTokenId_1 = TAL_get_channel_token( null );
        $registerUser     = [
            "triiboHeader"    => [
                "apiToken"       => TAL_API_TOKEN,
                "channelGroup"   => TAL_CHANNEL_GROUP,
                "channelName"    => TAL_CHANNEL_NAME,
                "channelId"      => TAL_CHANNEL_ID,
                "sessionId"      => "001",
                "transactionId"  => "002",
                "channelTokenId" => $channelTokenId_1["success"]["newToken"]["channelTokenId"]
            ],
            "linkedAccount" => null,
            "cellphone"     => $cellphone,
        ];

        // cria usuário se não existir e retorna as informações
        $resp1 = TAL_exec("login", $registerUser);

        if ( $resp1["success"] )
        {
            $user        = $resp1["user"];
            $uId         = $user["uId"];
            $contactList = $user["contactList"];

            if ( count( $contactList ) > 1 )
            {
                foreach ( $contactList as &$c )
                {
                    if ( $c["type"] == "email" ) $c["value"] = $emails[0]["value"];
                }
            }
            else
            {
                $contactList = array_merge( $contactList, $emails );
            }
        }

        $channelTokenId_2 = TAL_get_channel_token( $uId );
        $setUserInfo    = [
            "triiboHeader"    => [
                "apiToken"       => TAL_API_TOKEN,
                "channelGroup"   => TAL_CHANNEL_GROUP,
                "channelName"    => TAL_CHANNEL_NAME,
                "channelId"      => TAL_CHANNEL_ID,
                "sessionId"      => "001",
                "transactionId"  => "002",
                "channelTokenId" => $channelTokenId_2["success"]["newToken"]["channelTokenId"],
                "uId"            => $uId
            ],
            "queryPartnerAPI" => [
                "setUserInfo"
            ],
            "userInfo"        => [
                "syncType"    => "merge",
                "uId"         => $uId,
                "name"        => ( $user["name"] && $user["name"] != "Triiber" ) ? $user["name"]      : $name,
                "aliasName"   => ( $user["aliasName"] )                          ? $user["aliasName"] : $nick,
                "contactList" => [
                    ... $contactList
                ],
            ],
        ];

        // atualiza informações do usuario
        $resp2 = TAL_exec("setUserInfo_v1", $setUserInfo);

        return $uId;
    }

    /**
     * Envia código de validação por email
     */
    function TAL_send_email_code( $email )
    {
        $validCode = str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);

        set_transient( "_triibo_code", $validCode );

        $to      = $email;
        $subject = "Seu código de ativação";
        $body    = "<html lang='pt-br'><body><p>Seu código de ativação do Marketplace Triibo é:</p><p style='font-size:18px;margin:0'><b>$validCode</b></p></body></html>";
        $headers = array("Content-Type: text/html; charset=UTF-8","From: Marketplace Triibo <equipemarketplace@triibo.com.br>");

        return wp_mail( $to, $subject, $body, $headers );
    }

    /**
     * Verifica se tem email validado
     */
    function TAL_email_verified( $data )
    {
        $email = null;

        foreach ( $data as $c )
        {
            if ( $c["type"] == "emailMarketplace" && $c["verified"] ) $email = $c["value"];
        }

        return $email;
    }

    /**
     * Efetua login no Mkt, segundo $field passado
     */
    function TAL_login_by_triibo( $field, $value )
    {
        $user = get_user_by( $field, $value );

        return TAL_login( $user, "Login by field_$field" );
    }

    /**
     * Verifica usuario, define como corrente
     */
    function TAL_login( $user, $alert )
    {
        if ( ! is_wp_error( $user ) )
        {
            wp_clear_auth_cookie();
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );

            $result = [
                "success" => [
                    "redirect" => home_url( "/my-account" ),
                ],
                "error"   => null,
                "alert"   => null,
            ];
        }
        else
        {
            $result = [
                "success" => null,
                "error"   => [
                    "code"    => 500,
                    "message" => "Error login",
                ],
                "alert"   => $alert,
            ];
            TAL_register_log( "TAL_login", [ $user, $result ] );
        }

        return $result;
    }





    /**
     * Defini channelToken para as chamadas das APIs
     */
    function TAL_get_channel_token( $uId )
    {
        $passPhrase         = floor( rand( 1000000000, 9999999999 ) ) . $uId . floor( rand( 1000000000, 9999999999 ) );
        $challenge          = TAL_hash_triibo( TAL_API_TOKEN, $passPhrase, TAL_CHANNEL_TOKEN );
        $token_triiboHeader = [
            "triiboHeader" => [
                "apiToken"      => TAL_API_TOKEN,
                "channelGroup"  => TAL_CHANNEL_GROUP,
                "channelName"   => TAL_CHANNEL_NAME,
                "channelId"     => TAL_CHANNEL_ID,
                "sessionId"     => "001",
                "transactionId" => "002",
                "uId"           => $uId,
                "passPhrase"    => $passPhrase,
                "challenge"     => $challenge
            ],
        ];

        return TAL_exec("getChannelToken_v1", $token_triiboHeader);
    }

    /**
     * Cria hash para o triiboHeader
     */
    function TAL_hash_triibo( $apiToken, $passPhrase, $channelToken )
    {
        $challenge1 = md5( $passPhrase . $apiToken     );
        $challenge2 = md5( $challenge1 . $channelToken );
        return $challenge2;
    }

    /**
     * Executa chamadas ao Firebase
     */
    function TAL_exec($api, $params)
    {
        $ch = curl_init();

		curl_setopt_array( $ch, [
			CURLOPT_URL            => TAL_API_URL . $api,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_SSL_VERIFYPEER => false ,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_HTTPHEADER     => [ "Content-type: application/json" ],
            CURLOPT_POSTFIELDS     => json_encode( $params )
        ]);

        $result = curl_exec( $ch );
        curl_close( $ch );

        return json_decode( $result, true );
    }



    // /**
    //  * Manda os endereços do usuário para a Triibo
    //  */
    // function TAL_setUserAddress( $info, $addrs )
    // {
    //     $channelTokenId   = TAL_get_channel_token( $info['uId'] );
    //     $set_triiboHeader = [
    //         'triiboHeader'    => [
    //             'apiToken'       => TAL_API_TOKEN,
    //             'channelGroup'   => TAL_CHANNEL_GROUP,
    //             'channelName'    => TAL_CHANNEL_NAME,
    //             'channelId'      => TAL_CHANNEL_ID,
    //             'sessionId'      => '001',
    //             'transactionId'  => '002',
    //             'channelTokenId' => $channelTokenId["success"]["newToken"]["channelTokenId"],
    //             'uId'            => $info['uId']
    //         ],
    //         'queryPartnerAPI' => [
    //             'setUserInfo'
    //         ],
    //         'userInfo'        => [
    //             // 'addressList' => [],
    //             'triiboId'   => $info['triiboId'],
    //             'gender'     => $info['gender'],
    //             'syncType'   => 'merge',
    //             'updateDate' => 1610387795564
    //         ],
    //     ];
    //     $i = 0;
    //     foreach ($addrs as $key => $value)
    //     {
    //         if (empty($value['postcode']) &&
    //             empty($value['address_1']) &&
    //             empty($value['address_2']) &&
    //             empty($value['number']) &&
    //             empty($value['neighborhood']) &&
    //             empty($value['city']) &&
    //             empty($value['state']) &&
    //             empty($value['country'])
    //         ) {
    //             continue;
    //         }
    //         $set_triiboHeader['userInfo']['addressList'][$i++] = [
    //             'zipCode'      => $value['postcode'],
    //             'streetAve'    => $value['address_1'],
    //             'number'       => $value['number'],
    //             'extra'        => $value['address_2'],
    //             'neighborhood' => $value['neighborhood'],
    //             'city'         => $value['city'],
    //             'state'        => $value['state'],
    //             'country'      => $value['country'],
    //             'googleAddr'   => $value['address_1'] . ', ' . $value['number'] . ' - ' . $value['neighborhood'] . ', ' . $value['city'] . ' - ' . $value['state'] . ', ' . $value['postcode'],
    //             'type'         => $key,          // comercial, residencial, billing, shipping
    //             'origin'       => 'marketplace', // marketplace,
    //         ];
    //     }
    //     return TAL_exec("setUserInfo_v1", $set_triiboHeader);
    // }
    // function TAL_setPreferAddress( $uId, $addrs )
    // {
    //     $channelTokenId    = TAL_get_channel_token( $uId );
    //     $pref_triiboHeader = [
    //         'triiboHeader'    => [
    //             'apiToken'       => TAL_API_TOKEN,
    //             'channelGroup'   => TAL_CHANNEL_GROUP,
    //             'channelName'    => TAL_CHANNEL_NAME,
    //             'channelId'      => TAL_CHANNEL_ID,
    //             'sessionId'      => '001',
    //             'transactionId'  => '002',
    //             'channelTokenId' => $channelTokenId["success"]["newToken"]["channelTokenId"],
    //             'uId'            => $uId
    //         ],
    //         'uId'          => $uId,
    //         'addresses'    => [
    //             // [
    //             //     'origin'  => 'marketplace',
    //             //     'zipCode' => '04024-020',
    //             //     'type'    => 'billing',
    //             // ]
    //         ]
    //     ];
    //     foreach ($addrs as $key => $value)
    //     {
    //         if (empty($value['postcode'])) continue;
    //         $pref_triiboHeader['addresses'][] = [
    //             'origin'  => 'marketplace',
    //             'zipCode' => $value['postcode'],
    //             'type'    => $key
    //         ];
    //     }
    //     return TAL_exec("preferentialAddress", $pref_triiboHeader);
    // }
    // function TAL_getEmail( $data )
    // {
    //     $email = null;
    //     foreach( $data as $temp )
    //     {
    //         foreach ( $temp as $key => $value )
    //         {
    //             if ( $key == 'type' && $value == 'email' )
    //             {
    //                 $email = $temp['value'];
    //             }
    //         }
    //     }
    //     return $email;
    // }
    // function TAL_getCpf( $data )
    // {
    //     $cpf = '';
    //     foreach( $data as $temp )
    //     {
    //         foreach ( $temp as $key => $value )
    //         {
    //             if ( $key == 'type' && $value == 'cpf' )
    //             {
    //                 $cpf = $temp['value'];
    //             }
    //         }
    //     }
    //     return $cpf;
    // }
    // function TAL_getUserMeta( $user_id )
    // {
    //     $data = array_map( function( $a ) {
    //         return $a[0];
    //     }, get_user_meta( $user_id ) );
    //     return $data;
    // }
    // function TAL_getBillingShipping( $user_id )
    // {
    //     $data = TAL_getUserMeta( $user_id );
    //     $meta = [];
    //     $bill = 'billing_';
    //     $ship = 'shipping_';
    //     foreach ( $data as $key => $value )
    //     {
    //         if ( ( substr( $key, 0, strlen( $bill ) ) === $bill ) )
    //         {
    //             $temp = explode( $bill, $key )[1];
    //             $meta['billing'][$temp] = $value;
    //         }
    //         if ( ( substr( $key, 0, strlen( $ship ) ) === $ship ) )
    //         {
    //             $temp = explode( $ship, $key )[1];
    //             $meta['shipping'][$temp] = $value;
    //         }
    //     }
    //     return $meta;
    // }
