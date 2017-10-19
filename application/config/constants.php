<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');
/** 模块根目录 */
define('EXTEND_ROOT', '_extend');

define("GROUP_EVERYONE",0);//不设置权限 但显示在菜单
define("GROUP_ADMIN",1);//设置权限 且在菜单中显示
define("GROUP_HIDE",2);//隐藏（菜单和权限设置中都不显示）

define('DELETEPOWER', 8); //0x1000
define('ADDPOWER', 4); //0x1000
define('EDITPOWER', 2); //0x0100
define('VIEWPOWER', 1);//0x0010

define('DELETED',1);
define('UNDELETED',0);

define('STATUS_NORMAL','1');
define('STATUS_FORBIDEN','0');

/** 默认货币 */
define('DEFAULT_CURRENCY','USD');

/* End of file constants.php */
/* Location: ./application/config/constants.php */