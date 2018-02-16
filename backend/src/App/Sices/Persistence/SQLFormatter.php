<?php

namespace App\Sices\Persistence;

abstract class SQLFormatter
{
    public static function formatInsert($table, array $data)
    {
        $fields = array_keys($data);

        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(', ', $fields), '%s');

        return $sql;
    }

}
