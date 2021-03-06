<?php
namespace Hutong\Database;

interface Contract
{
    public function query($sql);

    public function exec($sql, $lastId = false);

    public function select($sql);

    public function find($sql);

    public function getOne($sql);

    public function getCol($sql);

    public function insert($table, $datas, $lastId = true);

    public function update($table, $datas, $where = null);

    public function delete($table, $where = null);

    public function begin();

    public function errorCode();

    public function rollBack();

    public function commit();
}
