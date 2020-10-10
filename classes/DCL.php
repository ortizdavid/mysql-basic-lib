<?php
namespace classes;

require_once 'HelpStatement.php';

/**
 * @author Ortiz de Arcanjo António David
 * <br>Emails: ortizaad1994@gmail.com  / ortizdavid-17@gmal.com
 * <br>Telefones: +244 936 166 699 / +244 916 975 061
 * <br>Endereço: Luanda - Angola,  Rua Guliherme Pereira Inglês - Largo das Ingombotas
 * @copyright 2020 
 * @version 1.0.0
 * @name DCL
 * @desc Serve para executar aluguns comandos da DCL: Grant, Revoke
 * @copyright 2020
 */

class DCL
{
    
    use HelpStatement;
    
    
    public static function createUser(string $userName, string $password, string $host='localhost') : bool
    {
        $sql = "CREATE USER IF NOT EXISTS '{$userName}'@'{$host}' IDENTIFIED BY '{$password}';";
        return self::execBoolStmt($sql);
    }


    public static function createRole(string $roleName) : bool
    {
        $sql = "CREATE ROLE IF NOT EXISTS {$roleName};";
        return self::execBoolStmt($sql);
    }
    

    public static function dropUser(string $userName, string $host='localhost') : bool
    {
        $sql = "DROP USER IF EXISTS '{$userName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }


    public static function dropRole(string $roleName) : bool
    {
        $sql = "DROP ROLE IF EXISTS {$roleName}';";
        return self::execBoolStmt($sql);
    }


    public static function alterUser(string $userName, string $authString, string $options='', string $host='localhost') : bool
    {
        $sql = "ALTER USER '{$userName}'@'{$host}' IDENTIFIED BY '{$authString}' {$options}; ";
        return self::execBoolStmt($sql);
    }


    public static function grant(string $userName, string $type, string $on, string $options='', string $host='localhost') : bool
    {
        $sql = "GRANT {$type} ON {$on} TO '{$userName}'@'{$host}' {$options};";
        return self::execBoolStmt($sql);
    }


    public static function grantAll(string $userName, string $on, string $options='', string $host='localhost') : bool 
    { 
        $sql = "GRANT ALL PRIVILEGES ON {$on} TO '{$userName}'@'{$host}' {$options}; ";
        return self::execBoolStmt($sql);
    }


    public static function grantRole(string $userName, string $roles, string $host='localhost') : bool
    {
        $sql = "GRANT ROLE {$roles} TO '{$userName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }


    public static function grantCreateRoutine(string $userName, string $on, string $host='localhost') : bool
    {
        $sql = "GRANT CREATE ROUTINE ON {$on} TO '{$userName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }


    public static function grantExecuteRoutine(string $userName, string $on, string $host='localhost') : bool
    {
        $sql = "GRANT EXECUTE ON {$on} TO '{$userName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }


    public static function grantProxy(string $userName, string $otherUser, string $otherHost, string $host='localhost') : bool
    {
        $sql = "GRANT PROXY ON '{$userName}'@'{$host}' TO '{$otherUser}'@'{$otherHost}';";
        return self::execBoolStmt($sql);
    }


    public static function grantPrivToRole(string $roleName, string $type, string $on, string $options='') : bool
    {
        $sql = "GRANT {$type} ON {$on} TO '{$roleName} {$options};";
        return self::execBoolStmt($sql);
    }

    
    public static function revoke(string $userName, string $type, string $on, string $options='', string $host='localhost') : bool
    {
        $sql = "REVOKE {$type} ON {$on} FROM '{$userName}'@'{$host}' {$options};";
        return self::execBoolStmt($sql);
    }
    

    public static function revokeAll(string $userName, string $on, string $options='', string $host='localhost') : bool
    {
        $sql = "REVOKE ALL ON {$on} FROM '{$userName}'@'{$host}' {$options};";
        return self::execBoolStmt($sql);
    }
    

    public static function revokeProxy(string $userName, string $userOrRole,  string $host='localhost') : bool
    {
        $sql = "REVOKE '{$userOrRole}' FROM '{$userName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }


    public static function revokeRole(string $userName, string $role, string $host='localhost') : bool
    {
        $sql = "REVOKE '{$role}' FROM '{$userName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }
    

    public static function setDefaultRole(string $userName, string $roles, string $host='localhost') : bool
    {
        $sql = "SET DEFAULT ROLE {$roles} TO '{$userName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }
    
    
    public static function renameUser(string $userName, string $newName, string $host='localhost') : bool
    {
        $sql = "RENAME USER '{$userName}'@'{$host}' TO '{$newName}'@'{$host}';";
        return self::execBoolStmt($sql);
    }
    
    
    public static function setPassword(string $userName, string $password, string $host='localhost') : bool
    {
        $user = "'{$userName}'@'{$host}'";
        $sql = "SET PASSWORD FOR {$user}=PASSWORD('{$password}');";
        return self::execBoolStmt($sql);
    }
    

    public static function showGrants(string $userName, string $host='localhost') : array
    {
        $sql = "SHOW GRANTS FOR '{$userName}'@'{$host}';";
        return self::execArrayStmt($sql);
    }


    public static function showPrivileges() : array
    {
        $sql = "SHOW PRIVILEGES;";
        return self::execArrayStmt($sql);
    }


    public static function showGrantsToRole(string $userName, string $role, string $host='localhost') : array
    {
        $sql = "SHOW GRANTS FOR '{$userName}'@'{$host}' USING '{$role}';";
        return self::execArrayStmt($sql);
    }


    public static function showCreateUser(string $userName, string $host='localhost') : array
    {
        $sql = "SHOW CREATE USER '{$userName}'@'{$host}';";
        return self::execArrayStmt($sql);
    }


    public static function getAllUsers() : array
    {
        $sql = "SELECT user, host FROM mysql.user;";
        return self::execArrayStmt($sql);
    }


    public static function getUser(string $userName) : object
    {
        $sql = "SELECT host, user, password_expired,
                authentication_string, password_last_changed
                FROM mysql.user WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }


    public static function getAllPrivileges(string $userName) : object
    {
        $sql = "SELECT *
                FROM mysql.user WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }
    

    public static function getDMLPrivileges(string $userName) : object
    {
        $sql = "SELECT select_priv, insert_priv,
                    update_priv, delete_priv
                FROM mysql.user 
                WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }


    public static function getDDLPrivileges(string $userName) : object
    {
        $sql = "SELECT index_priv, create_priv,
                    alter_priv, drop_priv
                FROM mysql.user 
                WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }


    public static function getDCLPrivileges(string $userName) : object
    {
        $sql = "SELECT create_user_priv, grant_priv,
                    lock_tables_priv, show_db_priv
                FROM mysql.user 
                WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }


    public static function getViewPrivileges(string $userName) : object
    {
        $sql = "SELECT create_view_priv, show_view_priv
                FROM mysql.user 
                WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }


    public static function getRoutinePrivileges(string $userName) : object
    {
        $sql = "SELECT create_routine_priv,
                    alter_routine_priv, execute_priv
                FROM mysql.user 
                WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }
    

    public static function getOtherPrivileges(string $userName) : object
    {
        $sql = "SELECT shutdown_priv, process_priv, 
                    reload_priv, references_priv,
                    file_priv, super_priv, create_template_priv
                FROM mysql.user 
                WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }


    public static function getPrivOfConstraint(string $userName) : object
    {
        $sql = "SELECT max_connections,  max_updates,
                    max_user_connections, max_questions
                FROM mysql.user 
                WHERE user = '{$userName}'; ";
        return self::execObjectStmt($sql);
    }
    

}
