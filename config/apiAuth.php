<?php

return [

    'clients' => [
            'switch'=> 0,                           // 一键下线
            '{access_key}' => [
                'name' => '{role_name}', 			// 角色名称，用于辨别例如 superman
                'secret_key' => '{secret_key}',     // 安全问题
                'timeout' => 60 					// 签名时间，单位: 秒
            ]

    ]

];