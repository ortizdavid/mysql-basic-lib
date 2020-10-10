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
     * @param string $campos
     * @return Query
     * **/
    public function select(string $campos=null) : Query
    {
        $strSelect = "";
        if($campos == null)
            $strSelect = " * ";
        else 
            $strSelect .= $campos;
        $this->_select .= " {$strSelect} ";  
        $this->queryResult .= $this->_select;
        return $this;
    }
    
    
    /**
    * @author Ortiz David
    * @copyright 2020
    * @desc Cria o delete na tabela e as condições
    * @name delete
    * @param string $tabela
    * @return Query
    * **/
    public function delete(string $tabela) : Query
    {
        if(count($this->_delete) > 0)
            $this->_delete[] = " ";
        else{
            $this->_delete[] = " DELETE FROM {$tabela} ";
            $this->queryResult .= $this->_delete[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Cria a query insert na tabela e as condições
     * @name insert
     * @param string $tabela
     * @param array $campos
     * @return Query
     * **/
    public function insert(string $tabela, array $campos) : Query
    {
        $this->_insert .= " INTO {$tabela} ( ";
        $cont = 0;
        $cont2 = 0;
        $strInsert = " ";
        $strValues = " ";
        foreach ($campos as $campo => $valor){
            $cont++;
            $strInsert .= ($cont == count($campos)) ? " {$campo} " : " {$campo}, ";
        }
        $this->_insert .= " {$strInsert} ) ";
        $this->_insert .= " VALUES ( ";
        foreach ($campos as $campo => $valor){
            $cont2++;
            $valor = $this->filter($valor);
            $novoVal = (is_string($valor)) ? " '{$valor}' " : $valor;
            $strValues .= ($cont2 == count($campos)) ? " {$novoVal} " : " {$novoVal}, ";
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
     * @param string $tabela
     * @param array $campos
     * @param array $condicoes
     * @return Query
     * **/
    public function update(string $tabela, array $campos, array $condicoes) : Query
    {
        $this->_update .= " {$tabela} SET ";
        $cont = 0;
        $cont2 = 0;
        $strSet = " ";
        $strCond = " ";
        foreach ($campos as $campo => $valor){
            $cont++;
            $valor = $this->filter($valor);
            $novoValor = (is_string($valor)) ? " '{$valor}' " : $valor;
            $strSet .= ($cont == count($campos)) ? " {$campo} = {$novoValor} " : " {$campo} = {$novoValor}, ";
        }
        foreach ($condicoes as $chave => $val){
            $cont2++;
            $val = $this->filter($val);
            $novoVal = (is_string($val)) ? " '{$val}' " : $val;
            $strCond .= ($cont2 == count($condicoes)) ? " {$chave} = {$novoVal} " : " {$chave} = {$novoVal} AND ";
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
     * @param string $tabela
     * @return Query
     * **/
    public function from(string $tabela) : Query
    {
       if(count($this->_from) > 0)
            $this->_from[] = " ";
        else {
            $this->_from[] = " FROM {$tabela} ";
            $this->queryResult .= $this->_from[0];
        }
       return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando JOIN ON e a tabela 
     * @name join
     * @param string $tabela
     * @param string $on
     * @return Query
     * **/
    public function join(string $tabela, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " JOIN {$tabela} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando LEFT JOIN ON e a tabela
     * @name leftJoin
     * @param string $tabela
     * @param string $on
     * @return Query
     * **/
    public function leftJoin(string $tabela, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " LEFT JOIN {$tabela} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando RIGHT JOIN ON e a tabela
     * @name rightJoin
     * @param string $tabela
     * @param string $on
     * @return Query
     * **/
    public function rightJoin(string $tabela, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " RIGHT JOIN {$tabela} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando FULL JOIN ON e a tabela
     * @name fullJoin
     * @param string $tabela
     * @param string $on
     * @return Query
     * **/
    public function fullJoin(string $tabela, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " FULL JOIN {$tabela} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando FULL CROSS ON e a tabela
     * @name crossJoin
     * @param string $tabela
     * @param string $on
     * @return Query
     * **/
    public function crossJoin(string $tabela, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " CROSS JOIN {$tabela} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando NATURAL CROSS ON e a tabela
     * @name naturalJoin
     * @param string $tabela
     * @param string $on
     * @return Query
     * **/
    public function naturalJoin(string $tabela, string $on=null) : Query
    {
        $strOn = ($on==null) ? "" : " ON ({$on}) " ;
        $this->queryResult .= " NATURAL JOIN {$tabela} {$strOn} ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando WHERE e a condição
     * @name where
     * @param string $tabela
     * @param string $operador
     * @param mixed $valor
     * @return Query
     * **/
    public function where(string $campo, string $operador, $valor) : Query
    {
        if(count($this->_where) > 0)
            $this->_where[] = " ";  
        else {
            $condicao = $this->getCondition($campo, $operador, $valor);
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
     * @param string $tabela
     * @param array $valores
     * @return Query
     * **/
    public function whereIn(string $campo, array $valores) : Query
    {
        if(count($this->_in) > 0)
            $this->_in[] = " ";
        else {
            $cont = 0;
            $strVal = " ";
            foreach ($valores as $valor){
                $cont++;
                $valor = $this->filter($valor);
                $novoValor = (is_string($valor)) ? " '{$valor}' " : $valor;
                $strVal .= ($cont == count($valores)) ? " {$novoValor} " : " {$novoValor}, ";
            }
            $this->_in[] = " WHERE {$campo} IN ({$strVal}) ";
            $this->queryResult .= $this->_in[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando WHERE e o comando NOT IN
     * @name whereNoiIn
     * @param string $tabela
     * @param array $valores
     * @return Query
     * **/
    public function whereNotIn(string $campo, array $valores) : Query
    {
        if(count($this->_notIn) > 0)
            $this->_notIn[] = " ";
            else {
                $cont = 0;
                $strVal = " ";
            foreach ($valores as $valor){
                $cont++;
                $valor = $this->filter($valor);
                $novoValor = (is_string($valor)) ? " '{$valor}' " : $valor;
                $strVal .= ($cont == count($valores)) ? " {$novoValor} " : " {$novoValor}, ";
            }
            $this->_notIn[] = " WHERE {$campo} NOT IN ({$strVal}) ";
            $this->queryResult .= $this->_notIn[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando IS NULL
     * @name _null
     * @param string $tabela
     * @param array $valores
     * @return Query
     * **/
    public function isNull(string $operador='AND', string $campo) : Query
    {
        $strOp = (in_array($operador, ['AND', 'OR'])) ? $operador : ' ___ERROR___ ';
        $this->_notNull .= " {$strOp} {$campo} IS NULL ";
        $this->queryResult .= $this->_notNull;
        return $this;
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando IS NOT NULL
     * @name notNull
     * @param string $tabela
     * @param array $valores
     * @return Query
     * **/
    public function notNull(string $operador='AND', string $campo) : Query
    {
        $strOp = (in_array($operador, ['AND', 'OR'])) ? $operador : ' ___ERROR___ ';
        $this->_nulls .= " {$strOp} {$campo} IS NOT NULL ";
        $this->queryResult .= $this->_nulls;
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando BETWEEN
     * @name between
     * @param string $operador
     * @param string $campo
     * @param string $inicio
     * @param string $fim
     * @return Query
     * **/
    public function between(string $operador='AND', string $campo,  $inicio, $fim) : Query
    {
        $strOp = (in_array($operador, ['AND', 'OR'])) ? $operador : ' ___ERROR___ ';
        $this->_nulls .= " {$strOp} {$campo} BETWEEN {$inicio} AND {$fim} ";
        $this->queryResult .= $this->_nulls;
        return $this;
    }
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando NOT BETWEEN
     * @name notBetween
     * @param string $operador
     * @param string $campo
     * @param string $inicio
     * @param string $fim
     * @return Query
     * **/
    public function notBetween(string $operador='AND', string $campo,  $inicio, $fim) : Query
    {
        $strOp = (in_array($operador, ['AND', 'OR'])) ? $operador : ' ___ERROR___ ';
        $this->_nulls .= " {$strOp} {$campo} NOT BETWEEN {$inicio} AND {$fim} ";
        $this->queryResult .= $this->_nulls;
        return $this;
    }
    
   
      
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando HAVING e a condição
     * @name having
     * @param string $tabela
     * @param string $operador
     * @param mixed $valor
     * @return Query
     * **/
    public function having(string $campo, string $operador, $valor) : Query
    {
        if(count($this->_having) > 0)
            $this->_having[] = " ";
        else {
            $condicao = $this->getCondition($campo, $operador, $valor);
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
     * @param string $tabela
     * @param string $operador
     * @param mixed $valor
     * @return Query
     * **/
    public function and(string $campo, string $operador, $valor) : Query
    {
        $condicao = $this->getCondition($campo, $operador, $valor);
        $this->queryResult .= " AND {$condicao} ";
        return $this;
    }
   
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando OR e a condição
     * @name or
     * @param string $tabela
     * @param string $operador
     * @param mixed $valor
     * @return Query
     * **/
    public function or(string $campo, string $operador, $valor) : Query
    {
        $condicao = $this->getCondition($campo, $operador, $valor);
        $this->queryResult .= " OR {$condicao} ";
        return $this;
    }
    

    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando NOT e a condição
     * @name not
     * @param string $tabela
     * @param string $operador
     * @param mixed $valor
     * @return Query
     * **/
    public function not(string $campo, string $operador, $valor) : Query
    {
        $condicao = $this->getCondition($campo, $operador, $valor);
        $this->queryResult .= " NOT ({$condicao}) ";
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando ORDER BY a ordem ASC ou DESC
     * @name orderBy
     * @param string $campo
     * @param string $ordem
     * @return Query
     * **/
    public function orderBy(string $campo, string $ordem) : Query
    {
        if(count($this->_order) > 0)
            $this->_order[] = " ";
        else{
            $this->_order[] = " ORDER BY {$campo} {$ordem} ";
            $this->queryResult .= $this->_order[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando GROUP BY e o campo
     * @name groupBy
     * @param string $campo
     * @return Query
     * **/
    public function groupBy(string $campo) : Query
    {
        if(count($this->_group) > 0)
            $this->_group[] = " ";
        else{
            $this->_group[] = " GROUP BY {$campo} ";
            $this->queryResult .= $this->_order[0];
        }
        return $this;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando LIMIT inicio, fim
     * @name limit
     * @param int $inicio
     * @param int  $fim
     * @return Query
     * **/
    public function limit(int $inicio, int $fim) : Query
    {
        if(count($this->_limit) > 0)
            $this->_limit[] = " ";
        else{
            $this->_limit[] = " LIMIT {$inicio}, {$fim}  ";
            $this->queryResult .= $this->_limit[0];
        }
        return $this;
    }
    
  
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Concatena a query actual com o comando CALL e o nome do procedimento
     * @name call
     * @param string $proc
     * @return Query
     * **/
    public function call(string $proc) : Query
    {
        if(count($this->_call) > 0)
            $this->_call[] = " ";
        else{
            $this->_call[] = " CALL {$proc} ";
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
     * @param string $campo
     * @param string $operador
     * @param mixed $valor
     * @return string
     * **/
    private function getCondition(string $campo, string $operador, $valor) 
    {
        $valor = $this->filter($valor);
        //$strCond = null;
        switch($operador){
            case 'LIKE': $strCond = " {$campo} LIKE '%{$valor}%' "; break;
            case 'IN': $strCond = " {$campo} IN ({$valor}) ";   break;
            case '=':
            case '<>':
            case '!=':
            case '>':
            case '<':
            case '>=':
            case '<=':
                $novoValor = (is_int($valor)) ? $valor : "'{$valor}'";
                $strCond = " {$campo} {$operador} {$novoValor} ";
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
     * @param mixed $valor
     * @return mixed
     * **/
    private function filter($valor)
    {
        if(is_int($valor)) 
            $filtrado = filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
        else if(is_float($valor)|| is_double($valor)) 
            $filtrado = filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT);
        else if(is_string($valor))
            $filtrado = filter_var($valor, FILTER_SANITIZE_STRING);
        else 
            $filtrado = filter_var($valor);
        return $filtrado;
    }
    
    
    /**
     * @author Ortiz David
     * @copyright 2020
     * @desc Executa a query final e retorna o resultado
     * <br> Pode Executar os comandos Insert, Update, Delete e outros
     * <br> Os Tipos de Operações são: delete, update, create, one, all, value, values e exists
     * @name execute
     * @param string $operacao
     * @return mixed
     * **/
    public function execute(string $operacao='')
    {
        try {
            $sql = $this->getQuery();
            $pdo = Connection::connect();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $operacao = strtolower($operacao);
            
            switch ($operacao)
            {
                case 'insert':
                case 'create':
                case 'new':
                case 'add':
                    $resultado = $stmt->execute();
                    $this->lastId = $pdo->lastInsertId();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    break;
                case 'update':
                case 'edit':
                case 'delete' :
                case 'remove':
                    $resultado = $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    break;
                case 'one':
                case 'find':
                case 'object':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $resultado = $stmt->fetch(PDO::FETCH_OBJ);
                    break;
                case 'all':
                case 'findAll':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
                    break;
                case 'value':
                case 'one_value':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $objecto = $stmt->fetch(PDO::FETCH_OBJ);
                    $resultado = $objecto->valor;
                    break;
                case 'values':
                case 'all_values':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
                    foreach ($resultado as $elemento){
                        $valores [] = $elemento->valor;
                    }
                    $resultado = $valores;
                    break;
                case 'exists':
                case 'contains':
                    $stmt->execute();
                    $this->numCols = $stmt->columnCount();
                    $this->numRows = $stmt->rowCount();
                    $resultado = ($stmt->rowCount() > 0);
                    break;
                default:
                    return $stmt->execute();
                    break;
            }
            $pdo->commit();
            Connection::disconnect();
            return $resultado;
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
