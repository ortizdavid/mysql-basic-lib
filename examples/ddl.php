<?php
namespace examples;

use classes\DDL;

require_once '../classes/DDL.php';


####################### Criando uma Base de Dados  #####################################
$dbName = 'bd_exemplo';
echo "<h3>Criando a Base dados '{$dbName}'</h3>";
var_dump(DDL::createDatabase($dbName, 'utf8_bin'));
echo "<hr><br>";
###############################################################################


####################### Eliminando uma Base de Dados  #####################################
$dbName = 'bd_exemplo2';
echo "<h3>Eliminando a Base dados '{$dbName}'</h3>";
var_dump(DDL::dropDatabase($dbName));
echo "<hr><br>";
###############################################################################


####################### Criando uma Tabela  #####################################
$tbName = 'tb_endereco';
echo "<h3>Criando a Tabela '{$tbName}'</h3>";
var_dump(DDL::createTable($tbName, ['cidade'=>'VARCHAR(50)', 'rua'=>'VARCHAR(100)']));
echo "<hr><br>";



####################### Criando um Procedimento #####################################
/*$procName = 'proc_mostrar_nome';
echo "<h3>Criando o Procedimento '{$procName}'</h3>";
var_dump(DDL::createProcedure($procName, ['IN id INT'], "SELECT nome FROM tb_cliente WHERE id_cliente = id;"));
echo "<hr><br>";*/

