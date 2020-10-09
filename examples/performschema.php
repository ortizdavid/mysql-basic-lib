<?php
namespace examples;

require_once '../classes/PerformanceSchema.php';

use classes\PerformanceSchema as perform;



#######################Obtem o número de Conexões por Usuários #####################################
$lista = perform::getAccounts();
$total = count($lista);
echo "<h3>Mostrando as as Conexões por usuários <br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################


####################### Mostra alguns dados das tabelas da BD #####################################
$lista = perform::getObjSummaryGlobal();
$total = count($lista);
echo "<h3>Mostrando o a Soma, Média , Mínimo e Máximo de Tempo de espera por tabelas <br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################


####################### Mostra Status por conta #####################################
$lista = perform::getStatusByAccount();
$total = count($lista);
echo "<h3>Mostrando Status por conta <br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################


####################### Mostra Tratamento de tabelas #####################################
$lista = perform::getTableHandles();
$total = count($lista);
echo "<h3>Mostrando Tratamento de Tabelas <br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################



####################### estatística de Tempo de espera por tabelas #####################################
$lista = perform::getTableLockWaitsSumByTable();
$total = count($lista);
echo "<h3>Mostrando a estatística das somas de Tempo de espera por tabelas <br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################


####################### estatística de Tempo de espera por índices #####################################
$lista = perform::getTableWaitsSumByIndex();
$total = count($lista);
echo "<h3>Mostrando a estatística de das somasTempo de espera por Índices <br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
###############################################################################

