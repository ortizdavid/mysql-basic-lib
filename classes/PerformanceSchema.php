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
 * @desc Obtém as informações da Base de Dados PERFORMANCE_SCHEMA
 */
class PerformanceSchema
{

    /**
     * @var $database
     * @desc Base de dados que está em uso
     */
    private static string $database = 'PERFORMANCE_SCHEMA';


    /**@author Ortiz David
     * @copyright 2020
     * @name getAccounts
     * @desc Retorna todas as conexões dos usuários<br>
     * Número de Conexões actuais e total de conexões
     * @return array
     *
     * */
    public static function getAccounts() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.accounts; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getGlobalStatus
     * @desc 
     * @return array
     *
     * */
    public static function getGlobalStatus() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.global_status; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getGlobalVariables
     * @desc 
     * @return array
     *
     * */
    public static function getGlobalVariables() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.global_variables; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getHosts
     * @desc 
     * @return array
     *
     * */
    public static function getHosts() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.hosts; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getObjSummaryGlobal
     * @desc 
     * @param string $dbName
     * @return array
     *
     * */
    public static function getObjSummaryGlobal(string $dbName=Config::DB_NAME) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.objects_summary_global_by_type 
                    WHERE object_schema = '{$dbName}'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getSessionConnectAttr
     * @desc 
     * @return array
     *
     * */
    public static function getSessionConnectAttr() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.session_connect_attrs; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getSessionAcountConnect
     * @desc 
     * @return array
     *
     * */
    public static function getSessionAcountConnect() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.session_account_connect_attrs; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getSetupActors
     * @desc 
     * @return array
     *
     * */
    public static function getSetupActors() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.setup_actors; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getSetupObjects
     * @desc 
     * @return array
     *
     * */
    public static function getSetupObjects() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.setup_objects; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getStatusByAccount
     * @desc 
     * @param string $userName
     * @param string $host
     * @return array
     *
     * */
    public static function getStatusByAccount(string $userName=Config::DB_USER, string $host=Config::DB_HOST) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.status_by_account 
                    WHERE user = '{$userName}' AND host = '{$host}' ; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getStatusByHost
     * @desc 
     * @param string $host
     * @return array
     *
     * */
    public static function getStatusByHost(string $host=Config::DB_HOST) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.status_by_host WHERE host = '{$host}' ; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getStatusByUsers
     * @desc 
     * @param string $userName
     * @return array
     *
     * */
    public static function getStatusByUser(string $userName=Config::DB_USER) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.status_by_user WHERE user = '{$userName}' ; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getTableHandles
     * @desc 
     * @param string $dbName
     * @return array
     *
     * */
    public static function getTableHandles(string $dbName=Config::DB_NAME) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.table_handles 
                    WHERE object_schema = '{$dbName}' ; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getTableWaitsSumByIndex
     * @desc 
     * @param string $dbName
     * @return array
     *
     * */
    public static function getTableWaitsSumByIndex(string $dbName=Config::DB_NAME) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.table_io_waits_summary_by_index_usage 
                    WHERE object_schema = '{$dbName}' ; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getTableWaitsSumByTable
     * @desc 
     * @param string $dbName
     * @return array
     *
     * */
    public static function getTableWaitsSumByTable(string $dbName=Config::DB_NAME) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.table_io_waits_summary_by_table 
                    WHERE object_schema = '{$dbName}' ; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getTableLockWaitsSumByTable
     * @desc 
     * @param string $dbName
     * @return array
     *
     * */
    public static function getTableLockWaitsSumByTable(string $dbName=Config::DB_NAME) : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.table_lock_waits_summary_by_table 
                    WHERE object_schema = '{$dbName}' ; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
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
     * @name getUsers
     * @desc 
     * @return array
     *
     * */
    public static function getUsers() : array
    {
        try {
            $db = self::$database;
            $sql = "SELECT * FROM {$db}.users; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


}
