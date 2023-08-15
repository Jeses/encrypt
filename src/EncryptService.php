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

}