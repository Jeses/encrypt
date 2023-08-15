<?php
/**
 * Created by PhpStorm
 * Desc:
 * User: 青山有木
 * Date: 2023/8/10
 * Email: yz_luck@163.com
 */

namespace Zhengcai\Encrypt;

class EncryptService
{
	protected $_services;

	/**
	 * Desc: 根据配置名进行实例化
	 * @param string $serverName
	 * @return mixed|Encrypt
	 * @throws \Exception
	 * User: 青山有木
	 * Date: 2023/8/10
	 * Email: yz_luck@163.com
	 */
	public function server(string $serverName)
	{
		if (!isset($this->_services[$serverName])) {
			$config = config('encrypt.' . $serverName);
			if (empty($config)) {
				throw new \Exception('Api server config is not exist.');
			}
			$this->_services[$serverName] = new Encrypt($config);
		}

		return $this->_services[$serverName];
	}



	/**
	 * Desc: 创建签名
	 * @param array $data 数据
	 * @param string $appKey  公钥
	 * @param string $appSecret 私钥
	 * @return array
	 * User: 青山有木
	 * Date: 2023/8/15
	 * Email: yz_luck@163.com
	 */
	public function createSign(array $data, string $appKey, string $appSecret)
	{
		if (isset($data['_sign']))
			unset($data['_sign']);

		$data['appKey'] = $appKey;
		$data['_timestamp'] = time();
		$signStr = $appSecret;
		ksort($data);
		foreach ($data as $key => $val) {
			$val = strval($val);
			if ($key != '' && strpos($val, '@') !== 0)
				$signStr .= $key . $val;
		}
		$data['_sign'] = strtoupper(md5($signStr . $appSecret));

		return $data;
	}


	/**
	 * Desc: 检验签名是否有效
	 * @param array $data  数据
	 * @param string $appSecret 秘钥
	 * @param int $timeDiff  允许的时间差
	 * @return bool
	 * User: 青山有木
	 * Date: 2023/8/15
	 * Email: yz_luck@163.com
	 */
	public static function checkVerify(array $data, string $appSecret, $timeDiff = 300)
	{
		if (!isset($data['_timestamp']) || !isset($data['_sign']))
			return false; //Arguments missing
		$timestamp = is_numeric($data['_timestamp']) ? $data['_timestamp'] : strtotime($data['_timestamp']);
		if ($timestamp < (time() - $timeDiff) || $timestamp > (time() + $timeDiff))
			return false; //Invalid timestamp
		$originSign = $data['_sign'];
		unset($data['_sign']);
		ksort($data);
		$signStr = $appSecret;
		foreach ($data as $key => $val) {
			$val = strval($val);
			if ($key != '' && strpos($val, '@') !== 0)
				$signStr .= $key . $val;
		}
		$sign = strtoupper(md5($signStr . $appSecret));
		if ($sign !== $originSign)
			return false; //Signature verification failed
		return true;
	}

}