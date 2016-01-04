<?php

/*
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\tests\data\ar;

/**
 * Description of ActiveRecord
 *
 * @author vistart <i@vistart.name>
 * @since 2.0
 */
class User extends \vistart\Models\models\BaseUserModel {
    
    public static $db;

    public static function getDb() {
        return self::$db;
    }

}
