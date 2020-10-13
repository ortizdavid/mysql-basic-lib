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
     * @param string $period
     * @return string
     * */
    private function mysqlPeriod(string $period) : string
    {
        $period = strtolower($period);
        switch ($period) {
            case 'year': $value = "YEAR"; break;
            case 'month': $value = "YEAR_MONTH"; break;
            case 'week': $value = "WEEK"; break;
            case 'day': $value = "DAY"; break;
            case 'hour': $value = "HOUR"; break;
            case 'minute': $value = "MINUTE"; break;
            case 'second': $value = "SECOND"; break;
            case 'microsecond': $value = "MINUTE_MICROSECOND"; break;
            default: $value = ""; break;
        }
        return $value;
    }
    
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name age
     * @desc Calcula a idade com base na data
     * @param string $date
     * @return int
     * @example: $tb->age('1994-10-22') 
     * */
    public function age(string $date) : int
    {
        try {
            $sql = "SELECT TIMESTAMPDIFF(YEAR, :date, CURDATE())  AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return (int) $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name subDate
     * @desc Subtrai um intervalo de tempo na data e retorna o resultado
     * <br> Pode subtrair: semana, dias, anos, etc
     * @param string $date
     * @param string $interval
     * @param string $period
     * @return string
     * @example: $tb->subDate('2020-10-22', '3', 'year') 
     * @example: $tb->subDate('2020-10-22', '5', 'month') 
     * */
    public function subDate(string $date, string $interval, string $period) : string
    {
        try {
            $value = $this->mysqlPeriod($period);
            $sql = "SELECT DATE_SUB(:date, INTERVAL {$interval} {$value}) as result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name addDate
     * @desc Adiciona um intervalo de tempo na data e retorna o resultado
     * <br> Pode adicionar: semana, dias, anos, etc
     * @param string $date
     * @param string $interval
     * @param string $period
     * @return string
     * @example: $tb->addDate('2020-10-22', '15', 'day') 
     * @example: $tb->addDate('2013-10-22', '8', 'year')
     * */
    public function addDate(string $date, string $interval, string $period) : string
    {
        try {
            $value = $this->mysqlPeriod($period);
            $sql = "SELECT DATE_ADD(:date, INTERVAL {$interval} {$value}) AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name diffDate
     * @desc Calcula a diferença de datas
     * @param string $date1
     * @param string $date2
     * @return string
     * @example: $tb->diffDate('2000-01-25', '2020-10-10') 
     * @example: $tb->diffDate('2010-10-22 10:00:09', '2012-12-12') 
     * */
    public function diffDate(string $date1, string $date2) : string
    {
        try {
            $sql = "SELECT DATEDIFF(:date1, :date2)  AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date1', $date1, PDO::PARAM_STR);
            $stmt->bindParam(':date2', $date2, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
   
    
    /**@author Ortiz David
     * @copyright 2020
     * @name extract
     * @desc Extrai uma unidade de tempo na data
     * <br> Pode extrair Ano, dia, més, etc
     * @param string $date
     * @param string $period
     * @return string
     * @example: $tb->extract('2000-01-25', 'day')
     * @example: $tb->extract('2000-01-25', 'year')
     * @example: $tb->extract('2000-01-25 09:12:00', 'minute')
     * */
    public function extract(string $date, string $period) : string
    {
        try {
            $value = $this->mysqlPeriod($period);
            $sql = "SELECT EXTRACT({$value} FROM :date) AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
   
    
    /**@author Ortiz David
     * @copyright 2020
     * @name convertPeriod
     * @desc Obtém um determinado Período tempo apartir da data
     * <br> Apenas retorna Ano, dia da Semana, més do Ano, último dia do més, etc
     * @param string $date
     * @return string
     * @example: $tb->convertPeriod('2020-01-10', 'day')
     * @example: $tb->convertPeriod('2000-01-25', 'hour')
     * @example: $tb->convertPeriod('2000-01-25', 'week')
     * */
    public function convertPeriod(string $date, string $type) : string
    {
        try {
            $type = strtolower($type);
            switch ($type) {
                case 'microsecond': $strTime = " MICROSSECOND(:date) "; break;
                case 'second': $strTime = " SECOND(:date) "; break;
                case 'minute':  $strTime = " MINUTE(:date) "; break;
                case 'hour':  $strTime = " HOUR(:date) "; break;
                case 'hour_complete':  $strTime = " TIME(:date) "; break;
                case 'day':  $strTime = " DAY(:date) "; break;
                case 'week':  $strTime = " WEEK(:date) "; break;
                case 'month':  $strTime = " MONTH(:date) "; break;
                case 'month_name':  $strTime = " MONTHNAME(:date) "; break;
                case 'last_day': $strTime = " LAST_DAY(:date) "; break;
                default: $strTime = ""; break;
            }
            $sql = "SELECT {$strTime} AS result ;";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
   
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name addTime
     * @desc Adiciona uma determinada hora (no formato HH:MM:SS) na outra 
     * @param string $hour1
     * @param string $hour2
     * @param string $period
     * @return string
     * @example: $tb->addTime('10:00:09', '08:45:23') 
     * */
    public function addTime(string $hour1, string $hour2) : string
    {
        try {
            $sql = "SELECT ADDTIME(:hour1, :hour2) AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':hour1', $hour1, PDO::PARAM_STR);
            $stmt->bindParam(':hour2', $hour2, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name timeDiff
     * @desc Retorna a diferença de datas/horas
     * @param string $date1
     * @param string $date2
     * @return string
     * @example: $tb->timeDiff('10:00:09', '08:45:23') 
     * @example: $tb->timeDiff('1999-09-02', '2015-12-23') 
     * */
    public function timeDiff(string $date1, string $date2) : string
    {
        try {
            $sql = "SELECT TIMEDIFF(:date1, :date2) AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date1', $date1, PDO::PARAM_STR);
            $stmt->bindParam(':date2', $date2, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name dayOf
     * @desc Retorna dia da semana, dia do ano ou  dia do més
     * @param string $type
     * @return string
     * @example: $tb->dayOf('2019-08-12', 'name')
     * @example: $tb->dayOf('2019-08-12', 'month') 
     * @example: $tb->dayOf('2019-08-12', 'year')
     * */
    public function dayOf(string $date, string $type) : string
    {
        try {
            $type = strtolower($type);
            switch ($type) {
                case 'name': $strTime = " DAYNAME(:date) "; break;
                case 'month': $strTime = " DAYOFMONTH(:date) "; break;
                case 'week':  $strTime = " DAYOFWEEK(:date) "; break;
                case 'year':  $strTime = " DAYOFYEAR(:date) "; break;
                default: $strTime = ""; break;
            }
            $sql = "SELECT {$strTime} AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
    /**@author Ortiz David
     * @copyright 2020
     * @name dateTo
     * @desc Converte uma Data em Dias e segundos
     * @param string $date 
     * @param string $type
     * @return string
     * @example: $tb->dateTo('2013-05-02', 'day')
     * @example: $tb->dateTo('2013-05-02', 'second')
     * */
    public function dateTo(string $date, string $type) 
    {
        try {
            $type = strtolower($type);
            switch ($type) {
                case 'day': $strTime = " TO_DAYS(:date) "; break;
                case 'second':  $strTime = " TO_SECONDS(:date) "; break;
                default: $strTime = ""; break;
            }
            $sql = "SELECT {$strTime} AS result; ";
            $pdo = Connection::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $obj = $stmt->fetch(PDO::FETCH_OBJ);
            Connection::disconnect();
            return $obj->result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    
       
}


