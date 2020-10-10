<?php
namespace classes;

/**
* @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @desc Interface com os métodos básicos de cálculos na tabela
 */
interface Calculation
{
    
    public function increase(string $campo, $valor, int $id) : bool;
    

    public function decrease(string $campo, $valor, int $id) : bool;
    
    
    public function multiply(string $campo, $valor, int $id) : bool;
    
    
    public function divide(string $campo, $valor, int $id) : bool;
    
    
    public function count() : int;
    
    
    public function countWhere(string $campo, $valor) : int;


    public function countExcept(string $campo, $valor) : int;
    
    
    public function max(string $campo, array $condicoes=null);
    
    
    public function min(string $campo, array $condicoes=null);
    
    
    public function avg(string $campo, array $condicoes=null) : float;
    
    
    public function sum(string $campo, array $condicoes=null) : float;
       
}

