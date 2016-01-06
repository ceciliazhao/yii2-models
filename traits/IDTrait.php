<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\traits;

use Yii;
use vistart\Helpers\Number;

/**
 * @property-read array $idRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait IDTrait {

    /**
     * @var string OPTIONAL.The attribute that will receive the IDentifier No.
     * You can set this property to false if you don't use this feature.
     * @since 1.1
     */
    public $idAttribute = 'id';
    public static $ID_TYPE_STRING = 0;
    public static $ID_TYPE_INTEGER = 1;
    public static $ID_TYPE_AUTO_INCREMENT = 2;

    /**
     * @var integer 
     * @since 2.0
     */
    public $idAttributeType = 0;

    /**
     * @var string The prefix of ID. When ID type is Auto Increment, this feature
     * is skipped.
     * @since 2.0
     */
    public $idAttributePrefix = '';

    /**
     * @var integer OPTIONAL. The length of id attribute value.
     * If you set $idAttribute to false or ID type to Auto Increment, this
     * property will be ignored.
     * @since 1.1
     */
    public $idAttributeLength = 4;

    /**
     * @var boolean Determine whether the ID is safe for validation.
     * @since 1.1
     */
    protected $idAttributeSafe = false;

    /**
     * Initialize the ID attribute with new generated ID.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onInitIdAttribute($event) {
        $sender = $event->sender;
        if ($sender->idAttribute !== false &&
                is_string($sender->idAttribute) &&
                is_int($sender->idAttributeLength) &&
                $sender->idAttributeLength > 0 &&
                $sender->idAttributeType != self::$ID_TYPE_AUTO_INCREMENT) {
            $idAttribute = $sender->idAttribute;
            $sender->$idAttribute = $sender->generateId();
        }
        if ($sender->idAttributeType === self::$ID_TYPE_AUTO_INCREMENT) {
            $sender->idAttributeSafe = true;
        }
    }

    /**
     * Generate the ID. You can override this method to implement your own 
     * generation algorithm.
     * @return string the generated ID.
     */
    public function generateId() {
        if ($this->idAttributeType == self::$ID_TYPE_INTEGER) {
            return Number::randomNumber($this->idAttributePrefix, $this->idAttributeLength);
        }
        if ($this->idAttributeType == self::$ID_TYPE_STRING) {
            return $this->idAttributePrefix .
                    Yii::$app->security->generateRandomString(
                            $this->idAttributeLength - strlen($this->idAttributePrefix
                            )
            );
        }
        if ($this->idAttributeType == self::$ID_TYPE_AUTO_INCREMENT) {
            return null;
        }
        return false;
    }

    /**
     * 
     * @return array
     */
    public function getIdRules() {
        if ($this->idAttribute == false) {
            return [];
        }
        if ($this->idAttributeSafe) {
            return [
                [[$this->idAttribute], 'safe'],
            ];
        }
        if (is_string($this->idAttribute) &&
                is_int($this->idAttributeLength) &&
                $this->idAttributeLength > 0) {
            $rules = [
                [[$this->idAttribute], 'required'],
                [[$this->idAttribute], 'unique'],
            ];
            if ($this->idAttributeType === self::$ID_TYPE_INTEGER) {
                $rules[] = [
                    [$this->idAttribute], 'integer',
                ];
            }
            if ($this->idAttributeType === self::$ID_TYPE_STRING) {
                $rules[] = [[$this->idAttribute], 'string',
                    'length' => $this->idAttributeLength,];
            }
            if ($this->idAttributeType === self::$ID_TYPE_AUTO_INCREMENT) {
                $rules[] = [
                    [$this->idAttribute], 'safe',
                ];
            }
            return $rules;
        }
        return [];
    }

}
