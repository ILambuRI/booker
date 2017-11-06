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
        list( $id ) = explode('/', $params['params'], 2);

        if ( !DbCheck::eventId($this->db, $id) )
            return $this->error(406, 100);
        
        $sql = 'SELECT booker_events.id,
                       booker_events.user_id,
                       booker_events.room_id,
                       booker_events.`desc`,
                       booker_events.start,
                       booker_events.end,
                       booker_events.created,
                       booker_events.event_id
                FROM booker_events
                WHERE booker_events.id = :id';

        $result = $this->db->execute($sql, ['id' => $id]);

        if (!$result)
            return $this->error();

        return $result;
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

        if ( $params['type'] == 'Weekly' && !((int)$params['duration'] >= 1 && (int)$params['duration'] <= 4) )
            return $this->error(406, 23);

        if ( $params['type'] == 'Bi-weekly' && !((int)$params['duration'] == 1 || (int)$params['duration'] == 2) )
            return $this->error(406, 24);

        if ($params['type'] == 'Monthly')
            $params['duration'] = 1;
        
        $operationResult = [];

        if ($params['type'] != 'Weekly' && $params['type'] != 'Bi-weekly' && $params['type'] != 'Monthly')
        {
            unset($params['type'], $params['duration']);

            $operationResult[] = [
                'start' => $params['timeStart'],
                'end' => $params['timeEnd'],
                'desc' => $params['desc']
            ];
            
            if ( DbCheck::eventAvailable($this->db, $params['roomId'], $params['timeStart'], $params['timeEnd']) ) {
                $sql = 'INSERT INTO booker_events (user_id, room_id, `desc`, start, end, created)
                        VALUES (:userId, :roomId, :desc, :timeStart, :timeEnd, :timeCreate)';
                $result = $this->db->execute($sql, $params);

                $operationResult[0]['success'] = true;
                return $operationResult;
            }
            else
            {
                $operationResult[0]['success'] = false;
                return $operationResult;
            }
        }
        
        $arrParams = [];
        $increase = 0;

        if ($params['type'] == 'Weekly')
        {
            $increase = $this->getTimeWeek();
        }

        if ($params['type'] == 'Bi-weekly') {
            $increase = $this->getTimeWeek(2);
        }

        if ($params['type'] == 'Monthly') {
            $increase = $this->getTimeWeek(4);
        }

        $duration = $params['duration'];
        unset($params['duration'], $params['type']);

        for ($i=0; $i<=$duration; $i++)
        {
            $operationResult[$i] = [
                'start' => $params['timeStart'],
                'end' => $params['timeEnd'],
                'desc' => $params['desc']
            ];

            if ( DbCheck::eventAvailable($this->db, $params['roomId'], $params['timeStart'], $params['timeEnd']) )
            {
                $operationResult[$i]['success'] = true;

                if ( empty($arrParams) )
                {
                    $arrParams[0] = [
                        'sql' => 'INSERT INTO booker_events (user_id, room_id, `desc`, start, end, created)
                                  VALUES (:userId, :roomId, :desc, :timeStart, :timeEnd, :timeCreate)',
                        'params' => $params
                    ];

                    $arrParams[1] = ['sql' => 'UPDATE booker_events
                                               SET event_id = :lastId
                                               WHERE id = :lastId'
                    ];                            
                }
                else
                {
                    $arrParams[] = [
                        'sql' => 'INSERT INTO booker_events (user_id, room_id, `desc`, start, end, created, event_id)
                                  VALUES (:userId, :roomId, :desc, :timeStart, :timeEnd, :timeCreate, :lastId)',
                        'params' => $params
                    ];
                }
            }
            else
            {
                $operationResult[$i]['success'] = false;            
            }

            $params['timeStart'] = $params['timeStart'] + $increase;
            $params['timeEnd'] = $params['timeEnd'] + $increase;
        }

        if (empty($arrParams))
        {
            return $operationResult;
        }

        $result = $this->db->execEventsTransaction($arrParams);
        return $operationResult;
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

    private function getTimeWeek($cnt = 1)
    {
        return 60 * 60 * 24 * 7 * $cnt;
    }
}

if (PHP_SAPI !== 'cli')
{
    try
    {
        $api = new Rest( new Events );
        $api->table = 'events';
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