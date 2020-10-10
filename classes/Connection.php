<?php

namespace classes;

use classes\Config;
use PDO;
use PDOException;

require_once 'Config.php';

/**
 * @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @desc Classe para a Conexão com a BD
 * @name Connection     
 */
class Connection
{
    
    /**
     * @staticvar PDO $pdo
     * @desc Objecto para a manipulação da BD
     * */
    private static $pdo;
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Abre a Conexão com a Base de Dados
     * @name connect
     * @return PDO
     * */
    public static function connect() : PDO
    {
        try {
            $dsn = Config::DRIVER.':host='.Config::DB_HOST
                   .';dbname='.Config::DB_NAME.';port='.Config::DB_PORT
                   .';charset='.Config::DB_CHARSET;
            self::$pdo = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$pdo;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Fecha a Conexão com a Base de Dados
     * @name disconnect
     * @return void
     * */
    public static function disconnect() : void
    {
        self::$pdo = NULL;
    }
    
    
}


