<?php
namespace classes;

/**
 *
 * @author Ortiz David
 * <br>Email: ortizaad1994@gmail.com <br>Tel: +244936166699
 * @name Calculation
 * @desc Interface com os métodos básicos de cálculos na tabela
 * @copyright 2020
 */
interface Calculation
{
    
    public function increase(string $campo, int $id, $valor) : bool;
    

    public function decrease(string $campo, int $id, $valor) : bool;
    
    
    public function multiply(string $campo, int $id, $valor) : bool;
    
    
    public function divide(string $campo, int $id, $valor) : bool;
    
    
    public function count() : int;
    
    
    public function countWhere(string $campo, $valor) : int;


    public function countExcept(string $campo, $valor) : int;
    
    
    public function max(string $campo, array $condicoes=null);
    
    
    public function min(string $campo, array $condicoes=null);
    
    
    public function avg(string $campo, array $condicoes=null) : float;
    
    
    public function sum(string $campo, array $condicoes=null) : float;
       
}

