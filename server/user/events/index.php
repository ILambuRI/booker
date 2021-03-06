<?php
require_once(__DIR__ . "/../../config.php");

use lib\db\BookerDb as Db;
use lib\db\helpers\BookerDbCheck as DbCheck;
use lib\traits\Error;
use lib\services\Validate;
use lib\services\Convert;

class UserEvents
{
    use Error;
    
    /**Database object (PDO)*/
    private $db;
    
    public function __construct()
    {
        $this->db = new Db();
    }
    
    /**
     * Receiving event information by id.
     * /id - input.
     * @return array
     */
    public function getEventsByParams($params)
    {
        list( $id ) = explode('/', $params['params'], 2);

        if ( !DbCheck::eventId($this->db, $id) )
            return $this->error(406, 31);

        $sql = 'SELECT booker_events.id,
                       booker_events.user_id,
                       booker_events.room_id,
                       booker_events.`desc`,
                       booker_events.start,
                       booker_events.end,
                       booker_events.created,
                       booker_events.event_id,
                       booker_users.name as user_name
                FROM booker_events
                LEFT JOIN booker_users
                    ON booker_events.user_id = booker_users.id
                WHERE booker_events.id = :id';

        $result = $this->db->execute($sql, ['id' => $id]);

        if (!$result)
            return $this->error();

        return $result;
    }


    /**
     * Add new event.
     * Inputs:
     * userId | roomId | desc | timeCreate(timestamp) | timeStart(timestamp) | timeEnd(timestamp) | type(Weekly | Bi-weekly | Monthly) | duration.
     * @return array
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

        /* One event */
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

