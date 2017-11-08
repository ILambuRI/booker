<?php

namespace lib\db\helpers;

class BookerDbCheck
{
    
    /** 
     * Checking user admin access by hash.
     * @return bool
     */
    static function adminRights($pdo, $hash)
    {
        $sql = 'SELECT admin FROM booker_users WHERE hash = :hash';
        $result = $pdo->execute($sql, ['hash' => $hash]);

        if (!$result or $result[0]['admin'] == 0)
            return FALSE;

        return TRUE;
    }
    
    /** 
     * Check user id in the table users.
     * @return bool
     */
    static function userId($pdo, $id)
    {
        $sql = 'SELECT id FROM booker_users WHERE id = :id';
        $result = $pdo->execute($sql, ['id' => $id]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
    
    /** 
     * Check id in the table rooms.
     * @return bool
     */
    static function roomId($pdo, $id)
    {
        $sql = 'SELECT id FROM booker_rooms WHERE id = :id';
        $result = $pdo->execute($sql, ['id' => $id]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
    
    /** 
     * Check event id in the table events.
     * @return bool
     */
    static function eventId($pdo, $id)
    {
        $sql = 'SELECT id FROM booker_events WHERE id = :id';
        $result = $pdo->execute($sql, ['id' => $id]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
    
    /** 
     * Check event_id in the table events.
     * @return bool
     */
    static function eventIdParent($pdo, $id, $event_id)
    {
        $sql = 'SELECT id FROM booker_events
                WHERE id = :id
                AND event_id = :event_id';
        $result = $pdo->execute($sql, ['id' => $id, 'event_id' => $event_id]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
    
    /** 
     * Check event own by id in the table events.
     * @return bool
     */
    static function eventOwnById($pdo, $userId, $eventId)
    {
        $sql = 'SELECT booker_events.id
                FROM booker_events
                WHERE booker_events.user_id = :userId
                AND booker_events.id = :eventId';
        $result = $pdo->execute($sql, ['userId' => $userId, 'eventId' => $eventId]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }

    /** 
     * Check name in the table.
     * @return bool
     */
    static function name($pdo, $name)
    {
        $sql = 'SELECT name FROM booker_users WHERE name = :name';
        $result = $pdo->execute($sql, ['name' => $name]);
        
        if (!$result)
            return FALSE;

        return TRUE;
	}
	
    /** 
     * Check email in the table.
     * @return bool
     */
    static function email($pdo, $email)
    {
        $sql = 'SELECT email FROM booker_users WHERE email = :email';
        $result = $pdo->execute($sql, ['email' => $email]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
	
    /** 
     * Check hash in the table.
     * @return bool
     */
    static function hash($pdo, $hash)
    {
        $sql = 'SELECT hash FROM booker_users WHERE hash = :hash';
        $result = $pdo->execute($sql, ['hash' => $hash]);
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
	
    /** 
     * Check user password in the table.
     * @return bool
     */
    static function password($pdo, $name, $password)
    {
        $sql = 'SELECT name FROM booker_users
				WHERE name = :name 
				AND password = :password';
        $result = $pdo->execute( $sql, ['name' => $name, 'password' => $password] );
        
        if (!$result)
            return FALSE;

        return TRUE;
    }
	
    /** 
     * Check user password in the table.
     * @return bool
     */
    static function eventAvailable($pdo, $roomId, $timeStart, $timeEnd)
    {
        $sql = "SELECT booker_events.id
                FROM booker_events
                WHERE (room_id = $roomId AND booker_events.start <= $timeStart AND $timeStart <= booker_events.end)
                OR (room_id = $roomId AND booker_events.start <= $timeEnd AND $timeEnd <= booker_events.end)
                OR (room_id = $roomId AND $timeStart <= booker_events.start AND booker_events.end <= $timeEnd)";
        $result = $pdo->execute($sql);
        
        if (!$result)
            return TRUE;

        return FALSE;
    }
	
    /** 
     * Check user password in the table.
     * @return bool
     */
    static function eventAvailableForUpdate($pdo, $id, $timeStart, $timeEnd)
    {
        $sql = "SELECT booker_events.id
                FROM booker_events
                WHERE ( (booker_events.start <= $timeStart AND $timeStart <= booker_events.end)
                OR (booker_events.start <= $timeEnd AND $timeEnd <= booker_events.end)
                OR ($timeStart <= booker_events.start AND booker_events.end <= $timeEnd) )
                AND booker_events.id <> $id";
        $result = $pdo->execute($sql);
        
        if (!$result)
            return TRUE;

        return FALSE;
    }
}