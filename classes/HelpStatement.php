<?php
namespace classes;

//use Connection;
use PDO;
use PDOException;

require_once 'Connection.php';

trait HelpStatement
{
  
    
    public static function execBoolStmt(string $sql) : bool
    {
        try {
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $retorno = $stmt->execute();
            $pdo->commit();
            Connection::disconnect();
            return $retorno;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    public static function execArrayStmt(string $sql) : array
    {
        try {
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $retorno = $stmt->fetchAll(PDO::FETCH_OBJ);
            $pdo->commit();
            Connection::disconnect();
            return $retorno;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    public static function execObjectStmt(string $sql) : object
    {
        try {
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $retorno = $stmt->fetch(PDO::FETCH_OBJ);
            $pdo->commit();
            Connection::disconnect();
            return $retorno;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    
}

