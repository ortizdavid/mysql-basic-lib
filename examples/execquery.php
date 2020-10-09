<?php
namespace examples;

use classes\ExecQuery;

require_once '../classes/ExecQuery.php';

###Criação do Objecto####
$eq = new ExecQuery();


################### Inserção de Registos ##########################
$add = $eq->execInsert("INSERT INTO tb_cliente(nome, sexo, data_nasc) VALUES ('Antonio Joaquim', 'Masculino', '1980-08-06');");
echo "<h3>Inserido um Registo</h3>";
var_dump($add);
echo "<hr><br>";
##################################################################


################### Inserção de Registos ##########################
$attr='id_cliente';
$val = 1;
$edit = $eq->execUpdateDelete("UPDATE tb_cliente SET data_nasc = '1947-01-09' WHERE {$attr} = {$val};");
echo "<h3>Actualizando a data do cliente '{$val}' </h3>";
var_dump($edit);
echo "<hr><br>";
##################################################################



################### Listagem de Registos ##########################
$lista = $eq->execSelectAll("SELECT * FROM tb_cliente;");
$total = count($lista);
echo "<h3>Listagem de todos os clientes<br>Total: {$total}</h3>";
var_dump($lista);
echo "<hr><br>";
##################################################################


################### Obter um Único registo##########################
$attr = 'id_cliente';
$val = 1;
$obj = $eq->execSelectOne("SELECT * FROM tb_cliente WHERE {$attr} = $val;");
echo "<h3>Obter os dados do cliente '{$val}' </h3>";
var_dump($obj);
echo "<hr><br>";
##################################################################


################### Obter os os nomes dos clientes ##########################
$values = $eq->execSelectValues("SELECT nome as valor FROM tb_cliente;");
echo "<h3>Obter os nomes dos clientes </h3>";
var_dump($values);
echo "<hr><br>";
##################################################################


################### Obter altura do cliente com id ##########################
$attr = 'id_cliente';
$val = 1;
$values = $eq->execSelectField("SELECT altura as resultado FROM tb_cliente WHERE {$attr} = {$val};");
echo "<h3>Obter os Altura do Cliente: '{$val}'</h3>";
var_dump($values);
echo "<hr><br>";
##################################################################


################### Verifica se existe um registo ##########################
$attr = 'nome';
$val = 'Maria Francisco';
$values = $eq->execExists("SELECT * FROM tb_cliente WHERE {$attr} = '{$val}';");
echo "<h3>Verificar se Existe o cliente: '{$val}'</h3>";
var_dump($values);
echo "<hr><br>";
##################################################################



################## Criando Uma Tabela ##########################
$tbName = 'tb_exemplo_tabela';
$res = $eq->execSpecial("CREATE TABLE IF NOT EXISTS {$tbName} (id INT, nome VARCHAR(100));");
echo "<h3>Criando a Tabela '{$tbName}'</h3>";
var_dump($res);
echo "<hr><br>";
##################################################################