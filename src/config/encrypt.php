<?php
/**
 * Created by PhpStorm
 * Desc:
 * User: 青山有木
 * Date: 2023/8/10
 * Email: yz_luck@163.com
 */
return [
	// 适配不同业务标识调用加密方法
	'test' => [
		//SM4 使用的ecb 模式，并且使用TOKEN_KEY字段
		'TOKEN_KEY' => 'F5A74513CEF42A3A', // 加密随机串，长度应大于等于16位
		// 以下JWT使用
		'TOKEN_EXPIRE' => 86400 * 31, // token 有效期单位秒
		'TOKEN_PREFIX' => 'TOKEN_PREFIX_%s_%s', // token缓存前缀
		'TOKEN_ISS' => 'zy-scheduling', // token签发者标识
		'TOKEN_ALG' => 'HS256', // token加密算法 ES384,ES256,HS256,HS384,HS512,RS256,RS384,RS512
	],
];