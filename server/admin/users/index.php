<?php
require_once(__DIR__ . "/../../config.php");

use lib\db\BookerDb as Db;
use lib\db\helpers\BookerDbCheck as DbCheck;
use lib\traits\Error;
use lib\services\Validate;
use lib\services\Convert;

class AdminUsers
{
    use Error;
    
    /**Database object (PDO)*/
    private $db;
    
    public function __construct()
    {
        $this->db = new Db();
    }
    
    /**
     * Get user(s) info.
     * /hash(admin) - get all users.
     * /hash(admin)/id(user) - get the user by ID.
     * @return array
     */
    public function getUsersByParams($params)
    {
        list($arrParams['hash'],
             $arrParams['id']
        ) = explode('/', $params['params'], 3);

        if ( !DbCheck::adminRights($this->db, $arrParams['hash']) )
            return $this->error(406, 16);

        $sql = 'SELECT booker_users.id,
                       booker_users.name,
                       booker_users.email,
                       booker_users.hash,
                       booker_users.admin
                FROM booker_users';

        if ($arrParams['id'])
        {
            if ( !DbCheck::userId($this->db, $arrParams['id']) )
                return $this->error(406, 17);

            $sql .= ' WHERE booker_users.id = :id';
            $result = $this->db->execute($sql, ['id' => $arrParams['id']]);
        }
        else
            $result = $this->db->execute($sql);
        
        if (!$result)
            return $this->error();

        return $result;
    }

    /**
     * Registration - write a new user in table.
     * hash(admin) | name | password | email - input.
     * @return bool
     */
    public function postUsers($params)
    {
        if ( !DbCheck::adminRights($this->db, $params['hash']) )
            return $this->error(406, 16);

        if ( !Validate::checkName($params['name']) )
            return $this->error(406, 6);

        if ( !Validate::checkPassword($params['password']) )
            return $this->error(406, 7);

        if ( !Validate::checkEmail($params['email']) )
            return $this->error(406, 8);

        if ( DbCheck::name($this->db, $params['name']) )
            return $this->error(406, 9);

        if ( DbCheck::email($this->db, $params['email']) )
            return $this->error(406, 10);

        unset($params['hash']);

        $params['password'] = Convert::toMd5($params['password']);

        do
        {
            $params['hash'] = Convert::toMd5( $params['name'] . rand(12345, PHP_INT_MAX) );
        }
        while ( DbCheck::hash($this->db, $params['hash']) );

        $sql = 'INSERT INTO booker_users (name, email, password, hash)
                VALUES (:name, :email, :password, :hash)';
        $result = $this->db->execute($sql, $params);

        if (!$result)
            return $this->error();

        return TRUE;
    }

    /**
     * Update - update user in table.
     * hash(admin) | id | name | password | email - input.
     * @return bool
     */
    public function putUsers($params)
    {
        if ( !DbCheck::adminRights($this->db, $params['hash']) )
            return $this->error(406, 16);

        if ( !DbCheck::userId($this->db, $params['id']) )
            return $this->error(406, 25);

        if ( !Validate::checkName($params['name']) )
            return $this->error(406, 26);

        if ( !Validate::checkPassword($params['password']) )
            return $this->error(406, 27);

        if ( !Validate::checkEmail($params['email']) )
            return $this->error(406, 28);

        unset($params['hash']);

        $params['password'] = Convert::toMd5($params['password']);

        $sql = 'UPDATE booker_users
                SET name = :name,
                    email = :email,
                    password = :password
                WHERE id = :id
                LIMIT 1';
        $result = $this->db->execute($sql, $params);

        if (!$result)
            return $this->error();

        return TRUE;
    }

    
    /**
     * Delete - removing user from table.
     * /hash(admin)/id - input
     * @return bool
     */
    public function deleteUsers($params)
    {
        list($arrParams['hash'],
             $arrParams['id']
        ) = explode('/', $params['params'], 3);
   
        if ( !DbCheck::adminRights($this->db, $arrParams['hash']) )
            return $this->error(406, 16);

        if ( !DbCheck::userId($this->db, $arrParams['id']) )
            return $this->error(406, 29);

        if ( DbCheck::adminById($this->db, $arrParams['id']) )
            return $this->error(406, 46);
            
        $sql = 'DELETE FROM booker_users
                WHERE id = ' . $arrParams['id'] . 
                ' LIMIT 1';
        $result = $this->db->execute($sql);
        
        if (!$result)
            return $this->error();

        return TRUE;
    }
}

if (PHP_SAPI !== 'cli')
{
    try
    {
        $api = new Rest( new AdminUsers );
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