<?php
//中心库连接配置
$IOSS_Config['db']['default']['hostname'] = '192.168.199.86';
$IOSS_Config['db']['default']['username'] = 'ioss';
$IOSS_Config['db']['default']['password'] = 'ioss';
$IOSS_Config['db']['default']['database'] = 'fifa_v2';
$IOSS_Config['db']['default']['dbdriver'] = 'mysqli';
$IOSS_Config['db']['default']['dbprefix'] = 'core_';
$IOSS_Config['db']['default']['pconnect'] = TRUE;
$IOSS_Config['db']['default']['db_debug'] = TRUE;
$IOSS_Config['db']['default']['cache_on'] = FALSE;
$IOSS_Config['db']['default']['cachedir'] = '';
$IOSS_Config['db']['default']['char_set'] = 'utf8';
$IOSS_Config['db']['default']['dbcollat'] = 'utf8_general_ci';
$IOSS_Config['db']['default']['swap_pre'] = '';
$IOSS_Config['db']['default']['autoinit'] = TRUE;
$IOSS_Config['db']['default']['stricton'] = FALSE;



//Memory Cache的连接配置
$IOSS_Config['memcache']['game']['host'] = '127.0.0.1';
$IOSS_Config['memcache']['game']['port'] = '11211';

$IOSS_Config['papal_notify_url'] = 'https://fqs.zhangyk.org/common/api/ipn';

//内部IP
$IOSS_Config['selfIp'] = array(
	'127.0.0.1',
);

$IOSS_Config['log_path'] = '/home/data/logs/IOSS/';
