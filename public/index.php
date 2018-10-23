<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义项目路径
define('APP_PATH', __DIR__ . '/../application/');
// 定义上传路径
define('UPLOAD_PATH', __DIR__ . '/uploads/');
// 定义数据目录
define('DATA_PATH', __DIR__ . '/../data/');
// 定义配置目录
define('CRET_PATH', DATA_PATH . 'cret/');
// 定义EXTEND目录
define('EXTEND_PATH', DATA_PATH . 'extend/');
// 定义RUNTIME目录
define('RUNTIME_PATH', DATA_PATH . 'runtime/');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
