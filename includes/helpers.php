<?php

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

    function TAL_hash_triibo( $apiToken, $passPhrase, $channelToken )
    {
        $challenge1 = md5( $passPhrase . $apiToken     );
        $challenge2 = md5( $challenge1 . $channelToken );
        return $challenge2;
    }

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
