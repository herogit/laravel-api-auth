# laravel-api-auth
laravel API 鉴权

这是一个 laravel 的 API 鉴权包， `laravel-api-auth` 采用签名的鉴权方式

## 安装  
```bash
composer require ycan/laravel-api-auth
```

## 配置
1. 注册 `ServiceProvider`: 
    > 如果laravel 5.5+ 版本，添加会自动发现

    ```
   Ycan\ApiAuth\ApiAuthServiceProvider::class,
    ```
    

2. 发布配置文件
    ```
    php artisan vendor:publish --tag="apiAuth"
    ```

3. 在 `App\Http\Kernal` 中注册中间件 
    ```
    protected $routeMiddleware = [
        'api-auth' => AuthMiddleware::class,
    ];
    ```
    
4. 添加 `role` 
    ```
    php artisan api_auth
    ```
    然后按照格式把 `access_key` 和 `secret_key` 添加到, `config/api_auth.php` 里面的 `roles` 数组中。
    ```
    'roles' => [
        '12345' => [
            'name' => 'superman',        // 角色名称，用于辨别例如 superman
            'secret_key' => '67890',
            'timeout' => 60              //签名时间，单位: 秒
        ],
    ],
    ```
    
    
     
## 中间件使用  
##### routes/api.php

```
Route::group(['middleware'=>'api-auth'], function(){
    // routes...
});
```

## 前端

> 所需物料

    access_key: 12345
    secret_key: 67890
    timestamp: 当前时间戳
    nonce: 随机字符串

> 签名方式

- ['timestamp' => now()]，当成一个参数、该时间会过期
- 将所以的参数变成一个数组用函数 ksort()排序 [关联数组按照键名进行升序排序] 
- 自主生成nonce,防止重放攻击
- 算法 signature = md5(key1 + value1 + key2 + value2 + secret_key)
- 示例

          get方法 : url?accessKey=access_key&key1=value1&key2=value2&timestamp=timestamp&signature=signature&nonce=nonce
          post方法 :  
          [
            'accessKey' => access_key,
            'key1' => value1,
            'key2' => value2,
            'nonce' => nonce,
            'timestamp' => timestamp,
            'signature' => signature
          ]
          其他 : method同post方法
          
- 亦或者将signature放到header中

