<?php
/**
 * Advanced Security - PHP Register/Login System
 *
 * @author Milos Stojanovic
 * @link   http://mstojanovic.net/as
 */

/**
 * Class ASDatabase
 */
class ASDatabase extends PDO
{
    protected $debug = false;

    /**
     * Class constructor
     * Parameters defined as constants in ASConfig.php file
     * @param $type string Database type
     * @param $host string Database host
     * @param $databaseName string Database username
     * @param $username string User's username
     * @param $password string Users's password
     */
    public function __construct($type, $host, $databaseName, $username, $password)
    {
        parent::__construct($type.':host='.$host.';dbname='.$databaseName.';charset=utf8', $username, $password);
        $this->exec('SET CHARACTER SET utf8');

        if ($this->debug) {
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
    }

    /**
     * Enable/disable debug for database queries.
     * @param $debug boolean TRUE to enable debug, FALSE otherwise.
     */
    public function debug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * Select
     * @param $sql string An SQL string.
     * @param $array array Parameters to bind.
     * @param $fetchMode int A PDO Fetch mode.
     * @return array
     */
    public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
    {
        $sth = $this->prepare($sql);

        foreach ($array as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();

        return $sth->fetchAll($fetchMode);
    }

    /**
     * Insert data to database.
     * @param $table string A name of table to insert into
     * @param $data string An associative array
     */
    public function insert($table, array $data)
    {
        ksort($data);

        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));

        $sth = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

    /**
     * Update
     * @param $table string A name of table to insert into.
     * @param $data array An associative array where keys have the same name as database columns.
     * @param $where string the WHERE query part.
     * @param $whereBindArray array Parameters to bind to where part of query.
     */
    public function update($table, $data, $where, $whereBindArray = array())
    {
        ksort($data);

        $fieldDetails = null;

        foreach ($data as $key => $value) {
            $fieldDetails .= "`$key`=:$key,";
        }

        $fieldDetails = rtrim($fieldDetails, ',');

        $sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        foreach ($whereBindArray as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

    /**
     * Delete
     *
     * IF YOU USE PREPARED STATEMENTS, DON'T FORGET TO UPDATE $bind ARRAY!
     *
     * @param $table
     * @param $where
     * @param array $bind
     * @param int $limit
     */
    public function delete($table, $where, $bind = array(), $limit = null)
    {
        $query = "DELETE FROM $table WHERE $where";

        if ($limit) {
            $query .= " LIMIT $limit";
        }

        $sth = $this->prepare($query);

        foreach ($bind as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }
}
