<?php
namespace classes;

require_once 'HelpStatement.php';

/**
 *
 * @author Ortiz David
 * <br>Email: ortizaad1994@gmail.com <br>Tel: +244936166699
 * @name DDL
 * @desc Serve para executar aluguns comandos da DDL
 * @copyright 2020
 */
class DDL
{
    
    use HelpStatement;
    
    
    public static function createDatabase(string $dbName, string $collate, array $options=null) : bool
    {
        $strOptions = "";
        if($options != null){
            foreach ($options as $key => $value)
                $strOptions .= " {$key} = {$value} ";
        }
        $sql = "CREATE DATABASE IF NOT EXISTS {$dbName} COLLATE {$collate} {$strOptions};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function createTable(string $tbName, array $fields) : bool
    {
        $strFields = "";
        $cont=0;
        foreach ($fields as $field => $def){
            $cont++;
            $strFields .= ($cont == count($fields)) ? " {$field} {$def} " : " {$field} {$def},  ";
        }  
        $sql = "CREATE TABLE IF NOT EXISTS {$tbName} ({$strFields});";
        return self::execBoolStmt($sql);
    }


    public static function createProcedure(string $procName, array $params=null, string $definition) : bool
    {
        $strParam = "";
        $cont=0;
        if(!is_null($params)){
            foreach ($params as $param) {
                $cont++;
                $strParam .= ($cont == count($params)) ? " {$param} " : " {$param}, ";
            }
        }
        $sql = "DELIMITER $$ 
                CREATE PROCEDURE {$procName} ({$strParam})
                    BEGIN 
                        {$definition}
                    END $$
                DELIMITER ;";
        return self::execBoolStmt($sql);
    }


    public static function createFunction(string $funName, array $params=null, string $returns, string $definition) : bool
    {
        $strParam = "";
        $cont=0;
        if(!is_null($params)){
            foreach ($params as $param) {
                $cont++;
                $strParam .= ($cont == count($params)) ? " {$param} " : " {$param}, ";
            }
        }
        $sql = "DELIMITER $$ 
                CREATE FUNCTION {$funName} ({$strParam})
                    RETURNS {$returns}
                    BEGIN 
                        {$definition}
                    END $$
                DELIMITER ;";
        return self::execBoolStmt($sql);
    }
     

    public static function createTrigger(string $trName, string $tbName, string $time, string $event, string $definition) : bool
    {

        $sql = "DELIMITER $$ 
                CREATE TRIGGER {$trName} {$time} {$event} ON {$tbName}
                    FOR EACH ROW 
                    BEGIN 
                        {$definition}
                    END $$
                DELIMITER ;";
        return self::execBoolStmt($sql);
    }

    
    public  function alterDatabase(string $dbName, array $options=null) : bool
    {
        $strOptions = "";
        if($options != null){
           foreach ($options as $key => $value)
              $strOptions .= " {$key} = {$value} ";
        }
        $sql = "ALTER DATABASE {$dbName} CHANGE {$strOptions};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function alterServer(string $serverName, array $options) : bool
    { 
        $strOptions = "";
        $cont = 0;
        foreach ($options as $key => $value){
            $cont++;
            $newValue = (is_int($value)) ? $value : "'{$value}'";
            $strOptions .= ($cont==count($options)) ? "{$key} {$newValue} " : "{$key} {$newValue}, ";
        }
        $sql = "ALTER SERVER {$serverName} OPTIONS ({$strOptions});";
        return self::execBoolStmt($sql);
    }
    
    
    public static function addColumn(string $tbName, string $colName, string $colDef) : bool
    {
        $sql = "ALTER TABLE {$tbName} ADD {$colName} {$colDef};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function addKey(string $tbName, string $keyName, string $keyDef) : bool
    {
        $sql = "ALTER TABLE {$tbName} ADD KEY {$keyName} {$keyDef};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function addConstraint(string $tbName, string $conName, string $conDef) : bool
    {
        $sql = "ALTER TABLE {$tbName} ADD KEY  {$conName} {$conDef};";
        return self::execBoolStmt($sql);
    }


    public static function changeColumn(string $tbName, string $colName, string $newName, string $colDef) : bool
    {
        $sql = "ALTER TABLE {$tbName} CHANGE COLUMN {$colName} {$newName} {$colDef};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function modifyColumn(string $tbName, string $colName, string $colDef) : bool
    {
        $sql = "ALTER TABLE {$tbName} MODIFY COLUMN {$colName} {$colDef};";
        return self::execBoolStmt($sql);
    }
    
    public static function dropDatabase(string $dbName) : bool
    {
        $sql = "DROP DATABASE IF EXISTS {$dbName};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function dropTable(string $tbName) : bool
    {
        $sql = "DROP TABLE IF EXISTS {$tbName};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function dropTrigger(string $trName) : bool
    {
        $sql = "DROP TRIGGER IF EXISTS {$trName};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function dropProcedure(string $procName) : bool
    {
        $sql = "DROP PROCEDURE IF EXISTS {$procName};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function dropFunction(string $funName) : bool
    {
        $sql = "DROP FUNCTION IF EXISTS {$funName};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function dropColumn(string $tbName, string $colName) : bool
    {
        $sql = "ALTER TABLE {$tbName} DROP {$colName};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function dropConstraint(string $tbName, string $conName) : bool
    {
        $sql = "ALTER TABLE {$tbName} DROP {$conName};";
        return self::execBoolStmt($sql);
    }
   
    
    public static function truncateTable(string $tbName) : bool
    {
        $sql = "TRUNCATE {$tbName};";
        return self::execBoolStmt($sql);
    }
    
    
    public static function renameTable(string $tbName, string $newName) : bool
    {
        $sql = "RENAME TABLE {$tbName} TO {$newName};";
        return self::execBoolStmt($sql);
    }
      
    
    public static function setForeignKeyChecks(int $value) : bool
    {
       $sql = "SET FOREIGN_KEY_CHECKS = {$value}; ";
       return self::execBoolStmt($sql);
    }
    
    
    public static function setAutoCommit(int $value) : bool
    {
        $sql = "SET AUTOCOMMIT = {$value}; ";
        return self::execBoolStmt($sql);
    }
    
    
    public static function setTimeZone(string $time) : bool
    {
        $sql = "SET TIMEZONE = '{$time}'; ";
        return self::execBoolStmt($sql);
    }
    
    
    public static function setSqlMode(string $mode) : bool
    {
        $sql = "SET SQL_MODE = '{$mode}'; ";
        return self::execBoolStmt($sql);
    }
     
    
}


