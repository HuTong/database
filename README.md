# database
### yaf 配置模式
```
数据库连接 多库连接配置
;数据库配置信息
database.default.driver     = "mysql"
database.default.type       = "Pdos"
database.default.host       = "127.0.0.1"
database.default.name       = "yaf"
database.default.user       = "root"
database.default.password   = "root"
database.default.port       = 3306
database.default.charset    = utf8
;other
database.other.driver     = "mysql"
database.other.type       = "Pdos"
database.other.host       = "127.0.0.1"
database.other.name       = "other"
database.other.user       = "root"
database.other.password   = "root"
database.other.port       = 3306
database.other.charset    = utf8
```
### 常量配置模式
array(
    'default' => array(
        'driver' => 'mysql',
        'type' => 'Pdos',
        'host' => '127.0.0.1',
        'name' => 'yaf',
        'user' => 'root',
        'password' => 'root',
        'port' => '3306',
        'charset' => 'utf8'
    ),
    'other' => array(
        'driver'    => 'mysql',
        'type'      => 'Pdos',
        'host'      => '127.0.0.1',
        'name'      => 'other',
        'user'      => 'root',
        'password'  => 'root',
        'port'      => '3306',
        'charset'   => 'utf8'
    )
)
### 学习交流群
630730920
