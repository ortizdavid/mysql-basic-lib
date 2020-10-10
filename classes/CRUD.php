<?php
namespace classes;

/**
 * @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @name CRUD
 * @desc Interface com os métodos básicos (CRUD) de uma tabela da base de dados
 */
interface CRUD
{
    
    public function insert(array  $obj) : bool;
    

    public function update(array $obj, int $id) : bool;
    
    
    public function find(int $id) : object;
    
    
    public function findAll() : array;
    
    
    public function delete(int $id) : bool; 
    
    
    public function deleteOnly(string $campo, $valor) : bool; 
    

    public function deleteMany(string $campo, array $valores) : bool; 
    
    
    public function deleteWhere(array $condicoes, string $operador='AND') : bool;
    
    
    public function search(array $condicoes, string $operador='LIKE') : array;
    
    
    public function exists(array $condicoes) : bool;
    
    
    public function unique(string $campoUnico, $valor) : object;
    
    
    public function first() : object;
    
    
    public function last() : object;
    
    
    public function middle() : object;
    
    
    public function values(string $campo, array $condicoes=null) : array;
    
    
    public function between(string $campo, $inicio, $fim) : array;
    
    
    public function notBetween(string $campo, $inicio, $fim) : array;
    
    
    public function in(string $campo, array $valores) : array;
    
    
    public function notIn(string $campo, array $valores) : array;
    
    
    public function isNull(string $campo) : array;
    
    
    public function isNotNull(string $campo) : array;
    
    
    public function distinct(string $campo) : array;
    
    
    public function groupBy(string $campo) : array;
    
    
    public function except(array $condicoes) : array;
    
    
    public function only(array $condicoes) : array;
    
    
    public function limit(int $inicio, int $fim) : array;
    
    
    public function orderBy(string $campo, string $ordem='ASC') : array;
    
    
    public function all(array $condicoes=null, string $campoOrdem=null, string $ordem=null, int $inicio=null, int $fim=null) : array;
    
    
}

