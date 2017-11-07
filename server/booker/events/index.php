<?php
require_once(__DIR__ . "/../../config.php");

use lib\db\BookerDb as Db;
use lib\db\helpers\BookerDbCheck as DbCheck;
use lib\traits\Error;

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
     * Get all events in the range for room.
     * id/month/year - input.
     * @return array
     */
    public function getEventsByParams($params)
    {
        list($sqlParams['id'],
             $arrParams['month'],
             $arrParams['year'],
        ) = explode('/', $params['params'], 4);

        if ( !DbCheck::roomId($this->db, $sqlParams['id']) )
            return $this->error(406, 30);

        $dateStart = new DateTime($arrParams['year'] . '-' . $arrParams['month'] . '-1');
        //$days = cal_days_in_month(CAL_GREGORIAN, $arrParams['month'], $arrParams['year']);
        $days = date('t', $dateStart->getTimestamp());
        $dateEnd = new DateTime($arrParams['year'] . '-' . $arrParams['month'] . '-' . $days);
        $dateEnd->setTime(23, 59, 59);

        $sqlParams['start'] = $dateStart->getTimestamp();
        $sqlParams['end'] = $dateEnd->getTimestamp();

        
        $sql = 'SELECT
                booker_events.id,
                booker_events.user_id,
                booker_events.room_id,
                booker_events.`desc`,
                booker_events.start,
                booker_events.end,
                booker_events.created,
                booker_events.event_id,
                booker_users.name
                FROM booker_events
                INNER JOIN booker_rooms
                    ON booker_events.room_id = booker_rooms.id
                INNER JOIN booker_users
                    ON booker_events.user_id = booker_users.id
                WHERE booker_events.start BETWEEN :start AND :end
                AND booker_events.room_id = :id';

        $result = $this->db->execute($sql, $sqlParams);

        if (!$result)
            return $this->error();

        return $result;
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
