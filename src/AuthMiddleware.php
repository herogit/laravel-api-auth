<?php

namespace Ycan\ApiAuth;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthMiddleware {

    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {

        $signature = $request->hasHeader('signature') ? $request->header('signature') : (!empty($request->input('signature')) ? $request->input('signature') : '');

        // 获取配置文件内容
        $apiAuthConfig = config('apiAuth');

        if(isset($apiAuthConfig['clients']['switch']) && $apiAuthConfig['clients']['switch'] !== 1){
            return $next($request);
        }

        // 获取请求参数
        $param = $request->input();

        if(!empty($param['signature'])){
            unset($param['signature']);
        }

        // 获取accessKey
        $accessKey = $param['accessKey'] ?? '';

        // 获取secretKey
        $secretKey = $apiAuthConfig['clients'][$accessKey]['secret_key'] ?? '';
        if (empty($accessKey) || empty($secretKey)) {
            throw new InvalidTokenException('accessKey校验失败');
        }
        // 校验nonce
        if(!empty($param['nonce']) && $this->validNonce($param['nonce'])) {
            throw new InvalidTokenException('防止重放校验失败');
        }
        // 设置nonce Cache
        $this->setNonce($param['nonce'], $apiAuthConfig['clients'][$accessKey]['timeout']);

        // 校验时间过期
        if(!empty($param['timestamp']) && $param['timestamp'] < (Carbon::now()->timestamp - $apiAuthConfig['clients'][$accessKey]['timeout'])) {
            throw new InvalidTokenException('有效时间校验失败');
        }
        
        // 校验signature正确
        $is_check = $this->checkToken($param, $secretKey, $signature);

        if(!$is_check){
            throw new InvalidTokenException('校验失败');
        }

        return $next($request);
    }

    /**
     * 生成token
     * @param $param
     * @param $secretKey
     * @return string
     */
    public function sign($param, $secretKey)
    {
        ksort($param);
        $str = '';
        foreach($param as $key=>$val){
            if(is_array($val)){
                $str .= $key.json_encode($val);
            }else{
                $str .= $key.$val;
            }
        }

        return md5($str.$secretKey);
    }

    /**
     * 校验token
     * @param $param
     * @param $secretKey
     * @param $signature
     * @return bool
     */
    public function checkToken($param, $secretKey, $signature)
    {
        return $this->sign($param, $secretKey) == $signature;
    }

    /**
     * 校验token
     * @param $nonce
     * @return bool
     */
    public function validNonce($nonce)
    {
        if(empty($nonce) || !Cache::has($this->getNonceKey($nonce))){
            return false;
        }
        return true;
    }

    /**
     * 设置token
     * @param $nonce
     * @param $timeout
     * @return bool
     */
    public function setNonce($nonce, $timeout)
    {
        Cache::add($this->getNonceKey($nonce), 1, $timeout);
    }

    /**
     * 生成NonceKey
     * @param $nonce
     * @return string
     */
    private function getNonceKey($nonce)
    {
        return 'api:nonce:'.$nonce;
    }
}

