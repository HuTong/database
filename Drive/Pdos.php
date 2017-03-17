<?php
/**
* 数据库处理
*/
namespace Hutong\Database\Drive;

class Pdos
{
    private $config;
    private $link;

    public function __construct($config)
    {
        $this->config = $config;
    }

    private function connection($driver, $host, $name, $user, $password, $port = null)
    {
        try {
            $dsn = $driver . ':host=' . $host;
            if (!empty($port))
            {
                $dsn .= ";port=$port";
            }
            $dsn .= ";dbname=$name";

            if(isset($this->config['charset']))
            {
                $charset = $this->config['charset'];
            }else{
               $charset = 'utf8';
            }

            $options = array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$charset,
            );

            $conn = new \PDO($dsn, $user, $password,$options);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (\PDOException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getLink()
    {
        if(is_null($this->link))
        {
            $this->link = $this->connection(
                    $this->config['driver'],
                    $this->config['host'],
                    $this->config['name'],
                    $this->config['user'],
                    $this->config['password'],
                    $this->config['port']
            );
        }
        return $this->link;
    }

    public function query($sql)
    {
        $link = $this->getLink();
        $sth = $link->prepare($sql);
        $sth->execute();

        return $sth;
    }

    public function exec($sql,$lastId = false)
    {
        $link = $this->getLink();
        if($lastId)
        {
            $link->exec($sql);
            return $link->lastInsertId();
        }else{
            return $link->exec($sql);
        }
    }

    public function select($sql)
    {
        $sth = $this->query($sql);

        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $result ? $result : array();
    }

    public function find($sql)
    {
        $sth = $this->query($sql);

        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result : array();
    }

    public function getOne($sql)
    {
        $sth = $this->query($sql);

        $result = $sth->fetch(\PDO::FETCH_NUM);

        return isset($result['0']) ? $result['0'] : '';
    }

    public function insert($table, $datas)
    {
        $values = array();
        $columns = array();

        $link = $this->getLink();

        foreach ($datas as $key => $value)
        {
            array_push($columns, $this->column_quote($key));
            array_push($values, $this->fn_quote($value, $link));
        }

        $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')';
        
        return $this->exec($sql,true);
    }

    public function update($table, $datas, $where = null)
    {
        $fields = array();

        $link = $this->getLink();

        foreach ($datas as $key => $value)
        {
            $fields[] = $this->column_quote($key) . ' = ' . $this->fn_quote($value, $link);
        }

        $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $fields);

        if(!is_null($where))
        {
            if(is_array($where))
            {
                $whereArr = array();
                foreach ($where as $key => $value)
                {
                    $whereArr[] = "`".$key."` = ".$value;
                }
                $sql .= " where ".implode(' AND ', $whereArr);
            }else{
                $sql .= " where ".$where;
            }
        }

        return $this->exec($sql);
    }

    public function delete($table, $where = null)
    {
        $sql = 'DELETE FROM `' . $table . '`';

        if(!is_null($where))
        {
            if(is_array($where))
            {
                $whereArr = array();
                foreach ($where as $key => $value)
                {
                    $whereArr[] = "`".$key."` = ".$value;
                }
                $sql .= " where ".implode(' AND ', $whereArr);
            }else{
                $sql .= " where ".$where;
            }
        }
        
        return $this->exec($sql);
    }

    public function begin()
    {
        $link = $this->getLink();

        $link->beginTransaction();
    }

    public function errorCode()
    {
        $link = $this->getLink();

        return $link->errorCode();
    }

    public function rollBack()
    {
        $link = $this->getLink();

        $link->rollBack();
    }

    public function commit()
    {
        $link = $this->getLink();

        $link->commit();
    }

    private function quote($string, $link)
    {
        return $link->quote($string);
    }

    private function column_quote($string)
    {
        return '`' . str_replace('.', '"."', preg_replace('/(^#|\(JSON\)\s*)/', '', $string)) . '`';
    }

    private function fn_quote($string, $link)
    {
        return preg_match('/^[A-Z0-9\_]*\([^)]*\)$/', $string) ? $string : $this->quote($string, $link);
    }

    public function __call($method, $parameters)
    {
        $link = $this->getLink();
        return $link->$method(...$parameters);
    }
}
