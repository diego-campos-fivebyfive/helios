<?php

namespace App\Sices\Persistence;

class Connection
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Connection constructor.
     * @param array $config
     */
    private function __construct(array $config = [])
    {
        extract($config);

        $host = isset($host) ? $host : getenv('CES_SICES_DATABASE_HOST');
        $port = isset($port) ? $port : getenv('CES_SICES_DATABASE_PORT');
        $name = isset($name) ? $name : getenv('CES_SICES_DATABASE_NAME');
        $user = isset($user) ? $user : getenv('CES_SICES_DATABASE_USER');
        $pass = isset($pass) ? $pass : getenv('CES_SICES_DATABASE_PASS');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $host, $port, $name);

        $this->pdo = new \PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param $table
     * @param array $data
     * @return bool
     */
    public function insert($table, array $data)
    {
        $fields = array_keys($data);

        //$sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(', ', $fields), '%s');
        $sql = SQLFormatter::formatInsert($table, $data);

        array_walk($fields, function(&$field){
            $field = ':' . $field;
        });

        $sql = sprintf($sql, implode(', ', $fields), '%s');

        $stmt = $this->pdo->prepare($sql);

        $params = array_combine($fields, array_values($data));

        return $stmt->execute($params);
    }

    public function update($table, array $data)
    {

    }

    /**
     * @param array $config
     * @return Connection
     */
    public static function create(array $config = [])
    {
        return new self($config);
    }
}
