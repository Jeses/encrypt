<?php
/**
 * Created by PhpStorm
 * Desc:
 * User: 青山有木
 * Date: 2023/8/10
 * Email: yz_luck@163.com
 */

namespace Zhengcai\Encrypt\Facades;

use Illuminate\Support\Facades\Facade;

class Encrypt extends Facade
{
	/**
	 * Desc:
	 * @return string
	 * User: 青山有木
	 * Date: 2023/8/10
	 * Email: yz_luck@163.com
	 */
	protected static function getFacadeAccessor()
	{
		return 'Encrypt';
	}
}