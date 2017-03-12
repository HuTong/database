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
            if (!empty($port)) {
                $dsn .= ";port=$port";
            }
            $dsn .= ";dbname=$name";

            if(isset($this->config['charset'])){
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
                    $this->config['s']['driver'], 
                    $this->config['s']['host'],
                    $this->config['s']['name'], 
                    $this->config['s']['user'],
                    $this->config['s']['password'], 
                    $this->config['s']['port']
            );
        }
        return $this->link;
    }

	public function query($sql, $use_master = true)
	{
        $link = $this->getLink($use_master);
        $sth = $link->prepare($sql);
        $sth->execute();
        
        return $sth;
	}

	public function exec($sql,$lastId = false)
	{
        $link = $this->getLink(true);
        if($lastId){
            $link->exec($sql);
            return $link->lastInsertId();
        }else{
            return $link->exec($sql);
        }
	}

	public function select($sql, $use_master = false)
	{
        $sth = $this->query($sql, $use_master);

        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        
        return $result;
	}

    public function find($sql, $use_master = false)
    {
        $result = $this->select($sql, $use_master);
        
        return isset($result['0']) ? $result['0'] : array();
    }

    public function queryScalar($sql, $use_master = false)
    {
        $result = $this->find($sql, $use_master);

        return isset($result) ? (int)array_shift($result) : 0;
    }

	public function insert($table, $datas)
	{
        $values = array();
        $columns = array();

        $link = $this->getLink(true);

        foreach ($datas as $key => $value) {
            array_push($columns, $this->column_quote($key));
            array_push($values, $this->fn_quote($value, $link));
        }

        $sql = 'INSERT INTO "' . $table . '" (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')';

        return $this->exec($sql,true);
	}

	public function update($table, $data, $where = null)
	{
        $fields = array();

        $link = $this->getLink(true);

        foreach ($datas as $key => $value) {
            $fields[] = $this->column_quote($key) . ' = ' . $this->fn_quote($value, $link);
        }

        $sql = 'UPDATE "' . $this->prefix . $table . '" SET ' . implode(', ', $fields);
        
        if(!is_null($where)){
            $sql .= " where ".$where;
        }

        return $this->exec($sql);
	}

	public function delete($table, $where = null)
	{
        $sql = 'DELETE FROM "' . $table;

        if(!is_null($where)){
            $sql .= " where ".$where;
        }

        return $this->exec($sql);
	}

    private function quote($string, $link)
    {
        return $link->quote($string);
    }

    private function column_quote($string)
    {
        return '"' . str_replace('.', '"."', preg_replace('/(^#|\(JSON\)\s*)/', '', $string)) . '"';
    }

    private function fn_quote($string, $link)
    {
        return preg_match('/^[A-Z0-9\_]*\([^)]*\)$/', $string) ? $string : $this->quote($string, $link);
    }

    public function getRealEscapeString($string)
    {
        return preg_match('/^[A-Z0-9\_]*\([^)]*\)$/', $string) ? $string : addslashes($string);
    }
}