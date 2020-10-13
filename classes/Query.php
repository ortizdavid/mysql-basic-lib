<?php
namespace classes;

//use Connection;
use PDO;
use PDOException;

require_once 'Connection.php';

/**
* @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @name Query
 * @desc Classe que permite Criar querys e retornar a query
 * <br> Serve de ajuda para as classes que executam query directamente  
 */
class Query
{

    private $queryResult;
    private $lastId;
    private $numRows;
    private $numCols;
    private $_select;
    private $_delete;
    private $_insert;
    private $_update;
    private $_from;
    private $_where;
    private $_in;
    private $_notIn;
    private $_having;
    private $_limit;
    private $_call;
    private $_order;
    private $_group;
    private $_nulls;
    private $_notNull;
    
    
    /**
     */
    public function __construct()
    {
        $this->queryResult = "";
        $this->_select = " SELECT ";
        $this->_update = " UPDATE ";
        $this->_insert = " INSERT ";
        $this->_nulls = "";
        $this->_notNull = "";
        $this->_from = array();
        $this->_delete = array();
        $this->_where = array();
        $this->_in = array();
        $this->_notIn = array();
        $this->_having = array();
        $this->_limit = array();
        $this->_call = array();
        $this->_order = array();
        $this->_group = array();
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Cria o comando select, com os campos desejados, separados por vírgulas
     * @name select
     * @param string $fields
     * @return Query
     * **/
    public function select(string $fields=null) : Query
    {
        $strSelect = "";
        if($fields == null)
            $strSelect = " * ";
        else 
            $strSelect .= $fields;
        $this->_select .= " {$strSelect} ";  
        $this->queryResult .= $this->_select;
        return $this;
    }
    
    
    /**
    * @author Ortiz David
    * @copyright 2020
    * @desc Cria o delete na tabela e as condições
    * @name delete
    * @param string $table
    * @return Query
    * **/
    public function delete(string $table) : Query
    {
        if(count($this->_delete) > 0)
            $this->_delete[] = " ";
        else{
            $this->_delete[] = " DELETE FROM {$table} ";
            $this->queryResult .= $this->_delete[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Cria a query insert na tabela e as condições
     * @name insert
     * @param string $table
     * @param array $fields
     * @return Query
     * **/
    public function insert(string $table, array $fields) : Query
    {
        $this->_insert .= " INTO {$table} ( ";
        $cont = 0;
        $cont2 = 0;
        $strInsert = " ";
        $strValues = " ";
        foreach ($fields as $field => $value){
            $cont++;
            $strInsert .= ($cont == count($fields)) ? " {$field} " : " {$field}, ";
        }
        $this->_insert .= " {$strInsert} ) ";
        $this->_insert .= " VALUES ( ";
        foreach ($fields as $field => $value){
            $cont2++;
            $value = $this->filter($value);
            $novoVal = (is_string($value)) ? " '{$value}' " : $value;
            $strValues .= ($cont2 == count($fields)) ? " {$novoVal} " : " {$novoVal}, ";
        }
        $this->_insert .= " {$strValues} ); ";
        $this->queryResult .= $this->_insert;
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Cria a query update na tabela e as condições
     * @name update
     * @param string $table
     * @param array $fields
     * @param array $conditions
     * @return Query
     * **/
    public function update(string $table, array $fields, array $conditions) : Query
    {
        $this->_update .= " {$table} SET ";
        $cont = 0;
        $cont2 = 0;
        $strSet = " ";
        $strCond = " ";
        foreach ($fields as $field => $value){
            $cont++;
            $value = $this->filter($value);
            $newValue = (is_string($value)) ? " '{$value}' " : $value;
            $strSet .= ($cont == count($fields)) ? " {$field} = {$newValue} " : " {$field} = {$newValue}, ";
        }
        foreach ($conditions as $chave => $val){
            $cont2++;
            $val = $this->filter($val);
            $novoVal = (is_string($val)) ? " '{$val}' " : $val;
            $strCond .= ($cont2 == count($conditions)) ? " {$chave} = {$novoVal} " : " {$chave} = {$novoVal} AND ";
        }
        $this->_update .= " {$strSet} WHERE {$strCond} ";
        $this->queryResult .= $this->_update;
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando FROM e a tabela
     * @name from
     * @param string $table
     * @return Query
     * **/
    public function from(string $table) : Query
    {
       if(count($this->_from) > 0)
            $this->_from[] = " ";
        else {
            $this->_from[] = " FROM {$table} ";
            $this->queryResult .= $this->_from[0];
        }
       return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando JOIN ON e a tabela 
     * @name join
     * @param string $table
     * @param string $on
     * @return Query
     * **/
    public function join(string $table, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " JOIN {$table} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando LEFT JOIN ON e a tabela
     * @name leftJoin
     * @param string $table
     * @param string $on
     * @return Query
     * **/
    public function leftJoin(string $table, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " LEFT JOIN {$table} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando RIGHT JOIN ON e a tabela
     * @name rightJoin
     * @param string $table
     * @param string $on
     * @return Query
     * **/
    public function rightJoin(string $table, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " RIGHT JOIN {$table} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando FULL JOIN ON e a tabela
     * @name fullJoin
     * @param string $table
     * @param string $on
     * @return Query
     * **/
    public function fullJoin(string $table, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " FULL JOIN {$table} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando FULL CROSS ON e a tabela
     * @name crossJoin
     * @param string $table
     * @param string $on
     * @return Query
     * **/
    public function crossJoin(string $table, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " CROSS JOIN {$table} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando NATURAL CROSS ON e a tabela
     * @name naturalJoin
     * @param string $table
     * @param string $on
     * @return Query
     * **/
    public function naturalJoin(string $table, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " NATURAL JOIN {$table} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando WHERE e a condição
     * @name where
     * @param string $table
     * @param string $operator
     * @param mixed $value
     * @return Query
     * **/
    public function where(string $field, string $operator, $value) : Query
    {
        if(count($this->_where) > 0)
            $this->_where[] = " ";  
        else {
            $condicao = $this->getCondition($field, $operator, $value);
            $this->_where[] = " WHERE {$condicao} "; 
            $this->queryResult .= $this->_where[0];
        }
        return $this;
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando WHERE e o comando IN
     * @name whereIn
     * @param string $table
     * @param array $values
     * @return Query
     * **/
    public function whereIn(string $field, array $values) : Query
    {
        if(count($this->_in) > 0)
            $this->_in[] = " ";
        else {
            $cont = 0;
            $strVal = " ";
            foreach ($values as $value){
                $cont++;
                $value = $this->filter($value);
                $newValue = (is_string($value)) ? " '{$value}' " : $value;
                $strVal .= ($cont == count($values)) ? " {$newValue} " : " {$newValue}, ";
            }
            $this->_in[] = " WHERE {$field} IN ({$strVal}) ";
            $this->queryResult .= $this->_in[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando WHERE e o comando NOT IN
     * @name whereNoiIn
     * @param string $table
     * @param array $values
     * @return Query
     * **/
    public function whereNotIn(string $field, array $values) : Query
    {
        if(count($this->_notIn) > 0)
            $this->_notIn[] = " ";
            else {
                $cont = 0;
                $strVal = " ";
            foreach ($values as $value){
                $cont++;
                $value = $this->filter($value);
                $newValue = (is_string($value)) ? " '{$value}' " : $value;
                $strVal .= ($cont == count($values)) ? " {$newValue} " : " {$newValue}, ";
            }
            $this->_notIn[] = " WHERE {$field} NOT IN ({$strVal}) ";
            $this->queryResult .= $this->_notIn[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando IS NULL
     * @name _null
     * @param string $table
     * @param array $values
     * @return Query
     * **/
    public function isNull(string $operator='AND', string $field) : Query
    {
        $strOp = (in_array($operator, ['AND', 'OR'])) ? $operator : ' ___ERROR___ ';
        $this->_notNull .= " {$strOp} {$field} IS NULL ";
        $this->queryResult .= $this->_notNull;
        return $this;
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando IS NOT NULL
     * @name notNull
     * @param string $table
     * @param array $values
     * @return Query
     * **/
    public function notNull(string $operator='AND', string $field) : Query
    {
        $strOp = (in_array($operator, ['AND', 'OR'])) ? $operator : ' ___ERROR___ ';
        $this->_nulls .= " {$strOp} {$field} IS NOT NULL ";
        $this->queryResult .= $this->_nulls;
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando BETWEEN
     * @name between
     * @param string $operator
     * @param string $field
     * @param string $start
     * @param string $end
     * @return Query
     * **/
    public function between(string $operator='AND', string $field,  $start, $end) : Query
    {
        $strOp = (in_array($operator, ['AND', 'OR'])) ? $operator : ' ___ERROR___ ';
        $this->_nulls .= " {$strOp} {$field} BETWEEN {$start} AND {$end} ";
        $this->queryResult .= $this->_nulls;
        return $this;
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando NOT BETWEEN
     * @name notBetween
     * @param string $operator
     * @param string $field
     * @param string $start
     * @param string $end
     * @return Query
     * **/
    public function notBetween(string $operator='AND', string $field,  $start, $end) : Query
    {
        $strOp = (in_array($operator, ['AND', 'OR'])) ? $operator : ' ___ERROR___ ';
        $this->_nulls .= " {$strOp} {$field} NOT BETWEEN {$start} AND {$end} ";
        $this->queryResult .= $this->_nulls;
        return $this;
    }
    
   
      
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando HAVING e a condição
     * @name having
     * @param string $table
     * @param string $operator
     * @param mixed $value
     * @return Query
     * **/
    public function having(string $field, string $operator, $value) : Query
    {
        if(count($this->_having) > 0)
            $this->_having[] = " ";
        else {
            $condicao = $this->getCondition($field, $operator, $value);
            $this->_having[] = " HAVING {$condicao} ";
            $this->queryResult .= $this->_having[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando AND e a condição
     * @name and
     * @param string $table
     * @param string $operator
     * @param mixed $value
     * @return Query
     * **/
    public function and(string $field, string $operator, $value) : Query
    {
        $condicao = $this->getCondition($field, $operator, $value);
        $this->queryResult .= " AND {$condicao} ";
        return $this;
    }
   
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando OR e a condição
     * @name or
     * @param string $table
     * @param string $operator
     * @param mixed $value
     * @return Query
     * **/
    public function or(string $field, string $operator, $value) : Query
    {
        $condicao = $this->getCondition($field, $operator, $value);
        $this->queryResult .= " OR {$condicao} ";
        return $this;
    }
    

    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando NOT e a condição
     * @name not
     * @param string $table
     * @param string $operator
     * @param mixed $value
     * @return Query
     * **/
    public function not(string $field, string $operator, $value) : Query
    {
        $condicao = $this->getCondition($field, $operator, $value);
        $this->queryResult .= " NOT ({$condicao}) ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando ORDER BY a ordem ASC ou DESC
     * @name orderBy
     * @param string $field
     * @param string $ordem
     * @return Query
     * **/
    public function orderBy(string $field, string $ordem) : Query
    {
        if(count($this->_order) > 0)
            $this->_order[] = " ";
        else{
            $this->_order[] = " ORDER BY {$field} {$ordem} ";
            $this->queryResult .= $this->_order[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando GROUP BY e o campo
     * @name groupBy
     * @param string $field
     * @return Query
     * **/
    public function groupBy(string $field) : Query
    {
        if(count($this->_group) > 0)
            $this->_group[] = " ";
        else{
            $this->_group[] = " GROUP BY {$field} ";
            $this->queryResult .= $this->_order[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando LIMIT inicio, fim
     * @name limit
     * @param int $start
     * @param int  $end
     * @return Query
     * **/
    public function limit(int $start, int $end) : Query
    {
        if(count($this->_limit) > 0)
            $this->_limit[] = " ";
        else{
            $this->_limit[] = " LIMIT {$start}, {$end}  ";
            $this->queryResult .= $this->_limit[0];
        }
        return $this;
    }
    
  
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando CALL e o nome do procedimento
     * @name call
     * @param string $procName
     * @return Query
     * **/
    public function call(string $procName) : Query
    {
        if(count($this->_call) > 0)
            $this->_call[] = " ";
        else{
            $this->_call[] = " CALL {$procName} ";
            $this->queryResult .= $this->_call[0];
        }
        return $this;
    }
    
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query com uma outra query
     * @name statement
     * @param string $stmt
     * @return Query
     * **/
    public function statement(string $stmt) : Query
    {
        $this->queryResult .= $stmt;
        return  $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Retorna a query final
     * @name getQuery
     * @return string
     * **/
    public function getQuery() : string
    {
        return $this->queryResult;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Retorna o id Inserido
     * @name getLastId
     * @return int
     * **/
    public function getLastId() : int
    {
        return $this->lastId;
    }


    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Retorna o número de linhas afectadas
     * @name getNumRows
     * @return int
     * **/
    public function getNumRows() : int
    {
        return $this->numRows;
    }


    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Retorna o número de colunas afectadas
     * @name getNumCols
     * @return int
     * **/
    public function getNumCols() : int
    {
        return $this->numCols;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Retorna uma string com a condição e os operadores
     * @name getCondition
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return string
     * **/
    private function getCondition(string $field, string $operator, $value) 
    {
        $value = $this->filter($value);
        //$strCond = null;
        switch($operator){
            case 'LIKE': $strCond = " {$field} LIKE '%{$value}%' "; break;
            case 'IN': $strCond = " {$field} IN ({$value}) ";   break;
            case '=':
            case '<>':
            case '!=':
            case '>':
            case '<':
            case '>=':
            case '<=':
                $newValue = (is_int($value)) ? $value : "'{$value}'";
                $strCond = " {$field} {$operator} {$newValue} ";
                break;
            default: $strCond = " __ERROR__"; break;
        }
        return $strCond;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Filtra o valor de uma variável
     * @name filter
     * @param mixed $value
     * @return mixed
     * **/
    private function filter($value)
    {
        if(is_int($value)) 
            $cleanValue = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        else if(is_float($value)|| is_double($value)) 
            $cleanValue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
        else if(is_string($value))
            $cleanValue = filter_var($value, FILTER_SANITIZE_STRING);
        else 
            $cleanValue = filter_var($value);
        return $cleanValue;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa a query final e retorna o resultado
     * <br> Pode Executar os comandos Insert, Update, Delete e outros
     * <br> Os Tipos de Operações são: delete, update, create, one, all, value, values e exists
     * @name execute
     * @param string $operation
     * @return mixed
     * **/
    public function execute(string $operation='')
    {
        try {
            $sql = $this->getQuery();
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $operation = strtolower($operation);
            
            switch ($operation)
            {
                case 'insert':
                case 'create':
                case 'new':
                case 'add':
                    $result = $stmt->execute();
                    $this->lastId = $pdo->lastInsertId();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    break;
                case 'update':
                case 'edit':
                case 'delete' :
                case 'remove':
                    $result = $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    break;
                case 'one':
                case 'find':
                case 'object':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $result = $stmt->fetch(PDO::FETCH_OBJ);
                    break;
                case 'all':
                case 'findAll':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                    break;
                case 'value':
                case 'one_value':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                   $obj = $stmt->fetch(PDO::FETCH_OBJ);
                    $result =$obj->value;
                    break;
                case 'values':
                case 'all_values':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                    foreach ($result as $element){
                        $values [] = $element->value;
                    }
                    $result = $values;
                    break;
                case 'exists':
                case 'contains':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $result = ($stmt->rowCount() > 0);
                    break;
                default:
                    return $stmt->execute();
                    break;
            }
            $pdo->commit();
            Connection::disconnect();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $pdo->rollBack();
        }  
    }
  
    
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa  um insert
     * @name add
     * @return bool
     * **/
    public function add() : bool
    {
        return $this->execute('create');
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa  um update
     * @name edit
     * @return bool
     * **/
    public function edit() : bool
    {
        return $this->execute('update');
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa  um delete
     * @name remove
     * @return bool
     * **/
    public function remove() : bool
    {
        return $this->execute('delete');
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa SELECT ALL de todos os Campos e retorna um array de todos os registos
     * @name all
     * @return array
     * **/
    public function all() : array
    {
        return $this->execute('all');
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa SELECT one e retorna o objecto
     * @name one
     * @return object
     * **/
    public function one() : object
    {
        return $this->execute('one');
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa o exists e retorna o true caso existe
     * @name exists
     * @return bool
     * **/
    public function exists() : bool 
    {
        return $this->execute('exists');
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa o valuee retorna o valor desejado
     * @name value
     * @return mixed
     * **/
    public function value() 
    {
        return $this->execute('value');
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa o values e retorna um array com todos os valores
     * @name values
     * @return array
     * **/
    public function values() : array
    {
        return $this->execute('values');
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa as outras querys não definidas
     * @name other
     * @return bool
     * **/
    
    public function special() : bool
    {
        return $this->execute();
    }
    
    
}
