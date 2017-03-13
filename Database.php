<?php
namespace Hutong\Database;
/**
* @desc 数据库连接
*/
class Database
{
    protected static $instance;

    public static function getInstance($config, $dbName = 'default')
    {
        if (!isset(self::$instance[$dbName])) {
            if(isset($config['type'])){
                $class = "HuTong\Database\Drive\\".$config['type'];
            }else{
                throw new \Exception('数据库类型不能为空');
            }
            self::$instance[$dbName] = new $class($config);
        }
        return self::$instance[$dbName];
    }
}