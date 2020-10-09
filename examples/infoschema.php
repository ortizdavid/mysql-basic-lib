<?php
namespace examples;

use classes\InformationSchema as info;

require_once '../classes/InformationSchema.php';


##################### Listando Todas As Tabelas da Base de Dados ###################
$total = info::getCountTables();
echo "<h3>Listando todas as tabelas da Base de Dados<br>Total: {$total}</h3>";
var_dump(info::getAllTables());
echo "<hr><br>";
#####################################################################################



##################### Obter Dados de Uma Tabela ###################;
$tbName = 'tb_cliente';
echo "<h3>Obtendo os dados da Tabela: '{$tbName}'</h3>";
var_dump(info::getTable($tbName));
echo "<hr><br>";
#####################################################################################



##################### Listando Todos os Procedures ###################
$lista = info::getAllProcedures();
$total = count($lista);
echo "<h3>Listando todos os Procedures da Base de Dados<br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
#####################################################################################


##################### Obter Dados de Um Procedure ###################;
$procName = 'proc_contactos_cliente';
echo "<h3>Obtendo os dados do Procedure: '{$procName}'</h3>";
var_dump(info::getProcedure($procName));
echo "<hr><br>";
#####################################################################################



##################### Listando Todas as functions ###################
$lista = info::getAllFunctions();
$total = count($lista);
echo "<h3>Listando todas as Functions da Base de Dados<br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
#####################################################################################


##################### Obter Dados de Uma Function ###################;
$funName = 'fun_ola_mundo';
echo "<h3>Obtendo os dados da function: '{$funName}'</h3>";
var_dump(info::getFunction($funName));
echo "<hr><br>";
#####################################################################################


##################### Listando Todos os triggers###################
$lista = info::getAllTriggers();
$total = count($lista);
echo "<h3>Listando todos os Triggers da Base de Dados<br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
#####################################################################################


##################### Obter Dados de Uma Function ###################;
$trName = 'tr_teste';
echo "<h3>Obtendo os dados do Trigger: '{$trName}'</h3>";
var_dump(info::getTrigger($trName));
echo "<hr><br>";
#####################################################################################
