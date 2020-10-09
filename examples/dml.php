<?php
namespace examples;

use classes\DML;

require_once '../classes/DML.php';



####################### Mostra as Tabelas em  #####################################
$lista = DML::showTables();
$total = count($lista);
echo "<h3>Mostrando as Tabelas da Base de Dados<br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################


####################### Mostra as os Indices  #####################################
$tbName = 'tb_cliente';
$lista = DML::showIndexesFrom($tbName);
$total = count($lista);
echo "<h3>Mostrando os índices da Tabela '{$tbName}'<br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################



####################### Mostra a CXriação da Tabela  #####################################
$tbName = 'tb_cliente';
echo "<h3>Mostrando os índices da Tabela '{$tbName}'<br></h3>";
var_dump(DML::showCreateTable($tbName));
echo "<hr><br>";
###############################################################################



####################### Mostra o status e uma tabela #####################################
$tbName = 'tb_cliente';
echo "<h3>Mostrando o Status da Tabela: '{$tbName}'</h3>";
var_dump(DML::showTableStatus($tbName));
echo "<hr><br>";
###############################################################################


####################### Mostra as Tabelas em Uso #####################################
$tbName = 'tb_';
echo "<h3>Mostrando as Tabelas em Uso contendo: '{$tbName}'</h3>";
var_dump(DML::showOpenTables($tbName));
echo "<hr><br>";
###############################################################################