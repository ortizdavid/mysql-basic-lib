<?php
namespace classes;

require_once 'HelpStatement.php';

/**
 * @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @name FileDB
 * @desc Manipula a BD através de um ficheiro .sql    
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



