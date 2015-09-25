<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 05/02/15 - 22:00
 * 
 */

namespace Indaba\Dashboard;

use \BitPrepared\Commons\BasicEnum;

class Source extends BasicEnum {
    
    const EMAIL = 1;
    const SMS = 2;
    const TWITTER = 3;
    const TELEGRAM = 4;
    const WEB = 5;

    protected static $typeLabels = array(
        self::EMAIL => 'Email',
        self::SMS => 'SMS',
        self::TWITTER => 'Twitter',
        self::TELEGRAM => 'Telegram',
        self::WEB => 'Web'
    );

}