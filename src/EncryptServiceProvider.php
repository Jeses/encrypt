<?php
/**
 * Created by PhpStorm
 * Desc:
 * User: 青山有木
 * Date: 2023/8/10
 * Email: yz_luck@163.com
 */

namespace Zhengcai\Encrypt;

use Illuminate\Support\ServiceProvider;

class EncryptServiceProvider extends ServiceProvider
{
	protected $defer = false;

	/**
	 * Desc:
	 * @return void
	 * User: 青山有木
	 * Date: 2023/8/10
	 * Email: yz_luck@163.com
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/config/encrypt.php' => config_path('encrypt.php'),
		]);
	}

	/**
	 * Desc:
	 * @return void
	 * User: 青山有木
	 * Date: 2023/8/10
	 * Email: yz_luck@163.com
	 */
	public function register()
	{
		$this->app->singleton('Encrypt', function () {
			return new EncryptService();
		});
	}

	/**
	 * Desc:
	 * @return string[]
	 * User: 青山有木
	 * Date: 2023/8/10
	 * Email: yz_luck@163.com
	 */
	public function provides()
	{
		return ['Encrypt'];
	}
}