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
 * Description of UserEmail
 *
 * @author vistart <i@vistart.name>
 * @since 2.0
 */
class UserEmail extends \vistart\Models\models\BaseBlameableEntityModel {

    public static function tableName() {
        return '{{%user_email}}';
    }

    public $contentAttribute = 'email';
    public $contentAttributeRule = ['email'];
    public $confirmationAttribute = 'confirmed';
    public $confirmCodeAttribute = 'confirm_code';
    public $updatedByAttribute = false;
    public $enableIP = false;
    public $contentTypeAttribute = 'type';
    public $contentTypes = [
        0 => 'home',
        1 => 'work',
        255 => 'other',
    ];
    
    public static $db;

    public static function getDb() {
        return self::$db;
    }

}
