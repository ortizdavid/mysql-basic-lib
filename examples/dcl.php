<?php

namespace examples;

use classes\DCL;

require_once '../classes/DCL.php';


####################### CRIANDO UM USUÁRIO #####################################
$userName = 'usuario_teste';
$password = '123';
echo "<h3>Criando o Usuário: '{$userName}'</h3>";
var_dump(DCL::createUser($userName, $password));
echo "<hr><br>";
###############################################################################