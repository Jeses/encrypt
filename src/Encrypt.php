<?php
/**
 * Created by PhpStorm
 * Desc:
 * User: 青山有木
 * Date: 2023/8/10
 * Email: yz_luck@163.com
 */

namespace Zhengcai\Encrypt;


use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Zhengcai\Encrypt\smec\SM4;
use function request;

class Encrypt
{
	protected $TOKEN_KEY; // 加密随机串
	protected $TOKEN_EXPIRE; // 有效期单位秒
	protected $TOKEN_RENEW; // token是否自动续期
	protected $TOKEN_PREFIX; // 缓存前缀
	protected $TOKEN_ISS; // 签发者标识
	protected $TOKEN_ALG; // 加密算法

	public function __construct(array $config)
	{
		$this->TOKEN_KEY = $config['TOKEN_KEY'];
		$this->TOKEN_EXPIRE = $config['TOKEN_EXPIRE'];
		$this->TOKEN_RENEW = $config['TOKEN_RENEW'];
		$this->TOKEN_PREFIX = $config['TOKEN_PREFIX'];
		$this->TOKEN_ISS = $config['TOKEN_ISS'];
		$this->TOKEN_ALG = $config['TOKEN_ALG'];
	}

	/**
	 * Desc:
	 * @param string|array $data 需要加密的数据
	 * @return string 加密后的结果
	 * User: 青山有木
	 * Date: 2023/8/11
	 * Email: yz_luck@163.com
	 */
	public function SM4Encrypt($data): string
	{
		if (is_array($data)) {
			$data = json_encode($data);
		}

		$sm4 = new SM4();
		return $sm4->encrypt($this->TOKEN_KEY, $data);
	}

	/**
	 * Desc: SM4解密
	 * @param string $data 需要解密的数据
	 * @return array|false|mixed|string|string[]
	 * User: 青山有木
	 * Date: 2023/8/11
	 * Email: yz_luck@163.com
	 */
	public function SM4Decrypt(string $data)
	{
		$sm4 = new SM4();
		$decrypt = $sm4->decrypt($this->TOKEN_KEY, $data);
		$jsonDecrypt = json_decode($decrypt,true);

		// return json_last_error() === JSON_ERROR_NONE ? $jsonDecrypt : $decrypt;
		if(json_last_error() === JSON_ERROR_NONE){
			return $jsonDecrypt;
		}else{
			return $decrypt;
		}
	}

	/**
	 * Desc: 生成Jwt Token
	 * @param string|numeric $userId 用户唯一标识
	 * @param array $DS      需要生成token的数据
	 * @param string $layered 模块标识
	 * @return array
	 * User: 青山有木
	 * Date: 2023/8/11
	 * Email: yz_luck@163.com
	 */
	public function makeJwtToken( $userId, array $DS, string $layered = 'User'): array
	{
		$payload = $this->payload($userId, $DS, $layered);
		$token = JWT::encode($payload, $this->TOKEN_KEY, $this->TOKEN_ALG);
		//调用laravel缓存组件，写入缓存增加一层验证
		try {
			Cache::put(sprintf($this->TOKEN_PREFIX, $layered, $userId), $token, $this->TOKEN_EXPIRE);
		}catch (\Exception $exception){
			throw new Exception('无法写入缓存',99999);
		}
		return ['token' =>$token, 'exp' =>$payload['exp'],'cacheKey' => sprintf($this->TOKEN_PREFIX, $layered, $userId)];
	}

	/**
	 * Desc: 验证token
	 * @param string $token token串
	 * @return array
	 * @throws Exception
	 * User: 青山有木
	 * Date: 2023/8/11
	 * Email: yz_luck@163.com
	 */
	public function verifyToken(string $token): array
	{
		$payload = JWT::decode($token, new Key($this->TOKEN_KEY, $this->TOKEN_ALG));
		$cacheKey = sprintf($this->TOKEN_PREFIX, $payload->layered, $payload->userId);
		if (!Cache::has($cacheKey) || $token != Cache::get($cacheKey)) {
			throw new Exception('当前token无效.',90001);
		}

		if($payload->exp < time()){
			throw new Exception('当前token已过期.',90002);
		}

		// 重置缓存，延长有效时间
		if($this->TOKEN_RENEW){
			try {
				Cache::put(sprintf($this->TOKEN_PREFIX, $payload->layered, $payload->userId), $token, $this->TOKEN_EXPIRE);
			}catch (\Exception $exception){
				throw new Exception('无法续期缓存,请自行续期',99999);
			}
		}

		// 销毁iss防止泄露
		unset($payload->iss);

		return (array)$payload;
	}

	/**
	 * Desc: 清除用户Token
	 * @param string|numeric $userId 用户唯一标识
	 * @param string $token token串
	 * @param string $layered 模块标识
	 * @return bool
	 * User: 青山有木
	 * Date: 2023/8/11
	 * Email: yz_luck@163.com
	 */
	public function clearToken($userId,string $token, string $layered = 'User'): bool
	{
		//如果当前用户的token和缓存的token一致才可清除
		$cacheKey = sprintf($this->TOKEN_PREFIX, $layered, $userId);
		try {
			if ($token == Cache::get($cacheKey)) {
				return Cache::forget(sprintf($this->TOKEN_PREFIX, $layered, $userId));
			}
		}catch (\Exception $exception){
			throw new Exception('无法调用laravel组件清除缓存,请自行清除 ',9999);
		}
		return true;
	}

	/**
	 * Desc: payload参数生成
	 * @param string $userId  用户标识
	 * @param array $DS       用户使用的基础数据
	 * @param string $layered 业务标识
	 * @return array
	 * User: 青山有木
	 * Date: 2023/8/11
	 * Email: yz_luck@163.com
	 */
	private function payload(string $userId,array $DS, string $layered): array
	{
		$timestamp = time();
		$map = array(
			"iss" => $this->TOKEN_ISS,
			"exp" => $timestamp + $this->TOKEN_EXPIRE,
			"userId" => $userId,
			'layered' => $layered,
			);

		return array_merge($map,$DS);
	}

}