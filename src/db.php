<?php
namespace HNova\Db;

use PDO;

class db
{
    private static Pull $_pull;

    public static function setPDO(PDO $pdo): void {
        $_ENV['nv-db']['pdo'] = $pdo;
    }

    public static function connect(string $type = 'mysql', string $host='localhost', string $user = 'root', string $dbname = '', string $password = '', string $port = null){
        $_ENV['nv-db']['type'] = $type;
        $_ENV['nv-db']['host'] = $host;
        $_ENV['nv-db']['user'] = $user;
        $_ENV['nv-db']['password'] = $password;
        $_ENV['nv-db']['port'] = $port;

        try{
            $pdo = new PDO("$type:host=$host; dbname=$dbname", $user, $password);
            $_ENV['nv-db']['pdo'] = $pdo;
            self::$_pull = new Pull();
            return true;
        } catch(\Throwable $err){
            $_ENV['nv-db']['error'] = $err;
            return false;
        }
    }

    public static function error():?\Throwable{
        return $_ENV['nv-db']['error'] ?? null;
    }

    public static function pull():Pull{
        return self::$_pull;
    }
}