                $operationResult[0]['success'] = TRUE;
                return $operationResult;
            }
            else
            {
                $operationResult[0]['success'] = FALSE;
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

        /* Many events */
        for ($i=0; $i<=$duration; $i++)
        {
            $operationResult[$i] = [
                'start' => $params['timeStart'],
                'end' => $params['timeEnd'],
                'desc' => $params['desc']
            ];

            if ( DbCheck::eventAvailable($this->db, $params['roomId'], $params['timeStart'], $params['timeEnd']) )
            {
                $operationResult[$i]['success'] = TRUE;

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
                $operationResult[$i]['success'] = FALSE;            
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
     * Event Update(s).
     * hash | userId | roomId | eventId | startHour(8-20) | startMinutes(0 | 30) | endHour(8-20) | endMinutes(0 | 30) | desc | reacurring(true | false) - input.
     * @return array
     */
    public function putEvents($params)
    {
        $userId = $this->getUserIdByHash($params['hash']);

        if ( !DbCheck::eventId($this->db, $params['eventId']) )
            return $this->error(404, 38);

        if ( !DbCheck::eventOwnById($this->db, $userId, $params['eventId']) )
        {
            if ( !DbCheck::adminRights($this->db, $params['hash']) )
                return $this->error(406, 39);
        }

        if ( !DbCheck::userId($this->db, $params['userId']) )
            return $this->error(406, 40);

        if( !$params['desc'] = Validate::clearText($params['desc']) )
            return $this->error(406, 41);
        
        if ( !$eventInfo = $this->getFutureEventInfoById($params['eventId']) )
            return $this->error(404, 42);

        /* One event */
        if ($params['reacurring'] == 'false')
        {
            $newStartHour = new DateTime();
            $newStartHour->setTimestamp($eventInfo[0]['start']);
            $newStartHour->setTime( (int)$params['startHour'], (int)$params['startMinutes'] );
            $newStartHour = $newStartHour->getTimestamp();

            $newEndHour = new DateTime();
            $newEndHour->setTimestamp($eventInfo[0]['end']);
            $newEndHour->setTime( (int)$params['endHour'], (int)$params['endMinutes'] );
            $newEndHour = $newEndHour->getTimestamp();

            $timeNow = time();
            
            if ( $newStartHour <= $timeNow )
                return $this->error(406, 43);
            
            if ( !(1800 + $newStartHour <= $newEndHour) )
                return $this->error(406, 44);
                            
            $eventInfo[0]['start'] = $newStartHour;
            $eventInfo[0]['end'] = $newEndHour;
            $eventInfo[0]['desc'] = $params['desc'];
            $eventInfo[0]['user_id'] = $params['userId'];
            $eventInfo[0]['success'] = FALSE;

            if ( !DbCheck::eventAvailableForUpdate($this->db, $params['roomId'], $params['eventId'], $newStartHour, $newEndHour) )
            {
                $eventInfo[0]['success'] = FALSE;
            }
            else
            {
                $arrParams['start'] = $newStartHour;
                $arrParams['end'] = $newEndHour;
                $arrParams['desc'] = $params['desc'];
                $arrParams['id'] = $params['eventId'];
                $arrParams['user_id'] = $params['userId'];
    
                $sql = 'UPDATE booker_events
                        SET start = :start,
                            end = :end,
                            `desc` = :desc,
                            user_id = :user_id
                        WHERE id = :id';
                $result = $this->db->execute($sql, $arrParams);
    
                if (!$result)
                    $eventInfo[0]['success'] = FALSE;
                    // return $this->error();

                $eventInfo[0]['success'] = TRUE;
            }
            
            return $eventInfo;
        }

        /* All events */
        if ($params['reacurring'] == 'true')
        {
            
            if ( !$allEventsInfo = $this->getEventInfoByParentId($eventInfo[0]['start'], $eventInfo[0]['event_id']) )
                return $this->error(404, 36);
                
            $eventsArr = [];
            foreach ($allEventsInfo as $value)
            {
                $newStartHour = new DateTime();
                $newStartHour->setTimestamp($value['start']);
                $newStartHour->setTime( (int)$params['startHour'], (int)$params['startMinutes'] );
                $newStartHour = $newStartHour->getTimestamp();
    
                $newEndHour = new DateTime();
                $newEndHour->setTimestamp($value['end']);
                $newEndHour->setTime( (int)$params['endHour'], (int)$params['endMinutes'] );
                $newEndHour = $newEndHour->getTimestamp();
    
                $timeNow = time();
                
                if ( $newStartHour <= $timeNow )
                    return $this->error(406, 22);
                
                if ( !(1800 + $newStartHour <= $newEndHour) )
                    return $this->error(406, 21);

                $value['start'] = $newStartHour;
                $value['end'] = $newEndHour;
                $value['desc'] = $params['desc'];
                $value['user_id'] = $params['userId'];
                $value['success'] = FALSE;

                if ( !DbCheck::eventAvailableForUpdate($this->db, $params['roomId'], $value['id'], $newStartHour, $newEndHour) )
                {
                    $value['success'] = FALSE;
                }
                else
                {
                    $arrParams['start'] = $newStartHour;
                    $arrParams['end'] = $newEndHour;
                    $arrParams['desc'] = $params['desc'];
                    $arrParams['id'] = $value['id'];
                    $arrParams['user_id'] = $params['userId'];
    
                    $sql = 'UPDATE booker_events
                            SET start = :start,
                                end = :end,
                                `desc` = :desc,
                                user_id = :user_id
                            WHERE id = :id';
                    $result = $this->db->execute($sql, $arrParams);
        
                    if (!$result)
                        $value['success'] = FALSE;
                        // return $this->error();
                    
                    $value['success'] = TRUE;
                }
                    
                $eventsArr[] = $value;
            }

            return $eventsArr;
        }

        return $this->error(406, 45);
    }

    /**
     * Delete event(s).
     * /hash(user)/id(event)/false - 1 event.
     * /hash(user)/id(event)/true - reacurring.
     * @return array
     */
    public function deleteEvents($params)
    {
        list( $hash, $eventId, $reacurring ) = explode('/', $params['params'], 3);
        
        $userId = $this->getUserIdByHash($hash);

        if ( !DbCheck::eventId($this->db, $eventId) )
            return $this->error(404, 32);
            
        if ( !DbCheck::eventOwnById($this->db, $userId, $eventId) )
        {
            if ( !DbCheck::adminRights($this->db, $hash) )
                return $this->error(406, 34);
        }

        if ( !$eventInfo = $this->getFutureEventInfoById($eventId) )
            return $this->error(404, 35);
        
        $timeLabel = $eventInfo[0]['start'];

        /* One event */
        if ($reacurring == 'false')
        {
            $sql = "DELETE FROM booker_events
                    WHERE id = :id
                    AND start >= $timeLabel
                    AND end >= $timeLabel
                    LIMIT 1";

            $result = $this->db->execute($sql, ['id' => $eventId]);

            if (!$result)
                return $this->error();

            return $eventInfo;
        }
        
        /* All events */
        if ($reacurring == 'true')
        {
            $event_id = $eventInfo[0]['event_id'];

            if ( !DbCheck::eventIdParent($this->db, $eventId, $event_id) )
                return $this->error(404, 33);

            if ( !$allEventsInfo = $this->getEventInfoByParentId($timeLabel, $event_id) )
                return $this->error(404, 36);

            $cntLimit = count($allEventsInfo);

            $sql = "DELETE FROM booker_events
                    WHERE event_id = :event_id
                    AND start >= $timeLabel
                    AND end >= $timeLabel
                    LIMIT $cntLimit";

            $result = $this->db->execute($sql, ['event_id' => $event_id]);

            if (!$result)
                return $this->error();

            return $allEventsInfo;
        }
        
        return $this->error(406, 37);
    }
    
    /**
     * Get seconds on week(s).
     *
     * @param integer $cnt
     * @return integer
     */
    private function getTimeWeek($cnt = 1)
    {
        return 60 * 60 * 24 * 7 * $cnt;
    }
    
    /**
     * Get user ID by hash from table.
     *
     * @param string $hash
     * @return bool
     * @return integer
     */
    private function getUserIdByHash($hash)
    {
        $sql = 'SELECT id FROM booker_users WHERE hash = :hash';
        $result = $this->db->execute($sql, ['hash' => $hash]);
        
        if (!$result)
            return FALSE;
        
        return $result[0]['id'];
    }
    
    /**
     * Get array of event by ID.
     *
     * @param integer $id
     * @return bool
     * @return array
     */
    private function getFutureEventInfoById($id)
    {
        $timestampNow = time();
        
        $sql = "SELECT booker_events.start,
                       booker_events.event_id,
                       booker_events.end,
                       booker_events.created,
                       booker_events.`desc`
                FROM booker_events
                WHERE booker_events.id = :id
                AND start > $timestampNow
                AND end > $timestampNow
                LIMIT 1";
                
        $result = $this->db->execute($sql, ['id' => $id]);
        
        if (!$result)
            return FALSE;
        
        return $result;
    }
    
    /**
     * Get array of events by event_id.
     *
     * @param integer $timeLabel
     * @param integer $event_id
     * @return bool
     * @return array
     */
    private function getEventInfoByParentId($timeLabel, $event_id)
    {
        $sql = "SELECT booker_events.start,
                       booker_events.event_id,
                       booker_events.id,
                       booker_events.end,
                       booker_events.created,
                       booker_events.`desc`
                FROM booker_events
                WHERE booker_events.event_id = :event_id
                AND start >= $timeLabel
                AND end >= $timeLabel";
                
        $result = $this->db->execute($sql, ['event_id' => $event_id]);
        
        if (!$result)
            return FALSE;
        
        return $result;
    }
}

if (PHP_SAPI !== 'cli')
{
    try
    {
        $api = new Rest( new UserEvents );
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
