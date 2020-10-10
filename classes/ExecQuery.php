<?php
namespace classes;

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
 * @name ExecQuery
 * @desc Classe que permite Executar Querys directamente
 * <br> Para o caso de querermos criar e executar uma query sem usar a classe OperacaoBD    
 */
class ExecQuery
{

    private $host;
    private $dbName;
    private $password;
    private $user;
    private $driver;
    
    
    public function __construct(string $driver=null, string $host=null, string $dbName=null, string $user=null, string $password=null)
    {
        if(is_null($driver) && is_null($host) && is_null($dbName) && is_null($user) && is_null($password))
        {
            $this->driver = Config::DRIVER;
            $this->host = Config::DB_HOST;
            $this->dbName = Config::DB_NAME;
            $this->user = Config::DB_USER;
            $this->password = Config::DB_PASSWORD;
        }
        else 
        {  
            $this->driver = $driver;
            $this->host = $host;
            $this->dbName = $dbName;
            $this->user = $user;
            $this->password = $password;
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name connect
     * @desc Faz a conexão com a base de dados
     * @return PDO
     * */
    private function connect() : PDO
    {
        $dsn = "{$this->driver}:host={$this->host};dbname={$this->dbName};";
        $conexao = new PDO($dsn, $this->user, $this->password);
        return $conexao;
    }

    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execSelectAll
     * @desc Exeuta uma  a query SELECT contida no parametro $query
     * @param string $query
     * @param int $tipoRetorno
     * @return array
     *
     * */
    public function execSelectAll(string $query) : array
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execSelectValues
     * @desc Exeuta uma  a query SELECT e retorna todos os valores do campo selecionado contido no parametro $query
     * @param string $query
     * @param int $tipoRetorno
     * @return array
     *
     * */
    public function execSelectValues(string $query) : array
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $elementos = array();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            foreach ($resultado as $elemento){
                $elementos[] = $elemento->valor;
            }
            return $elementos;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execSelectOne
     * @desc Exeuta uma  a query SELECT e retorna o registo da consulta
     * @param string $query
     * @param int $tipoRetorno
     * @return object
     *
     * */
    public function execSelectOne(string $query) : object
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execSelectField
     * @desc Exeuta uma  a query SELECT e retorna o valor do campo selecionado contido no parametro $query
     * @param string $query
     * @return mixed
     *
     * */
    public function execSelectField(string $query)
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            return $res->resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execExists
     * @desc Exeuta uma  a query SELECT e retorna true se existir algum resultado
     * @param string $query
     * @return bool
     *
     * */
    public function execExists(string $query) : bool
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return ($stmt->rowCount() > 0);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execUpdateDelete
     * @desc Exeuta uma  a query Delete ou Update
     * @param string $query
     * @return bool
     *
     * */
    public function execUpdateDelete(string $query) : bool
    {
        try {
            $pdo = $this->connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($query);
            $retorno = $stmt->execute();
            $pdo->commit();
            return $retorno;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execInsert
     * @desc Exeuta uma  a query Insert
     * @param string $query
     * @return bool
     *
     * */
    public function execInsert(string $query)
    {
        try {
            $pdo = $this->connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($query);
            $retorno = $stmt->execute();
            $pdo->commit();
            return $retorno;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name execSpecial
     * @desc Exeuta as outras Querys
     * @param string $query
     * @return bool
     *
     * */
    public function execSpecial(string $query) : bool
    {
        try {
            $pdo = $this->connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($query);
            $retorno = $stmt->execute();
            $pdo->commit();
            return $retorno;
        }catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
}
