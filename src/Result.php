<?php
namespace HNova\Db;

use PDO;
use PDOStatement;

class Result
{
    public int $rowCount = 0;

    public function __construct(private PDOStatement $stmt)
    {
        $this->rowCount = $stmt->rowCount();
    }

    /**
     * @param int $mode numm: 3, assoc: 4, object: 4, class: 8
     */
    public function rows(int $mode = PDO::FETCH_ASSOC, mixed ...$args):array{
        return $this->stmt->fetchAll($mode, ...$args);
    }

    /**
     * Retorna un array del nombre de las columnas
     */
    public function fields():array {
        $list = [];
        for ( $i = 0; $i < $this->stmt->columnCount(); $i++ ){
            $list[] = $this->stmt->getColumnMeta($i)['name'];
        }
        return $list;
    }

    /**
     * Retorna un array con la data de las columnas resultantes de la consulta SQL
     */
    public function columsData():array{
        $list = [];
        for ( $i = 0; $i < $this->stmt->columnCount(); $i++ ){
            $list[] = $this->stmt->getColumnMeta($i);
        }
        return $list;
    }
}