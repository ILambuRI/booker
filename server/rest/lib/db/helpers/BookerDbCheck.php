<?php

namespace lib\db\helpers;

class BookerDbCheck
{
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
}