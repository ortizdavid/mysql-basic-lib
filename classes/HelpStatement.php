<?php
namespace classes;

//use Connection;
use PDO;
use PDOException;

require_once 'Connection.php';

/**
 * * @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @name HelpStatement
 * @desc Forence os métodos re retorno das consultas na base de dados
 */
trait HelpStatement
{
  
    
    public static function execBoolStmt(string $sql) : bool
    {
        try {
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute();
            $pdo->commit();
            Connection::disconnect();
            return $result;
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
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $pdo->commit();
            Connection::disconnect();
            return $result;
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
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    
}

