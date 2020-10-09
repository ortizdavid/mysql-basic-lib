<?php
namespace examples;

use classes\Query;

require_once '../classes/Query.php';



########################## INSERÇÃO  de Registos######################################################
$query = new Query();
/*$query->insert('tb_cliente', [
            'nome' => 'Joana Francisco',
            'sexo' => 'Feminino',
            'data_nasc' => '1991-11-09',
            'altura' => 1.62
            ])
        ->add();*/

$query2 = new Query();
/*        
$query->insert('tb_contacto', [
            'id_cliente' => $query->getLastId(),
            'telefone' => 928654329,
            'email' => 'joana-009@gmal.com'
            ])
        ->add();*/
#################################################################################################



########################## Obter Registos######################################################
$nome = "Joana Francisco";
echo "<h3>Obtendo os Dados do cliente com Nome '{$nome}'</h3>";
$query4 = new Query();
$cliente = $query4->select()
                   ->from('tb_cliente')
                   ->where('nome','=', $nome)
                   ->one();

echo "Nome: {$cliente->nome}<br>Sexo: {$cliente->sexo}<br>";   

$id = 11;
echo "<h3>Obtendo os Dados do cliente com Id '{$id}'</h3>";
$query4 = new Query();
$cliente = $query4->select()
                ->from('tb_cliente')
                ->where('id_cliente','=', $id)
                ->one();

echo "Nome: {$cliente->nome}<br>Sexo: {$cliente->sexo}";      
echo "<hr><br>";  
#################################################################################################



########################## Obter apenas os nomes e altulras ######################################################
echo "<h3>Obtendo apenas os nomes dos clientes'</h3>";
$query4 = new Query();
$nomes = $query4->select('nome as valor')
       ->from('tb_cliente')
       ->values();
foreach ($nomes as $nome){
    echo "<li>{$nome}</li>";  
}
 
$nome = "Joana Francisco";
echo "<h3>Obtendo apenas as alturas dos clientes'</h3>";
$query4 = new Query();
$alturas = $query4->select('altura as valor')
       ->from('tb_cliente')
       ->values();
foreach ($alturas as $altura){
    echo "<li>{$altura} </li>";  
}
echo "<hr><br>";
#################################################################################################



########################## Actualização de Registos######################################################
$altura = 1.72;
$sexo = 'Masculino';
echo "<h3>Actualizando a Altura dos clientes do sexo '{$sexo}', para '{$altura}m'  </h3>";
$query3 = new Query();
$query3->update('tb_cliente', ['altura' => $altura], ['sexo'=>$sexo])
      ->edit();
echo "<hr><br>";
#################################################################################################


########################## Actualização de Registos######################################################
$id = 6;
echo "<h3>Foi Eliminado o cliente com o Id:'{$id}' </h3>";
$query3 = new Query();
$query3->delete('tb_cliente')
       ->where('id_cliente', '=', $id)
       ->remove();
echo "<hr><br>";
#################################################################################################



########################## Listagem de Clientes ######################################################
$lista = $query->select()
               ->from('tb_cliente')
               ->all();

$total = count(($lista));               

echo "<h3>Listagem de Clientes<br>Total: {$total}</h3>";
echo "";
echo "<table border='1'>";
echo "  <tr>";
echo "      <th>Id</th>";
echo "      <th>Nome</th>";
echo "      <th>Sexo</th>";
echo "      <th>Data de Nascimento</th>";
echo "      <th>Altura</th>";
echo "  </tr>";
foreach ($lista as $cl) {
    echo "  <tr>";
    echo "      <td>{$cl->id_cliente}</td>";
    echo "      <td>{$cl->nome}</td>";
    echo "      <td>{$cl->sexo}</td>";
    echo "      <td>{$cl->data_nasc}</td>";
    echo "      <td>{$cl->altura} </td>";
    echo "  </tr>";
}
echo "</table>";
echo "<p>Colunas Afectadas: {$query->getNumCols()}</p>";
echo "<p>Linhas Afectadas: {$query->getNumRows()}</p>";
echo "<hr><br>";
##########################################################################################################



########################## Listagem de Contactos  ######################################################
$lista = $query2->select('nome, telefone, email')
       ->from('tb_cliente')
       ->join('tb_contacto', 'tb_contacto.id_cliente=tb_cliente.id_cliente')
       ->all();

$total = count(($lista));               

echo "<h3>Listagem dos Contactos Clientes<br>Total: {$total}</h3>";
echo "";
echo "<table border='1'>";
echo "  <tr>";
echo "      <th>Nome</th>";
echo "      <th>Telefone</th>";
echo "      <th>Email</th>";
echo "  </tr>";
foreach ($lista as $cl) {
    echo "  <tr>";
    echo "      <td>{$cl->nome}</td>";
    echo "      <td>{$cl->telefone}</td>";
    echo "      <td>{$cl->email}</td>";
    echo "  </tr>";
}
echo "</table>";
echo "<p>Colunas Afectadas: {$query2->getNumCols()}</p>";
echo "<p>Linhas Afectadas: {$query2->getNumRows()}</p>";
echo "<hr><br>";
##########################################################################################################