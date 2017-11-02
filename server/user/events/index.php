<?php
require_once(__DIR__ . "/../../config.php");

use lib\db\BookerDb as Db;
use lib\db\helpers\BookerDbCheck as DbCheck;
use lib\traits\Error;
use lib\services\Validate;
use lib\services\Convert;

class Events
{
    use Error;
    
    /**Database object (PDO)*/
    private $db;
    
    public function __construct()
    {
        $this->db = new Db();
    }
    
    /**
     * /name - сheck for the existence of a name in the table.
     * /null(or false)/email - Check for the existence of email in the table.
     * @return bool
     */
    public function getEventsByParams($params)
    {
        list($arrParams['name'],
             $arrParams['email']
        ) = explode('/', $params['params'], 3);
        
        if ( ($arrParams['name'] != 'false' && $arrParams['name'] != 'null' && $arrParams['name'] != null)
             &&
             ($arrParams['email'] == 'false' || $arrParams['email'] == 'null' ||  $arrParams['email'] == null) )
        {
            if ( !Validate::checkName($arrParams['name']) )
                return $this->error(406, 3);

            $sql = 'SELECT name FROM booker_users WHERE name = :name';
            $result = $this->db->execute($sql, ['name' => $arrParams['name']]);
            
            if (!$result)
                return $this->error();

            return TRUE;
        }

        if ( ($arrParams['email'] != 'false' && $arrParams['email'] != 'null' && $arrParams['email'] != null)
             &&
             ($arrParams['name'] == 'false' || $arrParams['name'] == 'null' ||  $arrParams['name'] == null) )
        {
            if ( !Validate::checkEmail($arrParams['email']) )
                return $this->error(406, 4);

            $sql = 'SELECT email FROM booker_users WHERE email = :email';
            $result = $this->db->execute($sql, ['email' => $arrParams['email']]);
            
            if (!$result)
                return $this->error();

            return TRUE;
        }

        return $this->error(404, 5);
    }

    /**
     * Add new event.
     * Inputs:
     * userId | roomId | desc | timeCreate | timeStart | timeEnd | type | duration
     * @return bool
     */
    public function postEvents($params)
    {
        if ( !DbCheck::userId($this->db, $params['userId']) )
            return $this->error(406, 18);

        if ( !DbCheck::roomId($this->db, $params['roomId']) )
            return $this->error(406, 19);

        if( !$params['desc'] = Validate::clearText($params['desc']) )
            return $this->error(406, 20);
        
        if ( !(1800 + (int)$params['timeStart'] <= (int)$params['timeEnd']) )
            return $this->error(406, 21);
        
        if ( (int)$params['timeStart'] <= (int)$params['timeCreate'] )
            return $this->error(406, 22);

        if ( !$params['type'] == 'Weekly' && ($params['duration'] >= 1 && $params['duration'] <= 4) )
            return $this->error(406, 23);

        if ( !$params['type'] == 'Bi-weekly' && ($params['duration'] == 1 || $params['duration'] == 2) )
            return $this->error(406, 24);

        if ($params['type'] == 'Monthly')
            $params['duration'] == 1;
        
        $sql = 'INSERT INTO booker_events (user_id, room_id, `desc`, start, end, created, event_id)
                VALUES (:userId, :roomId, :desc, :timeStart, :timeEnd, :timeCreate, :lastId)';
        $result = $this->db->execute($sql, $params);

        if (!$result)
            return $this->error();

        return TRUE;
    }

    /**
     * Login - user authorization, if it is in the table,
     * we write a new hash.
     * name | password - input.
     * Return user info array whith new hash.
     * @return array
     */
    public function putEvents($params)
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
    public function deleteEvents($params)
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
        $api = new Rest( new Events );
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