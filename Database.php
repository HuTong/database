<?php
/**
* @desc 数据库连接
*/
namespace Hutong\Database;

class Database
{
    private $config;
    private $instance;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getInstance($dbName = null)
    {
        $dbName = is_null($dbName) ? 'default': $dbName;
        
        if(!isset($this->config[$dbName]) || empty($this->config[$dbName]))
        {
            throw new \Exception('连接的类型不存在');
        }

        $config = $this->config[$dbName];

        if (!isset($this->instance[$dbName]))
        {
            if (isset($config['type']))
            {
                $class = "HuTong\Database\Drive\\".$config['type'];
            } else {
                throw new \Exception('数据库类型不能为空');
            }

            $this->instance[$dbName] = new $class($config);
        }

        return $this->instance[$dbName];
    }
}
