<?php
namespace classes;

require_once 'HelpStatement.php';

/**
 *
 * @author Ortiz David
 * <br>Email: ortizaad1994@gmail.com <br>Tel: +244936166699
 * @name DDL
 * @desc Serve para executar aluguns comandos da DML
 * @copyright 2020
 */
class DML
{
    
    use HelpStatement;
    

    public static function descTable(string $tbName) : array
    {
        $sql = "DESCRIBE {$tbName};";
        return self::execArrayStmt($sql);
    }


    public static function showDatabases() : array
    {
        $sql = "SHOW DATABASES;";
        return self::execArrayStmt($sql);
    }


    public static function showDataBasesLike(string $dbName=Config::DB_NAME) : array
    {
        $sql = "SHOW DATABASES LIKE '%{$dbName}%'; ";
        return self::execArrayStmt($sql);
    }


    public static function showTables() : array
    {
        $sql = "SHOW TABLES;";
        return self::execArrayStmt($sql);
    }


    public static function showTablesFrom(string $tbName, string $dbName=Config::DB_NAME) : array
    {
        $sql = "SHOW TABLES FROM {$dbName} LIKE '%{$tbName}%'; ";
        return self::execArrayStmt($sql);
    }


    public static function showTableStatus(string $tbName, string $dbName=Config::DB_NAME) : array
    {
        $sql = "SHOW TABLE STATUS FROM {$dbName} LIKE '%{$tbName}%'; ";
        return self::execArrayStmt($sql);
    }


    public static function showOpenTables(string $tbName, string $dbName=Config::DB_NAME) : array
    {
        $sql = "SHOW OPEN TABLES FROM {$dbName} LIKE '%{$tbName}%'; ";
        return self::execArrayStmt($sql);
    }

    
    public static function showViews() : array
    {
        $sql = "SHOW VIEWS;";
        return self::execArrayStmt($sql);
    }


    public static function showIndexes() : array
    {
        $sql = "SHOW INDEXES;";
        return self::execArrayStmt($sql);
    }


    public static function showIndexesFrom(string $tbName) : array
    {
        $sql = "SHOW INDEXES FROM {$tbName}; ";
        return self::execArrayStmt($sql);
    }


    public static function showCreateTable(string $tbName) : object
    {
        $sql = "SHOW CREATE TABLE {$tbName};";
        return self::execObjectStmt($sql);
    }
    

    public static function showCreateFunction(string $funName) : object
    {
        $sql = "SHOW CREATE FUNCTION {$funName};";
        return self::execObjectStmt($sql);
    }


    public static function showCreateProcedure(string $procName) : object
    {
        $sql = "SHOW CREATE PROCEDURE {$procName};";
        return self::execObjectStmt($sql);
    }


    public static function showCreateTrigger(string $trName) : object
    {
        $sql = "SHOW CREATE TRIGGER {$trName};";
        return self::execObjectStmt($sql);
    }


    public static function showCreateView(string $vName) : object
    {
        $sql = "SHOW CREATE VIEW {$vName};";
        return self::execObjectStmt($sql);
    }


    public static function showCreateEvent(string $evtName) : object
    {
        $sql = "SHOW CREATE EVENT {$evtName};";
        return self::execObjectStmt($sql);
    }



}

