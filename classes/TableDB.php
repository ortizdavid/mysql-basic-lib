<?php
namespace classes;

//use Connection;
use PDO;
use PDOException;

require_once 'Connection.php';
require_once 'CRUD.php';
require_once 'Calculation.php';
require_once 'TimePeriod.php';

/**
 * @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0  
 * @name TableDB
 * @desc Classe com as Operações da base de dados<br>Operações CRUD, funções de agregação e outras
 * <br> Permite Inserir, Actalizar, Eliminar, Listar, Ordenar e outras manipulações de registos
 */
abstract class TableDB implements CRUD, Calculation
{
    
    //A classe usa os métodos do TimePeriod
    use TimePeriod;

    
    /**
     * @var $tableName
     * @desc Nome da tabela da Base de dados
     * */
    protected string $tableName;
    
    /**
     * @var $primaryKey
     * @desc primaryKey da tabela
     * */
    protected string $primaryKey;
    
    /**
     * @var $lastId
     * @desc último Id isnserido 
     * */
    protected int $lastId;

    /**
     * @var $numRows
     * @desc Linhas Afectadas
     * */
    protected int $numRows;

    /**
     * @var $numCols
     * @desc Colunas afectadas 
     * */
    protected int $numCols;
    
    
    /*
    public function __construct(string $tableName=null, string $primaryKey=null)
    {
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
    }*/ 
    
     
    /**@author Ortiz David 
     * @copyright 2020
     * @name insert
     * @desc Faz a Inserção de Dados na tabela
     * @param array $obj
     * @return boolean
     * @example: $tb->insert(['nome'=>'Joao', 'idade'=>23]); 
     *
     * */
    public function insert(array $obj) : bool
    {
        try {
            $data = $obj;
            $strKeys = "";
            $strBinds = "";
            $values = [];
            $binds = [];
            foreach ($data as $key => $value){
                $strKeys = "{$strKeys}, {$key}";
                $strBinds = "{$strBinds}, :{$key}";
                $binds[] = ":{$key}";
                $values[] = $value;
            }
            $strKeys = substr($strKeys, 1);
            $strBinds = substr($strBinds, 1);
            $data = [$strKeys, $strBinds, $binds, $values];
            
            $sql = "INSERT INTO {$this->tableName} ({$data[0]}) VALUES ({$data[1]});";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            for ($i = 0; $i < count($data[2]); $i++) {
                $value = $data[3][$i];
                $param = $this->getParam($value);
                $stmt->bindValue("{$data[2][$i]}", $value, $param);
            }
            $result = $stmt->execute();
            $this->setLastId($pdo->lastInsertId());
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name update
     * @desc Faz a Actualização de Dados na tabela através do Id
     * @param array $obj
     * @param int $id
     * @return boolean
     * @example: $tb->update(['nome'=>'Maria', 'idade'=>26], 1); 
     * */
    public function update(array $obj, int $id) : bool
    {
        try{
            $data = $obj;
            $strKeys = "";
            $values = [];
            $binds = [];
            $cont = 0;
            foreach ($data as $key => $value){
                $cont++;
                $strKeys .= ($cont == count($data)) ? "{$key} = :{$key}" : "{$key} = :{$key}, ";
                $binds[] = ":{$key}";
                $values[] = $value;
            }
            $strKeys = substr($strKeys, 0);
            $data = [$strKeys, $binds, $values];
            
            $sql = "UPDATE {$this->tableName}
                    SET {$data[0]}
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            for ($i = 0; $i < count($data[2]); $i++) {
                $value = $data[2][$i];
                $param = $this->getParam($value);
                $stmt->bindValue("{$data[1][$i]}", $value, $param);
            }
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name updateWhere
     * @desc Faz a Actualização de Dados na tabela através do Id
     * @param array $obj
     * @param array $conditions
     * @return boolean
     * @example: $tb->updateWhere(['nome'=>'Maria José', 'idade'=>29], ['nome'=>'Maria']);
     * */
    public function updateWhere(array $obj, array $conditions) : bool
    {
        try{
            $data = $obj;
            $strKeys = "";
            $values = [];
            $binds = [];
            $cont = 0;
            foreach ($data as $key => $value){
                $cont++;
                $strKeys .= ($cont == count($data)) ? "{$key} = :{$key}" : "{$key} = :{$key}, ";
                $binds[] = ":{$key}";
                $values[] = $value;
            }
            $strKeys = substr($strKeys, 0);
            $data = [$strKeys, $binds, $values];
            
            $where = "";
            foreach ($conditions as $key => $value){
                $newValue = (is_string($value)) ? "'{$value}'" : $value;
                $where .= "{$key} = {$newValue} AND ";
            }
            
            $sql = "UPDATE {$this->tableName}
                    SET {$data[0]}
                    WHERE {$where} 1 ; ";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            for ($i = 0; $i < count($data[2]); $i++) {
                $value = $data[2][$i];
                $param = $this->getParam($value);
                $stmt->bindValue("{$data[1][$i]}", $value, $param);
            }
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    

    /**@author Ortiz David 
     * @copyright 2020
     * @name replace
     * @desc Faz a Substituição de Dados na tabela
     * @param array $values
     * @return boolean
     * @example: $tb->replace(1, 'Maria Jose Pedro', 34);
     * */
    public function replace(array $values) : bool
    {
        try {
            $strValues = "";
            $cont = 0;
            foreach ($values as $value) {
                $cont++;
                $newValue = (is_string($value)) ? " '{$value}' " : $value;
                $strValues .= ($cont == count($values)) ? " {$newValue} " : " {$newValue}, ";
            }
            $sql = "REPLACE INTO {$this->tableName} VALUES ({$strValues});";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute();
            $this->setLastId($pdo->lastInsertId());
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    

    
    /**@author Ortiz David 
     * @copyright 2020
     * @name find
     * @desc Retorna um registo da  atraves do ID passado
     * @param int $id
     * @return object
     * @example: $tb->find(1);
     * */
    public function find(int $id) : object
    {
        try {
             $sql = "SELECT * FROM {$this->tableName} 
                     WHERE {$this->primaryKey} = :id;";
             $pdo = Connection::connect();
             $stmt = $pdo->prepare($sql);
             $stmt->bindParam(':id', $id, PDO::PARAM_INT);
             $stmt->execute();
             $this->setNumCols($stmt->columnCount());
             $this->setNumRows($stmt->rowCount());
             $obj = $stmt->fetch(PDO::FETCH_OBJ);
             Connection::disconnect();
             return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name findAll
     * @desc Retorna todos registos da Tabela 
     * @return array
     * @example: $tb->findAll();
     * */
    public function findAll() : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $arrayObj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $arrayObj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name @delete
     * @desc Elimina um registo da tabela através do Id
     * @param int $id
     * @return bool
     * @example: $tb->delete(6);
     * */
    public function delete(int $id) : bool
    {
        try {
            $sql = "DELETE FROM {$this->tableName} 
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
   
    /**@author Ortiz David 
     * @copyright 2020
     * @name deleteOnly
     * @desc Elimina um registo da tabela apenas onde o nome do campo tiver o valor
     * @param int $id
     * @param mixed $value
     * @return bool
     * @example: $tb->deleteOnly('nome', 'Maria');
     * */
    public function deleteOnly(string $field, $value) : bool
    {
        try {            
            $sql = "DELETE FROM {$this->tableName}
                    WHERE {$field} = :value;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name deleteMany
     * @desc Elimina vários/cada registos da tabela, onde o campo tem os valores passados
     * @param string $field
     * @param array $values
     * @return bool
     * @example: $tb->deleteMany('id', [1, 3, 5]);
     * @example: $tb->deleteMany('nome', ['Maria', 'Paulo']);
     * */
    public function deleteMany(string $field, array $values) : bool
    {
        foreach ($values as $value){
            $result = $this->deleteOnly($field, $value, 'perm');
        }  
        return $result;
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name deleteWhere
     * @desc Elimina um registo de acordo as condições passadas
     * @param array $conditions
     * @param string $operator
     * @return bool
     * @example: $tb->deleteWhere(['idade'=>25, 'nome'=>'António'], 'OR');
     *  <br> $tb->deleteWhere(['nome'=>'José']);
     * */
    public function deleteWhere(array $conditions, string $operator='AND') : bool
    {
        try {
            $where = "";
            $sql = "";
            $aux = ($operator=='AND') ? 1 : 0;
            foreach ($conditions as $key => $value){
                $newValue = (is_string($value)) ? "'{$value}'" : $value;
                if($operator=='AND' || $operator=='OR')
                   $where .= "{$key} = {$newValue} {$operator} ";
                else  {$where .= "Operador '{$operator}' Nao Permitido";  break;}
            }
            $sql .= "DELETE FROM {$this->tableName} 
                        WHERE {$where} {$aux}";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }   
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name search
     * @desc Faz uma simples Busca os registos numa tabela, obedecendo as condições dadas e os operadores
     *      <br>Operadores lógicos: <i>AND</i> e <i>OR</i> <br> Operadores relacionais: <i>LIKE</i>, <i>'='</i> e <i><></i>
     * @param array $conditions
     * @param string $logOp
     * @param string $relOp
     * @return array
     * @example: $tb->search(['sexo'=>'Feminino']);
     * @example: $tb->search(['idade'=>27, 'sexo'=>'Masculino'], 'AND', '=');
     * */
    public function search(array $conditions, string $logOp='OR', string $relOp='LIKE') : array
    {
        try {
             $where = "";
             $aux = ($logOp=='AND') ? 1 : 0;
             foreach ($conditions as $key => $value){
                if($relOp == 'LIKE'){
                    $strCond = " LIKE '%{$value}%'";
                }
                else if($relOp == '=' || $relOp == '<>'){
                    $newValue = (is_string($value)) ? "'{$value}'" : $value;
                    $strCond = " {$relOp} {$newValue}" ;
                }
                else{
                    $strCond = "'{$relOp}' Operador Invalido" ;
                }
                $where .= "{$key} {$strCond} {$logOp} ";
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE ({$where} {$aux});";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $arrayObj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $arrayObj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
     
    
    /**@author Ortiz David
     * @copyright 2020
     * @name exists
     * @desc Verifica se um registo existe através das condições passadas
     * @param array $codicoes
     * @return bool
     * @example: $tb->exists(['nome'=>'Maria José']);
     * */
    public function exists(array $conditions) : bool
    {
        try {
            $strCond = "";
            foreach ($conditions as $key => $value){
                $newValue = (is_string($value)) ? "'{$value}'" : $value;
                $strCond .= " $key = {$newValue} AND " ;
            }
            $sql = "SELECT * 
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $result = ($stmt->rowCount() > 0);
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name unique
     * @desc Retorna um registo da  atraves de um campo e o seu valor
     * <br> O campo deve ser único
     * @param string $field
     * @param mixed $value
     * @return object
     * @example: $tb->unique('email', 'exemplo@gmail.com';
     * */
    public function unique(string $field, $value) : object
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$field} = :value;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name first
     * @desc Retorna o primeiro registo da tabela
     * @return object
     * @example: $tb->first();
     * */
    public function first() : object
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    ORDER BY {$this->primaryKey} ASC
                    LIMIT 1;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name last
     * @desc Retorna o último registo da tabela
     * @return object
     * @example: $tb->last();
     * */
    public function last() : object
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    ORDER BY {$this->primaryKey} DESC
                    LIMIT 1;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**@author Ortiz David
     * @copyright 2020
     * @name middle
     * @desc Retorna o registo localizado no meio data tabela
     * @return object
     * @example: $tb->middle();
     * */
    public function middle() : object
    {
        try {
            $midPos = (int) ($this->count() / 2);
            $sql = "SELECT * FROM {$this->tableName}
                    ORDER BY {$this->primaryKey} 
                    LIMIT 1 OFFSET {$midPos};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**@author Ortiz David
     * @copyright 2020
     * @name values
     * @desc Retorna um Todos os valores de dum campo
     * @param string $field
     * @param array $conditions
     * @return array
     * @example: $tb->values('nome');
     * @example: $tb->values('idade', ['sexo'=>'Masculino']);
     * */
    public function values(string $field, array $conditions=null) : array
    {
        try {
            $strCond = "";
            $values = [];
            if($conditions != null){
                foreach ($conditions as $key => $value){
                    $newValue = (is_string($value)) ? "'{$value}'" : $value;
                    $strCond .= " {$key} = {$newValue} AND " ;
                }
            }
            $sql = "SELECT {$field} AS val FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            foreach ($result as $element){
                $values [] = $element->val;
            }
            Connection::disconnect();
            return $values;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name between
     * @desc Retorna os registos onde campo está entre inicio e fim
     * @param string $field
     * @param mixed $start
     * @param mixed $end
     * @return array
     * @example: $tb->between('idade', 20, 43);
     * @example: $tb->between('data', '2019-01-01', '2020-12-31');
     * */
    public function between(string $field, $start, $end) : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$field} BETWEEN :start AND :end;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**@author Ortiz David
     * @copyright 2020
     * @name notBetween
     * @desc Retorna  os registos onde campo não está entre inicio e fim
     * @param string $field
     * @param mixed $start
     * @param mixed $end
     * @return array
     * @example: $tb->notBetween('idade', 20, 43);
     * @example: $tb->notBetween('data', '2019-01-01', '2020-12-31');
     * */
    public function notBetween(string $field, $start, $end) : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$field} NOT BETWEEN :start AND :end;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name in
     * @desc Retorna um Todos os registos onde campo está no Intervalo (IN)
     * @param string $field
     * @param array $values
     * @return array
     * @example: $tb->in('idade', [20, 39, 12, 43]);
     * @example: $tb->in('data', ['2019-01-01', '2019-09-08', '2020-12-31']);
     * */
    public function in(string $field, array $values) : array
    {
        try {
            $cont = 0;
            $strVal = "";
            foreach ($values as $value){
                $cont++;
                $newValue = (is_string($value)) ? " '{$value}' " : $value;
                $strVal .= ($cont == count($values)) ? " {$newValue} " : " {$newValue}, ";
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$field} IN ({$strVal});";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name isNull
     * @desc Retorna um Todos os registos onde o campo está nulo
     * @param string $field
     * @return array
     * @example: $tb->isNull('altura');
     * @example: $tb->isNull('data');
     * */
    public function isNull(string $field) : array
    {
        try {
       
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$field} IS NULL;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name isNotNull
     * @desc Retorna um Todos os registos onde campo não tem o valor nulo
     * @param string $field
     * @param array $values
     * @return array
     * @example: $tb->isNotNull('altura');
     * @example: $tb->isNotNull('data');
     * */
    public function isNotNull(string $field) : array
    {
        try {
            
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$field} IS NOT NULL;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name notIn
     * @desc Retorna um Todos os registos onde campo está no Intervalo (IN)
     * @param string $field
     * @param array $values
     * @return array
     *@example: $tb->notIn('idade', [20, 39, 12, 43]);
     * @example: $tb->notIn('data', ['2019-01-01', '2019-09-08', '2020-12-31']);
     * */
    public function notIn(string $field, array $values) : array
    {
        try {
            $cont = 0;
            $strVal = "";
            foreach ($values as $value){
                $cont++;
                $newValue = (is_string($value)) ? " '{$value}' " : $value;
                $strVal .= ($cont == count($values)) ? " {$newValue} " : " {$newValue}, ";
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$field} NOT IN ({$strVal});";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name except
     * @desc Retorna um Todos os registos excepto os que cumprem as condições
     * @param array $conditions
     * @return array
     * @example: $tb->except(['sexo'=>'Feminino', 'idade'=>30]);
     * @example: $tb->except(['nome'=>'José']);
     * */
    public function except(array $conditions) : array
    {
        try {
            $strCond = "";
            foreach ($conditions as $key => $value){
                $newValue = (is_string($value)) ? "'{$value}'" : $value;
                $strCond .= " {$key} <> {$newValue} AND " ;
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name only
     * @desc Retorna apenas os registos  que cumprem as condições
     * @param array $conditions
     * @return array
     *@example: $tb->only(['data'=>'2020-06-08', 'tipo'=>'Normal']);
     *@example: $tb->only(['sexo'=>'Masculino']);
     * */
    public function only(array $conditions) : array
    {
        try {
            $strCond = "";
            foreach ($conditions as $key => $value){
                $newValue = (is_string($value)) ? "'{$value}'" : $value;
                $strCond .= " {$key} = {$newValue} AND " ;
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE ($strCond 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name limit
     * @desc Retorna um Todos os registos no intervalo de início até fim
     * @param int $start
     * @param int $end
     * @return array
     * @example: $tb->limit(1, 10);
     * */
    public function limit(int $start, int $end) : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    LIMIT :start, :end;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':start', $start, PDO::PARAM_INT);
            $stmt->bindParam(':end', $end, PDO::PARAM_INT);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name orderBy
     * @desc Retorna um Todos os registos ordenado pelo campo
     * <br> Por defeito o métdo já faz por Ordem Crescente
     * @param string campo
     * @param string $order
     * @return array
     * @example: $tb->orderBy('nome');
     *  <br>     $tb->only('data', 'DESC');
     * */
    public function orderBy(string $field, string $order='ASC') : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    ORDER BY {$field} {$order}";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name distinct
     * @desc Retorna um Todos os campos distintos
     * <br> Retorna os registos, evitando a duplicação
     * @param string campo
     * @return array
     * @example: $tb->distinct('data');
     * */
    public function distinct(string $field) : array
    {
        try {
            $sql = "SELECT DISTINCT({$field})
                    FROM {$this->tableName};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name groupBy
     * @desc Retorna um Todos os registos agrupados pelo campo
     * @param string campo
     * @return array
     * @example: $tb->groupBy('sexo');
     * */
    public function groupBy(string $field) : array
    {
        try {
            $sql = "SELECT *
                    FROM {$this->tableName}
                    GROUP BY {$field};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name all
     * @desc Retorna um Todos os registos  seguindo ou não as condições e outros parametros
     *     <br> Também permite usar condições e ordenar os registos
     * @param array $conditions
     * @return array
     * @example: $tb->all();
     * @example: $tb->all(['data'=> '2020-10-10', 'sexo'=>'Feminino'], 'nome', 'ASC', 10, 50);
     * */
    public function all(array $conditions=null, string $fieldOrdem=null, string $order=null, int $start=null, int $end=null) : array
    {
        try {
            $strCond = "";
            $strOrd = ($fieldOrdem==null && $order==null) ? " " :" ORDER BY {$fieldOrdem} {$order} ";
            $strLim = ($start==null && $end==null) ? " " : " LIMIT {$start}, {$end}";
            if($conditions != null){
                foreach ($conditions as $key => $value){
                    $newValue = (is_string($value)) ? "'{$value}'" : $value;
                    $strCond .= " {$key} = {$newValue} AND " ;
                }
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE ({$strCond} 1)
                    {$strOrd} {$strLim};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name increase
     * @desc Incrementa ou soma  um valor ao campo
     * <br> Pode ser usado para aumentar o saldo de um cliente,
     * <br> ou Aumentar a quantidade em estoque do produto
     * @param string $field
     * @param mixed $value
     * @param int id
     * @return bool
     * @example: $tb->increase('qtd_stock', 2, 43);
     * @example: $tb->increase('saldo_cliente', 1000, 2)
     * */
    public function increase(string $field, $value, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$field} = {$field} + :value
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name increase
     * @desc Decrementa ou subtrai um valor ao campo
     * <br> Pode ser usado para diminuir o saldo de um cliente,
     * <br> ou Dimunir a quantidade em estoque do produto
     * @param string $field
     * @param mixed $value
     * @param int id
     * @return bool
     * @example: $tb->decrease('qtd_stock', 2, 43);
     * @example: $tb->deccrease('saldo_cliente', 1000, 2)
     * */
    public function decrease(string $field, $value, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$field} = {$field} - :value
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name multiply
     * @desc Multiplica um valor ao campo
     * <br> Pode ser usado para multiplicar o valor de um campo, por um número
     * @param string $field
     * @param mixed $value
     * @param int $id
     * @return bool
     * @example: $tb->multiply('quantidade', 2.79, 13)
     * */
    public function multiply(string $field, $value, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$field} = {$field} * :value
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name divide
     * @desc Divide um valor ao campo
     *  <br> Pode ser usado para dividir o valor de um campo, por um número
     * @param string $field
     * @param mixed $value
     * @return bool
     * @example: $tb->divide('quantidade', 0.5, 2)
     * */
    public function divide(string $field, $value, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$field} = {$field} / :value
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name count
     * @desc Retorna o número de registos da tabela
     * @return int
     * @example: $tb->count() 
     * */
    public function count() : int
    {
        try {
            $sql = "SELECT COUNT(*) AS total 
                    FROM {$this->tableName};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->total;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    

    /**@author Ortiz David
     * @copyright 2020
     * @name countWhere
     * @desc Retorna o numero de registos da tabela onde o campo igual ao valor
     * @param string $field 
     * @param mixed $value
     * @return int
     * @example: $tb->countWhere('sexo', 'Masculino');
     * */
    public function countWhere(string $field, $value) : int
    {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM {$this->tableName}
                    WHERE {$field} = :value;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->total;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    /**@author Ortiz David
     * @copyright 2020
     * @name countExcept
     * @desc Retorna o numero de registos da tabela, Excepto onde o campo igual ao valor
     * @param string $field 
     * @param mixed $value
     * @return int
     * @example  $tb->countExcept('data', '1990-11-10')
     * */
    public function countExcept(string $field, $value) : int
    {
        try {
            $sql = "SELECT COUNT(*) AS result
                    FROM {$this->tableName}
                    WHERE {$field} <> :value;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name max
     * @desc Retorna o maior valor de um campo, obedecendo as condições
     * @param string $field
     * @param array $conditions
     * @return mixed
     * @example:  $tb->max('salario', ['sexo'=>'Feminino'])
     * @example: $tb->max('altura')
     * */
    public function max(string $field, array $conditions=null) 
    {
        try {
            $strCond = "";
            if($conditions != null){
                foreach ($conditions as $key => $value){
                    $newValue = (is_string($value)) ? "'{$value}'" : $value;
                    $strCond .= " {$key} = {$newValue} AND " ;
                }
            }
            $sql = "SELECT MAX({$field}) AS result
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name min
     * @desc Retorna o menor valor de um campo, obedecendo as condições
     * @param string $field
     * @param array $conditions
     * @return mixed
     * @example:  $tb->min('salario', ['sexo'=>'Feminino'])
     * @example: $tb->min('altura')
     * */
    public function min(string $field, array $conditions=null)
    {
        try {
            $strCond = "";
            if($conditions != null){
                foreach ($conditions as $key => $value){
                    $newValue = (is_string($value)) ? "'{$value}'" : $value;
                    $strCond .= " {$key} = {$newValue} AND " ;
                }
            }
            $sql = "SELECT MIN({$field}) AS result
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name sum
     * @desc Retorna a Soma valor de um campo, obedecendo as condições
     * @param string $field
     * @param array $conditions
     * @return float
     * @example:  $tb->sum('salario', ['pais'=>'Angola'])
     * @example: $tb->sum('salario')
     * */
    public function sum(string $field, array $conditions=null) : float
    {
        try {
            $strCond = "";
            if($conditions != null){
                foreach ($conditions as $key => $value){
                    $newValue = (is_string($value)) ? "'{$value}'" : $value;
                    $strCond .= " {$key} = {$newValue} AND " ;
                }
            }
            $sql = "SELECT SUM({$field}) AS result
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name avg
     * @desc Retorna a Média valor de um campo, obedecendo as condições
     * @param string $field
     * @param array $conditions
     * @return float
     *@example:  $tb->media('altura', ['sexo'=>'Feminino'])
     * @example: $tb->media('altura')
     * */
    public function avg(string $field, array $conditions=null) : float
    {
        try {
            $strCond = "";
            if($conditions != null){
                foreach ($conditions as $key => $value){
                    $newValue = (is_string($value)) ? "'{$value}'" : $value;
                    $strCond .= " {$key} = {$newValue} AND " ;
                }
            }
            $sql = "SELECT AVG({$field}) AS result
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return string 
     */
    public function getTableName() : string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getPrimaryKey() : string
    {
        return $this->primaryKey;
    }

    /**
     * @return int
     */
    public function getLastId() : int
    {
        return $this->lastId;
    }

    /**
     * @return int
     */
    public function getNumRows() : int
    {
        return $this->numRows;
    }

    /**
     * @return int
     */
    public function getNumCols() : int
    {
        return $this->numCols;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName) : void
    {
        $this->tableName = $tableName;
    }

    /**
     * @param string $primaryKey
     */
    public function setPrimaryKey(string $primaryKey) : void
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @param int $lastId
     */
    public function setLastId(int $lastId) : void
    {
        $this->lastId = $lastId;
    }

    /**
     * @param int $numRows
     */
    public function setNumRows(int $numRows) : void
    {
        $this->numRows = $numRows;
    }

    /**
     * @param int $numRows
     */
    public function setNumCols(int $numCols) : void
    {
        $this->numCols = $numCols;
    }

    /**@author Ortiz David
     * @copyright 2020
     * @name getParam
     * @desc Retorna uma constante de filtragem da classe PDO
     * @param mixed $value
     * @return mixed
     *
     * */
    private function getParam($value)
    {
        if(is_int($value))
            $param = PDO::PARAM_INT;
        else if(is_string($value))
            $param = PDO::PARAM_STR;
        else if(is_bool($value))
             $param = PDO::PARAM_BOOL;
        else
            $param = null;
        return $param;
    }
    
  

}

