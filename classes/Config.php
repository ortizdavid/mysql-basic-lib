<?php
namespace classes;

/**
* @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @desc Classe com Constantes de conexão com a BD
 * @name Config
 * */
class Config
{
    
    /**
     * @desc Constante Driver ou SGBD
     * */
    const DRIVER = 'mysql';
   
    /**
     * @desc Constante HOST ou IP
     * */
    const DB_HOST = 'localhost';
    
    /**
     * @desc Constante Porta do Driver
     * */
    const DB_PORT = 3306; 
    
    /**
     * @desc Constante Nome do Usuário da BD
     * */
    const DB_USER = 'root'; 
    
    /**
     * @desc Constante Nome da BD
     * */
    const DB_NAME = 'bd_teste';
    
    /**
     * @desc Constante Password da BD
     * */
    const DB_PASSWORD = '';
    
    /**
     * @desc Constante Caractere das tabelas da BD
     * */
    const DB_CHARSET = 'utf8mb4';
    
    /**
     * @desc Constante Caractere da BD
     * */
    const COLLATION = 'utf8mb4_unicode_ci';
    
    /**
     * @desc Constante Engine da BD
     * */
    const ENGINE = 'MyISAM';  
    
    
}

