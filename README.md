# laravel-ssapi
一个简单的laravel服务器到服务器api签名和验证类

## Installation

Pull this package in through Composer.

or run in terminal:
`composer require zhengcai/encrypt`

将配置文件复制到config目录下，允许同时配置多套规则

`php artisan vendor:publish --provider="Zhengcai\Encrypt\EncryptServiceProvider"`

加载完composer包后建议手动清除编译的类文件和服务容器缓存
php artisan clear-compiled

## Usage

### Laravel usage

```php
    
    // 引用composer命名
    use Zhengcai\Encrypt\Facades\Encrypt;
    
    
    $data = ['key' => '测试内容1', 'love' => 'ILOVEYOU!!!'];

        $configArray = [
            //SM4 使用的ecb 模式，并且使用TOKEN_KEY字段
            'TOKEN_KEY' => 'F5A74513CEF42A3A', // 加密随机串，长度应大于等于16位
            'TOKEN_EXPIRE' => 86400 * 31, // token 有效期单位秒
            'TOKEN_RENEW' => false, // token是否自动续期(验证通过后有效期自动续签)
            'TOKEN_PREFIX' => 'TOKEN_PREFIX_%s_%s', // token缓存前缀
            'TOKEN_ISS' => 'zy-scheduling', // token签发者标识
            'TOKEN_ALG' => 'HS256', // token加密算法 ES384,ES256,HS256,HS384,HS512,RS256,RS384,RS512
        ];

        try {
        
            // Encrypt::server('test') 为读取config目录下encrypt.php
            // 如果不想使用此方式 可以 new Encrypt($configArray) 将配置数组传递进去
        
            /**
             * Desc: SM4 加密采用ecb 模式 blockSize为16块  
             * 前端可以使用 npm install gm-crypt 
             * @param string|array $data 需要加密的数据
             * @return string 加密后的结果
             * User: 青山有木
             * Date: 2023/8/11
             * Email: yz_luck@163.com
             */
            $hex = Encrypt::server('test')->SM4Encrypt($data);
            
            // 解密
            $hex = Encrypt::server('test')->SM4Decrypt($hex);
        
            /**
             * Desc: 生成JwtToken  makeJwtToken方法
             * @param string|numeric $userId 用户唯一标识
             * @param array $DS       需要生成token的数据
             * @param string $layered 模块标识 默认User
             * @return array
             * User: 青山有木
             * Date: 2023/8/11
             * Email: yz_luck@163.com
             */
            $server = Encrypt::server('test')->makeJwtToken(1,$data);
            
            /**
             * verifyToken 验证方法此方法如果验证token失败会抛出异常
             * $exception->getCode()  90001 => token无效 ,90002 => 当前token已过期 , 99999 => 无法调用缓存
             * Desc: 验证token  
             * @param string $token token串
             * @return array
             * @throws Exception
             * User: 青山有木
             * Date: 2023/8/11
             * Email: yz_luck@163.com
             */
            $server = Encrypt::server('test')->verifyToken($server['token']);
            
            /**
             * Desc: 清除用户Token clearToken方法
             * @param string|numeric $userId 用户唯一标识
             * @param string $token token串
             * @param string $layered 模块标识 默认User
             * @return bool
             * User: 青山有木
             * Date: 2023/8/11
             * Email: yz_luck@163.com
             */
            $server = Encrypt::server('test')->clearToken(1,$server['token']);
        }catch (\Exception $exception){
            // 非laravel框架则会抛出无法使用cache的异常
            dd($exception->getMessage());
        }
        dd($server);

    

```