<?php
require_once(__DIR__ . "/../../config.php");

use lib\db\BookerDb as Db;
use lib\db\helpers\BookerDbCheck as DbCheck;
use lib\traits\Error;
use lib\services\Validate;
use lib\services\Convert;

class Users
{
    use Error;
    
    /**Database object (PDO)*/
    private $db;
    
    public function __construct()
    {
        $this->db = new Db();
    }

    /**
     * Login - user authorization, if it is in the table,
     * we write a new hash.
     * name | password - input.
     * Return user info array whith new hash.
     * @return array
     */
    public function putUsers($params)
    {
        if ( !Validate::checkName($params['name']) )
            return $this->error(406, 11);

        if ( !Validate::checkPassword($params['password']) )
            return $this->error(406, 12);
        
        $params['password'] = Convert::toMd5($params['password']);
        
        if ( !DbCheck::name($this->db, $params['name']) )
            return $this->error(406, 13);
        
        if ( !DbCheck::password($this->db, $params['name'], $params['password']) )
            return $this->error(406, 14);
            
        $arrParams['name'] = $params['name'];

        do
        {
            $arrParams['hash'] = Convert::toMd5( $params['name'] . rand(12345, PHP_INT_MAX) );
        }
        while ( DbCheck::hash($this->db, $arrParams['hash']) );
        
        $sql = 'UPDATE booker_users
                SET hash = :hash
                WHERE name = :name';
        $result = $this->db->execute($sql, $arrParams);

        if (!$result)
            return $this->error();
            
        return $this->getUserInfo($arrParams['hash']);
    }

    /**
     * Logout - removing (updating) a hash in tables.
     * /hash - input
     * @return bool
     */
    public function deleteUsers($params)
    {
        if ( !DbCheck::hash($this->db, $params['params']) )
            return $this->error(404, 15);

        do
        {
            $newHash = Convert::toMd5( rand(12345, PHP_INT_MAX) );
        }
        while ( DbCheck::hash($this->db, $newHash) );

        $sql = 'UPDATE booker_users SET hash = "' .$newHash. '" WHERE hash = :hash';
        $result = $this->db->execute($sql, ['hash' => $params['params']]);
        
        if (!$result)
            return $this->error();

        return TRUE;
    }
    
    /** 
     * Get user info array.
     * @return array
     */
    private function getUserInfo($hash)
    {
        $sql = 'SELECT booker_users.id,
                       booker_users.name,
                       booker_users.email,
                       booker_users.hash,
                       booker_users.admin
                FROM booker_users
                WHERE booker_users.hash = :hash';
        $result = $this->db->execute($sql, ['hash' => $hash]);

        return $result[0];
    }
}

if (PHP_SAPI !== 'cli')
{
    try
    {
        $api = new Rest( new Users );
        $api->table = 'users';
        $api->play();
    }
    catch (Exception $e)
    {
        header( "HTTP/1.1 500 Internal Server Error | " . ERROR_HEADER_CODE . $e->getMessage() );
        header("Content-Type:text/html");
    
        $string = ERROR_HTML_TEXT;
        ksort( $patterns = ['/%STATUS_CODE%/', '/%ERROR_DESCRIPTION%/', '/%CODE_NUMBER%/'] );
        ksort( $replacements = [500, 'Internal Server Error', ERROR_HEADER_CODE . $e->getMessage()] );
        echo preg_replace($patterns, $replacements, $string);
    
        exit;
    }
}
