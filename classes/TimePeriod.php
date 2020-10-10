<?php
namespace classes;

//use Connection;
use PDO;
use PDOException;

require_once 'Connection.php';

/**
 * @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @name TimePeriod
 * @desc Trait reponsável pela manipulação de datas 
 */
trait TimePeriod
{
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name mysqlPeriod
     * @desc Converte o tempo em numa palavra reservada do MySQL
     * @param string $tempo
     * @return string
     * */
    private function mysqlPeriod(string $tempo) : string
    {
        $tempo = strtolower($tempo);
        switch ($tempo) {
            case 'ano': $valor = "YEAR"; break;
            case 'mes': $valor = "YEAR_MONTH"; break;
            case 'semana': $valor = "WEEK"; break;
            case 'dia': $valor = "DAY"; break;
            case 'hora': $valor = "HOUR"; break;
            case 'minuto': $valor = "MINUTE"; break;
            case 'segundo': $valor = "SECOND"; break;
            case 'milissegundo': $valor = "MINUTE_MICROSECOND"; break;
            default: $valor = ""; break;
        }
        return $valor;
    }
    
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name age
     * @desc Calcula a idade com base na data
     * @param string $dataNasc
     * @return int
     * @example: $tb->age('1994-10-22') 
     * */
    public function age(string $dataNasc) : int
    {
        try {
            $sql = "SELECT TIMESTAMPDIFF(YEAR, :data_nasc, CURDATE())  'idade'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data_nasc', $dataNasc, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return (int) $objecto->idade;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name subDate
     * @desc Subtrai um intervalo de tempo na data e retorna o resultado
     * <br> Pode subtrair: semana, dias, anos, etc
     * @param string $data
     * @param string $intervalo
     * @param string $tempo
     * @return string
     * @example: $tb->subDate('2020-10-22', '3', 'ano') 
     * @example: $tb->subDate('2020-10-22', '5', 'mes') 
     * */
    public function subDate(string $data, string $intervalo, string $tempo) : string
    {
        try {
            $valor = $this->mysqlPeriod($tempo);
            $sql = "SELECT DATE_SUB(:data, INTERVAL {$intervalo} {$valor}) 'diferenca'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->diferenca;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name addDate
     * @desc Adiciona um intervalo de tempo na data e retorna o resultado
     * <br> Pode adicionar: semana, dias, anos, etc
     * @param string $data
     * @param string $intervalo
     * @param string $tempo
     * @return string
     * @example: $tb->addDate('2020-10-22', '15', 'dia') 
     * @example: $tb->addDate('2013-10-22', '8', 'ano')
     * */
    public function addDate(string $data, string $intervalo, string $tempo) : string
    {
        try {
            $valor = $this->mysqlPeriod($tempo);
            $sql = "SELECT DATE_ADD(:data, INTERVAL {$intervalo} {$valor}) 'soma'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->soma;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name diffDate
     * @desc Calcula a diferença de datas
     * @param string $data1
     * @param string $data2
     * @return string
     * @example: $tb->diffDate('2000-01-25', '2020-10-10') 
     * @example: $tb->diffDate('2010-10-22 10:00:09', '2012-12-12') 
     * */
    public function diffDate(string $data1, string $data2) : string
    {
        try {
            $sql = "SELECT DATEDIFF(:data1, :data2) 'diferenca'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data1', $data1, PDO::PARAM_STR);
            $stmt->bindParam(':data2', $data2, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->diferenca;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
   
    
    /**@author Ortiz David
     * @copyright 2020
     * @name extract
     * @desc Extrai uma unidade de tempo na data
     * <br> Pode extrair Ano, dia, més, etc
     * @param string $data
     * @param string $tempo
     * @return string
     * @example: $tb->extract('2000-01-25', 'dia')
     * @example: $tb->extract('2000-01-25', 'ano')
     * @example: $tb->extract('2000-01-25 09:12:00', 'minuto')
     * */
    public function extract(string $data, string $tempo) : string
    {
        try {
            $valor = $this->mysqlPeriod($tempo);
            $sql = "SELECT EXTRACT({$valor} FROM :data) 'resultado'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
   
    
    /**@author Ortiz David
     * @copyright 2020
     * @name convertPeriod
     * @desc Obtém um determinado Período tempo apartir da data
     * <br> Apenas retorna Ano, dia da Semana, més do Ano, último dia do més, etc
     * @param string $data
     * @return string
     * @example: $tb->convertPeriod('2020-01-10', 'dia')
     * @example: $tb->convertPeriod('2000-01-25', 'hora')
     * @example: $tb->convertPeriod('2000-01-25', 'semana')
     * */
    public function convertPeriod(string $data, string $tipo) : string
    {
        try {
            $tipo = strtolower($tipo);
            switch ($tipo) {
                case 'milissegundo': $strTempo = " MICROSSECOND(:data) "; break;
                case 'segundo': $strTempo = " SECOND(:data) "; break;
                case 'minuto':  $strTempo = " MINUTE(:data) "; break;
                case 'hora':  $strTempo = " HOUR(:data) "; break;
                case 'hora_completa':  $strTempo = " TIME(:data) "; break;
                case 'dia':  $strTempo = " DAY(:data) "; break;
                case 'semana':  $strTempo = " WEEK(:data) "; break;
                case 'mes':  $strTempo = " MONTH(:data) "; break;
                case 'nome_mes':  $strTempo = " MONTHNAME(:data) "; break;
                case 'ultimo_dia_mes': $strTempo = " LAST_DAY(:data) "; break;
                default: $strTempo = ""; break;
            }
            $sql = "SELECT {$strTempo} 'resultado' ;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
   
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name addTime
     * @desc Adiciona uma determinada hora (no formato HH:MM:SS) na outra 
     * @param string $hora1
     * @param string $hora2
     * @param string $tempo
     * @return string
     * @example: $tb->addTime('10:00:09', '08:45:23') 
     * */
    public function addTime(string $hora1, string $hora2) : string
    {
        try {
            $sql = "SELECT ADDTIME(:hora1, :hora2) 'soma'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':hora1', $hora1, PDO::PARAM_STR);
            $stmt->bindParam(':hora2', $hora2, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->soma;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name timeDiff
     * @desc Retorna a diferença de datas/horas
     * @param string $data1
     * @param string $data2
     * @return string
     * @example: $tb->timeDiff('10:00:09', '08:45:23') 
     * @example: $tb->timeDiff('1999-09-02', '2015-12-23') 
     * */
    public function timeDiff(string $data1, string $data2) : string
    {
        try {
            $sql = "SELECT TIMEDIFF(:data1, :data2) 'diferenca'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data1', $data1, PDO::PARAM_STR);
            $stmt->bindParam(':data2', $data2, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->diferenca;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name dayOf
     * @desc Retorna dia da semana, dia do ano ou  dia do més
     * @param string $tipo
     * @return string
     * @example: $tb->dayOf('2019-08-12', 'nome_dia')
     * @example: $tb->dayOf('2019-08-12', 'dia_mes') 
     * @example: $tb->dayOf('2019-08-12', 'dia_ano')
     * */
    public function dayOf(string $data, string $tipo) : string
    {
        try {
            $tipo = strtolower($tipo);
            switch ($tipo) {
                case 'nome_dia': $strTempo = " DAYNAME(:data) "; break;
                case 'dia_mes': $strTempo = " DAYOFMONTH(:data) "; break;
                case 'dia_semana':  $strTempo = " DAYOFWEEK(:data) "; break;
                case 'dia_ano':  $strTempo = " DAYOFYEAR(:data) "; break;
                default: $strTempo = ""; break;
            }
            $sql = "SELECT {$strTempo} 'resultado'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name dateTo
     * @desc Converte uma Data em Dias e segundos
     * @param string $data 
     * @param string $tipo
     * @return string
     * @example: $tb->dateTo('2013-05-02', 'dia')
     * @example: $tb->dateTo('2013-05-02', 'segundo')
     * */
    public function dateTo(string $data, string $tipo) 
    {
        try {
            $tipo = strtolower($tipo);
            switch ($tipo) {
                case 'dia': $strTempo = " TO_DAYS(:data) "; break;
                case 'segundo':  $strTempo = " TO_SECONDS(:data) "; break;
                default: $strTempo = ""; break;
            }
            $sql = "SELECT {$strTempo} 'resultado'; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
            $objecto = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $objecto->resultado;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
       
}


