<?php
namespace HNova\Db;

use Exception;
use PDO;
use PDOStatement;

class Pull
{
    public PDO $pdo;
    private PDOStatement $stmt;
    public function __construct(mixed $arg = null)
    {
        if ( $arg instanceof PDO ){
            $this->pdo = $arg;
        }else {

            $this->pdo = $_ENV['nv-db']['pdo'];
        }
    }

    private function errorHandling(\Throwable $error){
        throw $error;
    }

    public function beginTransaccion(): bool {
        return $this->pdo->beginTransaction();
    }

    public function commit():bool{
        return $this->pdo->commit();
    }

    /**
     * @param string $sql Comando sql a ejecutar el la base de datos.
     * @param array $params array de los parametros para consultas preparadas
     */
    public function query(string $sql, array $params = null){

        try {
            $this->stmt = $this->pdo->prepare($sql);
        } catch (\Throwable $err) {
            $this->errorHandling(new Exception("*** Error al preparar la consulta SQL ***\n" . $err->getMessage() . "\n\nSQL: " . $sql, (int)$err->getCode(), $err->getPrevious()));
        }

        try {

            if ( $params ){
                foreach ( $params as $key => $value ){
                    if (is_object( $value ) || is_array( $value )) {
                        $params[$key] = json_encode( $value );
                    }
                }
            }
            
            $this->stmt->execute($params);
            return new Result($this->stmt);

        } catch (\Throwable $th) {  

            $msg =  "*** Error al ejecurtar la conuslta sql ***\n\n".$th->getMessage() . "\n\n" ."SQL: " . $sql . "\n"."params: " . json_encode($params);

            $this->errorHandling(new Exception(
                $msg,
                (int)$th->getCode(),
                $th->getPrevious()
            ));
        }
    }

    public function select(string $table, string|array $fields = '*', string $where = '', int $limit = null){

    }

    /**
     * @param array|object $params
     * @param string $table Nombre de la tabla a insertar los datos
     * @param string|string[] $returnning valores a retorna las consulta SQL, Nota: validao para mariadb +10.5
     */
    public function insert(array|object $params, string $table, string|array $returning  = null):Result{
        $params = (array)$params;
        $fields = "";
        $values = "";

        foreach ($params as $key => $value){
            $fields .= ", $key";
            $values .= ", :$key";
        }

        $fields = ltrim($fields, ', ');
        $values = ltrim($values, ', ');

        $return = "";

        if ( $returning ){
            if ( is_string($returning) ){
                $return = $returning;
            }else{
                foreach ($returning as $value){
                    $return .= ", $value";
                }
                $return = ltrim($return, ', ');
            }
            $return = " RETURNING $return";
        }

        return $this->query("INSERT INTO $table($fields) VALUES($values)$return", $params);
    }
    

    /**
     * @param array|string $condition
     */
    public function update(array|object $params, array|string $condition, string $table){

        $p = [];
        $values = "";
        $condit = "";
        foreach ( $params as $key => $value ){
            $values .= ", $key=:$key";
        }
        $values = ltrim($values, ', ');

        if (is_string($condition)){
            $condit = $condition;
        }else{
            $condit = $condition[0];
            $condit = str_replace(':', ':wp_',$condit);
            foreach ($condition[1] as $key => $value ){
                $params["wp_$key"] = $value;
            }
        }

        return $this->query("UPDATE $table SET $values WHERE $condit", $params );
    }

    public function delete($condition, $params, $table){
        
    }
}