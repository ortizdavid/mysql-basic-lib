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
 *
 * @author Ortiz David
 * <br>Email: ortizaad1994@gmail.com <br>Tel: +244936166699
 * @name TableDB
 * @desc Classe com as Operações da base de dados<br>Operações CRUD, funções de agregação e outras
 * <br> Permite Inserir, Actalizar, Eliminar, Listar, Ordenar e outras manipulações de registos
 * @copyright 2020   
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
            $dados = $obj;
            $strKeys = "";
            $strBinds = "";
            $values = [];
            $binds = [];
            foreach ($dados as $key => $value){
                $strKeys = "{$strKeys}, {$key}";
                $strBinds = "{$strBinds}, :{$key}";
                $binds[] = ":{$key}";
                $values[] = $value;
            }
            $strKeys = substr($strKeys, 1);
            $strBinds = substr($strBinds, 1);
            $dados = [$strKeys, $strBinds, $binds, $values];
            
            $sql = "INSERT INTO {$this->tableName} ({$dados[0]}) VALUES ({$dados[1]});";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            for ($i = 0; $i < count($dados[2]); $i++) {
                $valor = $dados[3][$i];
                $param = $this->getParam($valor);
                $stmt->bindValue("{$dados[2][$i]}", $valor, $param);
            }
            $resultado = $stmt->execute();
            $this->setLastId($pdo->lastInsertId());
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
            $dados = $obj;
            $strKeys = "";
            $values = [];
            $binds = [];
            $cont = 0;
            foreach ($dados as $key => $value){
                $cont++;
                $strKeys .= ($cont == count($dados)) ? "{$key} = :{$key}" : "{$key} = :{$key}, ";
                $binds[] = ":{$key}";
                $values[] = $value;
            }
            $strKeys = substr($strKeys, 0);
            $dados = [$strKeys, $binds, $values];
            
            $sql = "UPDATE {$this->tableName}
                    SET {$dados[0]}
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            for ($i = 0; $i < count($dados[2]); $i++) {
                $valor = $dados[2][$i];
                $param = $this->getParam($valor);
                $stmt->bindValue("{$dados[1][$i]}", $valor, $param);
            }
            $stmt->bindParam(':id', $id);
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
     * @param array $condicoes
     * @return boolean
     * @example: $tb->updateWhere(['nome'=>'Maria José', 'idade'=>29], ['nome'=>'Maria']);
     * */
    public function updateWhere(array $obj, array $condicoes) : bool
    {
        try{
            $dados = $obj;
            $strKeys = "";
            $values = [];
            $binds = [];
            $cont = 0;
            foreach ($dados as $key => $value){
                $cont++;
                $strKeys .= ($cont == count($dados)) ? "{$key} = :{$key}" : "{$key} = :{$key}, ";
                $binds[] = ":{$key}";
                $values[] = $value;
            }
            $strKeys = substr($strKeys, 0);
            $dados = [$strKeys, $binds, $values];
            
            $where = "";
            foreach ($condicoes as $chave => $valor){
                $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                $where .= "{$chave} = {$novoValor} AND ";
            }
            
            $sql = "UPDATE {$this->tableName}
                    SET {$dados[0]}
                    WHERE {$where} 1 ; ";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            for ($i = 0; $i < count($dados[2]); $i++) {
                $valor = $dados[2][$i];
                $param = $this->getParam($valor);
                $stmt->bindValue("{$dados[1][$i]}", $valor, $param);
            }
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
            $resultado = $stmt->execute();
            $this->setLastId($pdo->lastInsertId());
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
             $objecto = $stmt->fetch(PDO::FETCH_OBJ);
             Connection::disconnect();
             return $objecto;
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
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
     * @param mixed $valor
     * @return bool
     *  @example: $tb->deleteOnly('nome', 'Maria');
     * */
    public function deleteOnly(string $campo, $valor) : bool
    {
        try {            
            $sql = "DELETE FROM {$this->tableName}
                    WHERE {$campo} = :valor;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name deleteMany
     * @desc Elimina vários/cada registos da tabela, onde o campo tem os valores passados
     * @param string $campo
     * @param array $valores
     * @return bool
     * @example: $tb->deleteMany('id', [1, 3, 5]);
     *      <br> $tb->deleteMany('nome', ['Maria', 'Paulo']);
     * */
    public function deleteMany(string $campo, array $valores) : bool
    {
        foreach ($valores as $valor){
            $resultado = $this->deleteOnly($campo, $valor, 'perm');
        }  
        return $resultado;
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name deleteWhere
     * @desc Elimina um registo de acordo as condições passadas
     * @param array $condicoes
     * @param string $operador
     * @return bool
     * @example: $tb->deleteWhere(['idade'=>25, 'nome'=>'António'], 'OR');
     *  <br> $tb->deleteWhere(['nome'=>'José']);
     * */
    public function deleteWhere(array $condicoes, string $operador='AND') : bool
    {
        try {
            $where = "";
            $sql = "";
            $aux = ($operador=='AND') ? 1 : 0;
            foreach ($condicoes as $chave => $valor){
                $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                if($operador=='AND' || $operador=='OR')
                   $where .= "{$chave} = {$novoValor} {$operador} ";
                else  {$where .= "Operador '{$operador}' Nao Permitido";  break;}
            }
            $sql .= "DELETE FROM {$this->tableName} 
                        WHERE {$where} {$aux}";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }   
    }
    
    
    /**@author Ortiz David 
     * @copyright 2020
     * @name search
     * @desc Faz uma simples Busca os registos numa tabela, obedecendo as condições dadas e os operadores
     *      <br>Operadores lógicos: <i>AND</i> e <i>OR</i> <br> Operadores relacionais: <i>LIKE</i>, <i>'='</i> e <i><></i>
     * @param array $condicoes
     * @param string $opLogico
     * @param string $opRelacional
     * @return array
     * @example: $tb->search(['sexo'=>'Feminino']);
     *      <br> $tb->search(['idade'=>27, 'sexo'=>'Masculino'], 'AND', '=');
     * */
    public function search(array $condicoes, string $opLogico='OR', string $opRelacional='LIKE') : array
    {
        try {
             $where = "";
             $aux = ($opLogico=='AND') ? 1 : 0;
             foreach ($condicoes as $chave => $valor){
                if($opRelacional == 'LIKE'){
                    $strCond = " LIKE '%{$valor}%'";
                }
                else if($opRelacional == '=' || $opRelacional == '<>'){
                    $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                    $strCond = " {$opRelacional} {$novoValor}" ;
                }
                else{
                    $strCond = "'{$opRelacional}' Operador Invalido" ;
                };
                $where .= "{$chave} {$strCond} {$opLogico} ";
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
    public function exists(array $condicoes) : bool
    {
        try {
            $strCond = "";
            foreach ($condicoes as $chave => $valor){
                $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                $strCond .= " $chave = {$novoValor} AND " ;
            }
            $sql = "SELECT * 
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $resultado = ($stmt->rowCount() > 0);
            Connection::disconnect();
            return $resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name unique
     * @desc Retorna um registo da  atraves de um campo e o seu valor
     * <br> O campo deve ser único
     * @param string $campoUnico
     * @param mixed $valor
     * @return object
     * @example: $tb->unique('email', 'exemplo@gmail.com';
     * */
    public function unique(string $campoUnico, $valor) : object
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$campoUnico} = :valor;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
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
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
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
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
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
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**@author Ortiz David
     * @copyright 2020
     * @name values
     * @desc Retorna um Todos os valores de dum campo
     * @param string $campo
     * @param array $condicoes
     * @return array
     * @example: $tb->values('nome');
     *      <br> $tb->values('idade', ['sexo'=>'Masculino']);
     * */
    public function values(string $campo, array $condicoes=null) : array
    {
        try {
            $strCond = "";
            $valores = [];
            if($condicoes != null){
                foreach ($condicoes as $chave => $valor){
                    $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                    $strCond .= " {$chave} = {$novoValor} AND " ;
                }
            }
            $sql = "SELECT {$campo} AS valor FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            foreach ($resultado as $elemento){
                $valores [] = $elemento->valor;
            }
            Connection::disconnect();
            return $valores;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name between
     * @desc Retorna os registos onde campo está entre inicio e fim
     * @param string $campo
     * @param mixed $inicio
     * @param mixed $fim
     * @return array
     * @example: $tb->between('idade', 20, 43);
     *      <br> $tb->between('data', '2019-01-01', '2020-12-31');
     * */
    public function between(string $campo, $inicio, $fim) : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$campo} BETWEEN :inicio AND :fim;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':inicio', $inicio);
            $stmt->bindParam(':fim', $fim);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**@author Ortiz David
     * @copyright 2020
     * @name notBetween
     * @desc Retorna  os registos onde campo não está entre inicio e fim
     * @param string $campo
     * @param mixed $inicio
     * @param mixed $fim
     * @return array
     * @example: $tb->notBetween('idade', 20, 43);
     *      <br> $tb->notBetween('data', '2019-01-01', '2020-12-31');
     * */
    public function notBetween(string $campo, $inicio, $fim) : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$campo} NOT BETWEEN :inicio AND :fim;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':inicio', $inicio);
            $stmt->bindParam(':fim', $fim);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name in
     * @desc Retorna um Todos os registos onde campo está no Intervalo (IN)
     * @param string $campo
     * @param array $valores
     * @return array
     * @example: $tb->in('idade', [20, 39, 12, 43]);
     *      <br> $tb->in('data', ['2019-01-01', '2019-09-08', '2020-12-31']);
     * */
    public function in(string $campo, array $valores) : array
    {
        try {
            $cont = 0;
            $strVal = "";
            foreach ($valores as $valor){
                $cont++;
                $novoValor = (is_string($valor)) ? " '{$valor}' " : $valor;
                $strVal .= ($cont == count($valores)) ? " {$novoValor} " : " {$novoValor}, ";
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$campo} IN ({$strVal});";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name isNull
     * @desc Retorna um Todos os registos onde o campo está nulo
     * @param string $campo
     * @return array
     * @example: $tb->isNull('altura');
     *      <br> $tb->isNull('data');
     * */
    public function isNull(string $campo) : array
    {
        try {
       
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$campo} IS NULL;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name isNotNull
     * @desc Retorna um Todos os registos onde campo não tem o valor nulo
     * @param string $campo
     * @param array $valores
     * @return array
     * @example: $tb->isNotNull('altura');
     *      <br> $tb->isNotNull('data');
     * */
    public function isNotNull(string $campo) : array
    {
        try {
            
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$campo} IS NOT NULL;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name notIn
     * @desc Retorna um Todos os registos onde campo está no Intervalo (IN)
     * @param string $campo
     * @param array $valores
     * @return array
     *@example: $tb->notIn('idade', [20, 39, 12, 43]);
     *      <br> $tb->notIn('data', ['2019-01-01', '2019-09-08', '2020-12-31']);
     * */
    public function notIn(string $campo, array $valores) : array
    {
        try {
            $cont = 0;
            $strVal = "";
            foreach ($valores as $valor){
                $cont++;
                $novoValor = (is_string($valor)) ? " '{$valor}' " : $valor;
                $strVal .= ($cont == count($valores)) ? " {$novoValor} " : " {$novoValor}, ";
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE {$campo} NOT IN ({$strVal});";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name except
     * @desc Retorna um Todos os registos excepto os que cumprem as condições
     * @param array $condicoes
     * @return array
     * @example: $tb->except(['sexo'=>'Feminino', 'idade'=>30]);
     *      <br> $tb->except(['nome'=>'José']);
     * */
    public function except(array $condicoes) : array
    {
        try {
            $strCond = "";
            foreach ($condicoes as $chave => $valor){
                $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                $strCond .= " {$chave} <> {$novoValor} AND " ;
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name only
     * @desc Retorna apenas os registos  que cumprem as condições
     * @param array $condicoes
     * @return array
     *@example: $tb->only(['data'=>'2020-06-08', 'tipo'=>'Normal']);
     *      <br> $tb->only(['sexo'=>'Masculino']);
     * */
    public function only(array $condicoes) : array
    {
        try {
            $strCond = "";
            foreach ($condicoes as $chave => $valor){
                $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                $strCond .= " {$chave} = {$novoValor} AND " ;
            }
            $sql = "SELECT * FROM {$this->tableName}
                    WHERE ($strCond 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name limit
     * @desc Retorna um Todos os registos no intervalo de início até fim
     * @param int $inicio
     * @param int $fim
     * @return array
     * @example: $tb->limit(1, 10);
     * */
    public function limit(int $inicio, int $fim) : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    LIMIT :inicio, :fim;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
            $stmt->bindParam(':fim', $fim, PDO::PARAM_INT);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
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
     * @param string $ordem
     * @return array
     * @example: $tb->orderBy('nome');
     *  <br>     $tb->only('data', 'DESC');
     * */
    public function orderBy(string $campo, string $ordem='ASC') : array
    {
        try {
            $sql = "SELECT * FROM {$this->tableName}
                    ORDER BY {$campo} {$ordem}";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
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
    public function distinct(string $campo) : array
    {
        try {
            $sql = "SELECT DISTINCT({$campo})
                    FROM {$this->tableName};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
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
     *  @example: $tb->groupBy('sexo');
     * */
    public function groupBy(string $campo) : array
    {
        try {
            $sql = "SELECT *
                    FROM {$this->tableName}
                    GROUP BY {$campo};";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name all
     * @desc Retorna um Todos os registos  seguindo ou não as condições e outros parametros
     *     <br> Também permite usar condições e ordenar os registos
     * @param array $condicoes
     * @return array
     * @example: $tb->all();
     *      <br> $tb->all(['data'=> '2020-10-10', 'sexo'=>'Feminino'], 'nome', 'ASC', 10, 50);
     * */
    public function all(array $condicoes=null, string $campoOrdem=null, string $ordem=null, int $inicio=null, int $fim=null) : array
    {
        try {
            $strCond = "";
            $strOrd = ($campoOrdem==null && $ordem==null) ? " " :" ORDER BY {$campoOrdem} {$ordem} ";
            $strLim = ($inicio==null && $fim==null) ? " " : " LIMIT {$inicio}, {$fim}";
            if($condicoes != null){
                foreach ($condicoes as $chave => $valor){
                    $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                    $strCond .= " {$chave} = {$novoValor} AND " ;
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
            $objecto = $stmt->fetchAll(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto;
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
     * @param string $campo
     * @param mixed $valor
     * @param int id
     * @return bool
     *  @example: $tb->increase('qtd_stock', 2, 43);
     *       <br> $tb->increase('saldo_cliente', 1000, 2)
     * */
    public function increase(string $campo, $valor, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$campo} = {$campo} + :valor, 
                       actualizado_em = NOW()
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
     * @param string $campo
     * @param mixed $valor
     * @param int id
     * @return bool
     *  @example: $tb->decrease('qtd_stock', 2, 43);
     *       <br> $tb->deccrease('saldo_cliente', 1000, 2)
     * */
    public function decrease(string $campo, $valor, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$campo} = {$campo} - :valor,
                       actualizado_em = NOW()
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
     * @param string $campo
     * @param mixed $valor
     * @param int $id
     * @return bool
     * @example: $tb->multiply('quantidade', 2.79, 13)
     * */
    public function multiply(string $campo, $valor, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$campo} = {$campo} * :valor,
                       actualizado_em = NOW()
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
     * @param string $campo
     * @param mixed $valor
     * @return bool
     * @example: $tb->divide('quantidade', 0.5, 2)
     * */
    public function divide(string $campo, $valor, int $id) : bool
    {
        try {
            $sql = "UPDATE {$this->tableName}
                    SET {$campo} = {$campo} / :valor,
                       actualizado_em = NOW()
                    WHERE {$this->primaryKey} = :id;";
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->total;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    

    /**@author Ortiz David
     * @copyright 2020
     * @name countWhere
     * @desc Retorna o numero de registos da tabela onde o campo igual ao valor
     * @param string $campo 
     * @param mixed $valor
     * @return int
     * @example: $tb->countWhere('sexo', 'Masculino');
     * */
    public function countWhere(string $campo, $valor) : int
    {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM {$this->tableName}
                    WHERE {$campo} = :valor;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->total;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    /**@author Ortiz David
     * @copyright 2020
     * @name countExcept
     * @desc Retorna o numero de registos da tabela, Excepto onde o campo igual ao valor
     * @param string $campo 
     * @param mixed $valor
     * @return int
     * @example  $tb->countExcept('data', '1990-11-10')
     * */
    public function countExcept(string $campo, $valor) : int
    {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM {$this->tableName}
                    WHERE {$campo} <> :valor;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->total;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name max
     * @desc Retorna o maior valor de um campo, obedecendo as condições
     * @param string $campo
     * @param array $condicoes
     * @return mixed
     * @example:  $tb->max('salario', ['sexo'=>'Feminino'])
     *       <br> $tb->max('altura')
     * */
    public function max(string $campo, array $condicoes=null) 
    {
        try {
            $strCond = "";
            if($condicoes != null){
                foreach ($condicoes as $chave => $valor){
                    $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                    $strCond .= " {$chave} = {$novoValor} AND " ;
                }
            }
            $sql = "SELECT MAX({$campo}) AS maior
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->maior;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name min
     * @desc Retorna o menor valor de um campo, obedecendo as condições
     * @param string $campo
     * @param array $condicoes
     * @return mixed
     * @example:  $tb->min('salario', ['sexo'=>'Feminino'])
     *       <br> $tb->min('altura')
     * */
    public function min(string $campo, array $condicoes=null)
    {
        try {
            $strCond = "";
            if($condicoes != null){
                foreach ($condicoes as $chave => $valor){
                    $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                    $strCond .= " {$chave} = {$novoValor} AND " ;
                }
            }
            $sql = "SELECT MIN({$campo}) AS menor
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->menor;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name sum
     * @desc Retorna a Soma valor de um campo, obedecendo as condições
     * @param string $campo
     * @param array $condicoes
     * @return float
     * @example:  $tb->sum('salario', ['pais'=>'Angola'])
     *       <br> $tb->sum('salario')
     * */
    public function sum(string $campo, array $condicoes=null) : float
    {
        try {
            $strCond = "";
            if($condicoes != null){
                foreach ($condicoes as $chave => $valor){
                    $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                    $strCond .= " {$chave} = {$novoValor} AND " ;
                }
            }
            $sql = "SELECT SUM({$campo}) AS soma
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->soma;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name avg
     * @desc Retorna a Média valor de um campo, obedecendo as condições
     * @param string $campo
     * @param array $condicoes
     * @return float
     *@example:  $tb->media('altura', ['sexo'=>'Feminino'])
     *       <br> $tb->media('altura')
     * */
    public function avg(string $campo, array $condicoes=null) : float
    {
        try {
            $strCond = "";
            if($condicoes != null){
                foreach ($condicoes as $chave => $valor){
                    $novoValor = (is_string($valor)) ? "'{$valor}'" : $valor;
                    $strCond .= " {$chave} = {$novoValor} AND " ;
                }
            }
            $sql = "SELECT AVG({$campo}) AS media
                    FROM {$this->tableName}
                    WHERE ({$strCond} 1);";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $this->setNumCols($stmt->columnCount());
            $this->setNumRows($stmt->rowCount());
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->media;
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
     * @param mixed $valor
     * @return mixed
     *
     * */
    private function getParam($valor)
    {
        if(is_int($valor))
            $param = PDO::PARAM_INT;
        else if(is_string($valor))
            $param = PDO::PARAM_STR;
        else if(is_bool($valor))
             $param = PDO::PARAM_BOOL;
        else
            $param = null;
        return $param;
    }
    
  

}

