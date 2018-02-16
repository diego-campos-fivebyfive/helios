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
 * Simple SQL formatter
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
abstract class SQLFormatter
{
    /**
     * @param string $table
     * @param string|null $criteria
     * @return string
     */
    public static function select(string $table, string $criteria = null) : string
    {
        $sql = "SELECT * FROM {$table}";

        if($criteria) {
            $sql .= " WHERE {$criteria}";
        }

        return $sql;
    }

    /**
     * @param $table
     * @param array $data
     * @return string
     */
    public static function insert($table, array $data)
    {
        $fields = array_keys($data);

        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(', ', $fields), '%s');

        array_walk($fields, function(&$field){ $field = ':' . $field; });

        $sql = sprintf($sql, implode(', ', $fields), '%s');

        return $sql;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function bindEquals(array $data)
    {
        $fields = array_keys($data);
        array_walk($fields, function(&$field){
            $field = $field . ' = :' . $field;
        });

        return $fields;
    }
}
