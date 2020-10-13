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
    
    
    public function deleteOnly(string $field, $value) : bool; 
    

    public function deleteMany(string $field, array $values) : bool; 
    
    
    public function deleteWhere(array $conditions, string $operator='AND') : bool;
    
    
    public function search(array $conditions, string $operator='LIKE') : array;
    
    
    public function exists(array $conditions) : bool;
    
    
    public function unique(string $field, $value) : object;
    
    
    public function first() : object;
    
    
    public function last() : object;
    
    
    public function middle() : object;
    
    
    public function values(string $field, array $conditions=null) : array;
    
    
    public function between(string $field, $start, $end) : array;
    
    
    public function notBetween(string $field, $start, $end) : array;
    
    
    public function in(string $field, array $values) : array;
    
    
    public function notIn(string $field, array $values) : array;
    
    
    public function isNull(string $field) : array;
    
    
    public function isNotNull(string $field) : array;
    
    
    public function distinct(string $field) : array;
    
    
    public function groupBy(string $field) : array;
    
    
    public function except(array $conditions) : array;
    
    
    public function only(array $conditions) : array;
    
    
    public function limit(int $start, int $end) : array;
    
    
    public function orderBy(string $field, string $order='ASC') : array;
    
    
    public function all(array $conditions=null, string $fieldToOrd=null, string $order=null, int $start=null, int $end=null) : array;
    
    
}

