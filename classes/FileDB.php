<?php
namespace classes;

require_once 'HelpStatement.php';

/**
 * @author Ortiz David
 * <br>Email: ortizaad1994@gmail.com <br>Tel: +244936166699
 * @name FileDB
 * @desc Manipula a BD através de um ficheiro .sql
 * @copyright 2020      
 */
class FileDB
{
    
    use HelpStatement;   
    
    
    public static function import(string $file, string $dbName) : bool
    {
        $sql = "USE {$dbName}; SOURCE {$file}; ";
        return self::execBoolStmt($sql);
    }
    
    
    public static function loadDataInFile(string $file, string $tbName) : bool
    {
        $sql = "LOAD DATA INFILE '{$file}' INTO {$tbName}; ";
        return self::execBoolStmt($sql);
    }
    
    
    public static function intoOutFile(string $file, string $tbName) : bool
    {
        $sql = "SELECT IN '{$file}' INTO {$tbName}; ";
        return self::execBoolStmt($sql);
    }

    
    public static function backupTable( string $file, string $tbName) : bool
    {
        $sql = "BACKUP TABLE {$tbName} TO '{$file}'; ";
        return self::execBoolStmt($sql);
    }
    
    
    public static function executeFile(string $file) : bool
    {
        $sql = file_get_contents($file);
        return self::execBoolStmt($sql);
    }
    
    
    public static function printFile(string $file) : void
    {
        echo "<pre>".file_get_contents($file)."</pre>";
    }
    
    
    
}



