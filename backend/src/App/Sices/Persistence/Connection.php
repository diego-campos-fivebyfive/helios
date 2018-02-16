<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Sices\Persistence;

/**
 * This class provides the independent basic connection for crud operations
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Connection
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Connection
     */
    private static $instance;

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

        self::$instance = $this;
    }

    /**
     * @param $table
     * @param string|null $criteria
     * @param array $params
     * @return array
     */
    public function select($table, string $criteria = null, array $params = [])
    {
        $sql = SQLFormatter::select($table, $criteria);

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $table
     * @param array $data
     * @return bool
     */
    public function insert($table, array $data)
    {
        $fields = array_keys($data);

        $sql = SQLFormatter::insert($table, $data);

        $stmt = $this->pdo->prepare($sql);

        $params = array_combine($fields, array_values($data));

        return $stmt->execute($params);
    }

    /**
     * @param $table
     * @param array $data
     * @param int|null $id
     * @return int
     */
    public function update($table, array $data, int $id = null)
    {
        $params = [];

        $setFields = SQLFormatter::bindEquals($data);
        $setSQL = implode(', ', $setFields);

        $updateSQL = sprintf('UPDATE %s SET %s', $table, $setSQL);

        if($id) {
            $updateSQL .= sprintf(' WHERE id = :id');
            $params[':id'] = $id;
        }

        foreach ($data as $field => $value){
            $params[":{$field}"] = $value;
        }

        $stmt = $this->pdo->prepare($updateSQL);

        $stmt->execute($params);

        return $stmt->rowCount();
    }

    /**
     * @param $table
     * @param $id
     * @return bool
     */
    public function delete($table, $id)
    {
        $sql = "DELETE FROM {$table} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param array $config
     * @return Connection
     */
    public static function create(array $config = [])
    {
        return self::$instance ? self::$instance : new self($config);
    }
}
