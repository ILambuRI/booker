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
     * Write a new user in table.
     * hash(admin) | login | password | phone | idDiscount | admin(1 or 0) | active(1 or 0)- input.
     * Return 200 or 400+.
     */
    public function postUsers($params)
    {
        if ( !$this->checkAdminRights($params['hash']) )
            return $this->error(406, 33);

        if ( !Validate::checkLogin($params['login']) )
            return $this->error(406, 49);

        if ( !Validate::checkPassword($params['password']) )
            return $this->error(406, 50);

        if ( !Validate::checkPhone($params['phone']) )
            return $this->error(406, 51);

        if ( $this->checkLogin($params['login']) )
            return $this->error(406, 52);

        if ( !$this->checkDiscountId($params['idDiscount']) )
            return $this->error(404, 53);
        
        if ((int)$params['admin'] != 1 && (int)$params['admin'] != 0)
            return $this->error(406, 54);
        
        if ((int)$params['active'] != 1 && (int)$params['active'] != 0)
            return $this->error(406, 55);
        
        $params['password'] = Convert::toMd5($params['password']);
        $params['hash'] = Convert::toMd5( $params['login'] . rand(12345, PHP_INT_MAX) );
        $params['lifetime'] = time();

        $sql = 'INSERT INTO bookshop_users (login, password, phone, id_discount, hash, lifetime, admin, active)
                VALUES (:login, :password, :phone, :idDiscount, :hash, :lifetime, :admin, :active)';
        $result = $this->db->execute($sql, $params);

        if (!$result)
            return $this->error();

        return TRUE;
    }

    /**
     * Update user data.
     * hash(admin) | id(users) | login | password | phone | idDiscount | admin(1 or 0) | active(1 or 0)- input.
     * Return 200 or 400+.
     */
    public function putUsers($params)
    {
        if ( !$this->checkAdminRights($params['hash']) )
            return $this->error(406, 33);

        if ( !$this->checkUsersId($params['id']) )
            return $this->error(404, 56);

        if ( !Validate::checkLogin($params['login']) )
            return $this->error(406, 57);

        if ( !Validate::checkPassword($params['password']) )
            return $this->error(406, 58);

        if ( !Validate::checkPhone($params['phone']) )
            return $this->error(406, 59);

        if ( !$this->checkDiscountId($params['idDiscount']) )
            return $this->error(406, 60);
        
        if ((int)$params['admin'] != 1 && (int)$params['admin'] != 0)
            return $this->error(406, 61);
        
        if ((int)$params['active'] != 1 && (int)$params['active'] != 0)
            return $this->error(406, 62);
        
        $params['password'] = Convert::toMd5($params['password']);
        $params['hash'] = Convert::toMd5( $params['login'] . rand(12345, PHP_INT_MAX) );
        $params['lifetime'] = time();

        $sql = 'UPDATE bookshop_users
                SET login = :login,
                    password = :password,
                    phone = :phone,
                    hash = :hash,
                    lifetime = :lifetime,
                    admin = :admin,
                    active = :active,
                    id_discount = :idDiscount
                WHERE id = :id';
        $result = $this->db->execute($sql, $params);

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