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

    public $confirmationAttribute = 'confirmed';
    public $confirmCodeAttribute = 'confirm_code';
    public $contentTypeAttribute = 'type';

    const TYPE_HOME = 0x00;
    const TYPE_WORK = 0x01;
    const TYPE_OTHER = 0xff;

    public $contentTypes = [
        self::TYPE_HOME => 'home',
        self::TYPE_WORK => 'work',
        self::TYPE_OTHER => 'other',
    ];
    public $updatedByAttribute = false;
    public $contentAttribute = 'email';
    public $contentAttributeRule = ['email', 'message' => 'Please input valid emaill address.', 'allowName' => true];
    public $enableIP = false;
    /*
    public static $db;

    public static function getDb() {
        return self::$db;
    }
*/
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'email_uuid' => Yii::t('app', 'Email Uuid'),
            'user_uuid' => Yii::t('app', 'User Uuid'),
            'email_id' => Yii::t('app', 'Email ID'),
            'email' => Yii::t('app', 'Email'),
            'create_time' => Yii::t('app', 'Create Time'),
            'update_time' => Yii::t('app', 'Update Time'),
            'confirmed' => Yii::t('app', 'Confirmed'),
            'confirm_time' => Yii::t('app', 'Confirm Time'),
        ];
    }

}
