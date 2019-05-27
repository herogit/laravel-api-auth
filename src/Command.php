<?php

namespace Ycan\ApiAuth;

use Illuminate\Console\Command as LaravelCommand;

class Command extends LaravelCommand {

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'api_auth';
    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = '生成随机 access_key 和 secret_key 。';
    /**
     * 执行控制台命令。
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('access_key: ' . $this->str_rand());
        $this->info('secret_key: ' . $this->str_rand());
    }

    /*
    * 生成随机字符串
    * @param int $length 生成随机字符串的长度
    * @param string $char 组成随机字符串的字符串
    * @return string $string 生成的随机字符串
    */
    public function str_rand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        if (!is_int($length) || $length < 0) {
            return false;
        }
        $string = '';
        for ($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }
        return $string;
    }

}