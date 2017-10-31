<?php

function autoloadClass ($class) 
{
    $path = __DIR__ . "/rest/" . $class . ".php";
    $path = str_replace("\\", '/', $path);

    if (file_exists($path))
    {
        require_once $path;

        if (class_exists($class))
            return true;
    }

    return false;
}

spl_autoload_register('autoloadClass');

/* MySql Home */
// define('M_HOST','localhost');
// define('M_USER','root');
// define('M_PASS','');

// if (PHP_SAPI !== 'cli')
//     define('M_DB','booker');
// else
// {
//     define('M_DB','test_booker');
//     define('ADMIN_HASH','f82468c5e5e449cdce6f4595aef3cd78');
// }

// define('ERROR_CODE_INFORMATION', 'http://booker/server/ErrorCodeInformation.html');

/* MySql Class */
define('M_HOST','localhost');
define('M_USER','user10');
define('M_PASS','tuser10');
define('M_DB','user10');
define('ERROR_CODE_INFORMATION', 'http://192.168.0.15/~user10/booker/server/ErrorCodeInformation.html');

/* SERVICE */
define('DEFAULT_TYPE', '.json');

/* ERRORs */
define('DELIMITER', ' | ');
define('ERROR_HEADER_CODE', 'Error\'s Code: ');
define('ERROR_HTML_TEXT',
       '%STATUS_CODE% %ERROR_DESCRIPTION%' .DELIMITER. '%CODE_NUMBER%<br>
       <a href="' .ERROR_CODE_INFORMATION. '">
           View Error Code Information here.
       </a>'
);