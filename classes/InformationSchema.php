<?php
namespace classes;

//use Connection;
use PDO;
use PDOException;

require_once 'Config.php';
require_once 'Connection.php';

/**
* @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @name InformationSchema
 * @desc Obtém as informações da Base de Dados INFORMATION_SCHEMA
 */
class InformationSchema
{
   
    /**
     * @var $database
     * @desc Base de dados que está em uso
     */
    private static string $database = 'INFORMATION_SCHEMA';
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getAllTables
     * @desc Retorna todas as tabelas e seus Ids
     * @param string $dbName
     * @return array
     *
     * */
    public static function getAllTables(string $dbName=Config::DB_NAME) : array
    {
        try {
            $table = self::$database.".KEY_COLUMN_USAGE";
            $table2 = self::$database.".TABLES";
            $sql = "SELECT {$table}.TABLE_NAME,
                       {$table}.COLUMN_NAME,
                       {$table2}.ENGINE,
                       {$table2}.TABLE_TYPE,
                       {$table2}.ROW_FORMAT,
                       {$table2}.TABLE_COLLATION,
                       {$table2}.TABLE_ROWS,
                       {$table2}.AVG_ROW_LENGTH,
                       {$table2}.DATA_LENGTH,
                       {$table2}.MAX_DATA_LENGTH,
                       {$table2}.INDEX_LENGTH,
                       {$table2}.AUTO_INCREMENT,
                       {$table2}.CREATE_TIME,
                       {$table2}.UPDATE_TIME,
                       {$table2}.CHECK_TIME
                    FROM {$table}
                    JOIN {$table2} ON({$table}.TABLE_NAME = {$table2}.TABLE_NAME)
                    WHERE {$table}.CONSTRAINT_NAME = 'PRIMARY' 
                       AND {$table}.TABLE_SCHEMA = :dbname
                    GROUP BY {$table}.TABLE_NAME; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getAllProcedures
     * @desc Retorna todos os Procedures criados
     * @param string $dbName
     * @return array
     *
     * */
    public static function getAllProcedures(string $dbName=Config::DB_NAME) : array
    {
        try {
            $table = self::$database.".ROUTINES";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE ROUTINE_TYPE = 'PROCEDURE'
                       AND ROUTINE_SCHEMA = :dbname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getProcedure
     * @desc Retorna um procedure
     * @param string $dbName
     * @return object
     *
     * */
    public static function getProcedure(string $procName, string $dbName=Config::DB_NAME) : object
    {
        try {
            $table = self::$database.".ROUTINES";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE ROUTINE_TYPE = 'PROCEDURE'
                       AND ROUTINE_SCHEMA = :dbname
                       AND {$table}.ROUTINE_SCHEMA = :dbname
                       AND {$table}.SPECIFIC_NAME = :procname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->bindParam(':procname', $procName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getAllFunctions
     * @desc Retorna todas as funções criadas
     * @param string $dbName
     * @return array
     *
     * */
    public static function getAllFunctions(string $dbName=Config::DB_NAME) : array
    {
        try {
            $table = self::$database.".ROUTINES";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE ROUTINE_TYPE = 'FUNCTION'
                       AND ROUTINE_SCHEMA = :dbname; ";
           $pdo = Connection::connect();
           $stmt = $pdo->prepare($sql);
           $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
           $stmt->execute();
           $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
           Connection::disconnect();
           return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getFunction
     * @desc Retorna uma function
     * @param string $dbName
     * @return object
     *
     * */
    public static function getFunction(string $funName, string $dbName=Config::DB_NAME) : object
    {
        try {
            $table = self::$database.".ROUTINES";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE ROUTINE_TYPE = 'FUNCTION'
                       AND ROUTINE_SCHEMA = :dbname
                       AND {$table}.ROUTINE_SCHEMA = :dbname
                       AND {$table}.SPECIFIC_NAME = :funname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->bindParam(':funname', $funName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getTable
     * @desc Retorna uma Tabela
     * @param string $dbName
     * @return object
     *
     * */
    public static function getTable(string $tbName, string $dbName=Config::DB_NAME) : object
    {
        try {
            $table = self::$database.".KEY_COLUMN_USAGE";
            $table2 = self::$database.".TABLES";
            $sql = "SELECT {$table}.TABLE_NAME 'tabela',
                       {$table}.COLUMN_NAME 'identificador',
                       {$table}.COLUMN_NAME 'identificador',
                       {$table2}.ENGINE,
                       {$table2}.TABLE_TYPE,
                       {$table2}.ROW_FORMAT,
                       {$table2}.TABLE_COLLATION,
                       {$table2}.TABLE_ROWS,
                       {$table2}.AVG_ROW_LENGTH,
                       {$table2}.DATA_LENGTH,
                       {$table2}.MAX_DATA_LENGTH,
                       {$table2}.INDEX_LENGTH,
                       {$table2}.AUTO_INCREMENT,
                       {$table2}.CREATE_TIME,
                       {$table2}.UPDATE_TIME,
                       {$table2}.CHECK_TIME
                    FROM {$table}
                    JOIN {$table2} ON({$table}.TABLE_NAME = {$table2}.TABLE_NAME)
                    WHERE {$table}.CONSTRAINT_NAME = 'PRIMARY'
                       AND {$table}.TABLE_SCHEMA = :dbname
                       AND {$table}.TABLE_NAME = :tbname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->bindParam(':tbname', $tbName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getCountTables
     * @desc Faz o Count de tabelas da base de dados
     * @param string $dbName
     * @return int
     *
     * */
    public static function getCountTables(string $dbName=Config::DB_NAME) : int
    {
        try {
            $table = self::$database.".TABLES";
            $sql = "SELECT count(*) 'Count'
                    FROM {$table}
                    WHERE {$table}.TABLE_SCHEMA = :dbname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado->Count;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getColumns
     * @desc Retorna as Colunas de uma tabela
     * @param string $dbName
     * @return array
     *
     * */
    public static function getColumns(string $tbName, string $dbName=Config::DB_NAME) : array
    {
        try {
            $table = self::$database.".COLUMNS";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE {$table}.TABLE_SCHEMA = :dbname
                       AND {$table}.TABLE_NAME = :tbname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->bindParam(':tbname', $tbName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getAllTriggers
     * @desc Retorna todos os Triggers criados
     * @param string $dbName
     * @return array
     *
     * */
    public static function getAllTriggers(string $dbName=Config::DB_NAME) : array
    {
        try {
            $table = self::$database.".TRIGGERS";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE {$table}.TRIGGER_SCHEMA = :dbname; ";
           $pdo = Connection::connect();
           $stmt = $pdo->prepare($sql);
           $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
           $stmt->execute();
           $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
           Connection::disconnect();
           return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getTrigger
     * @desc Retorna um Trigger
     * @param string $dbName
     * @return array
     *
     * */
    public static function getTrigger(string $trName, string $dbName=Config::DB_NAME) : object
    {
        try {
            $table = self::$database.".TRIGGERS";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE {$table}.TRIGGER_SCHEMA = :dbname
                      AND TRIGGER_NAME = :trname; ";
           $pdo = Connection::connect();
           $stmt = $pdo->prepare($sql);
           $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
           $stmt->bindParam(':trname', $trName, PDO::PARAM_STR);
           $stmt->execute();
           $resultado = $stmt->fetch(PDO::FETCH_OBJ);
           Connection::disconnect();
           return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getAllStatistics
     * @desc Retorna todas as estatisticas
     * @param string $dbName
     * @return array
     *
     * */
    public static function getAllStatistics(string $dbName=Config::DB_NAME) : array
    {
        try {
            $table = self::$database.".STATISTICS";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE {$table}.TABLE_SCHEMA = :dbname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getStatistics
     * @desc Retorna todas as estatisticas
     * @param string $dbName
     * @return object
     *
     * */
    public static function getStatistics(string $tbName, string $dbName=Config::DB_NAME) : object
    {
        try {
            $table = self::$database.".STATISTICS";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE {$table}.TABLE_SCHEMA = :dbname
                       AND {$table}.TABLE_NAME = :tbname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->bindParam(':tbname', $tbName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getAllEvents
     * @desc Retorna todos os Eventos criados
     * @param string $dbName
     * @return array
     *
     * */
    public static function getAllEvents(string $dbName=Config::DB_NAME) : array
    {
        try {
            $table = self::$database.".EVENTS";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE {$table}.EVENT_SCHEMA = :dbname; ";
           $pdo = Connection::connect();
           $stmt = $pdo->prepare($sql);
           $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
           $stmt->execute();
           $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
           Connection::disconnect();
           return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name getEvent
     * @desc Retorna um Evento criado
     * @param string $dbName
     * @return object
     *
     * */
    public static function getEvent(string $evtName, string $dbName=Config::DB_NAME) : object
    {
        try {
            $table = self::$database.".EVENTS";
            $sql = "SELECT *
                    FROM {$table}
                    WHERE {$table}.EVENT_SCHEMA = :dbname
                       AND {$table}.EVENT_NAME = :evtname; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dbname', $dbName, PDO::PARAM_STR);
            $stmt->bindParam(':evtname', $evtName, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    
}
