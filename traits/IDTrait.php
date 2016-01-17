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
 * Entity features concerning ID.
 * @property-read array $idRules
 * @property mixed $id
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
    public static $idTypeString = 0;
    public static $idTypeInteger = 1;
    public static $idTypeAutoIncrement = 2;

    /**
     * @var integer 
     * @since 2.0
     */
    public $idAttributeType = 0;

    /**
     * @var boolean 
     */
    public $idPreassigned = false;

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

    public function getId() {
        $idAttribute = $this->idAttribute;
        if (is_string($idAttribute)) {
            return $this->$idAttribute;
        }
        return null;
    }

    public function setId($id) {
        $idAttribute = $this->idAttribute;
        if (is_string($idAttribute)) {
            $this->$idAttribute = $id;
        }
    }

    /**
     * Initialize the ID attribute with new generated ID.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onInitIdAttribute($event) {
        $sender = $event->sender;
        if ($sender->idPreassigned || $sender->idAttributeType === self::$idTypeAutoIncrement) {
            $sender->idAttributeSafe = true;
            return;
        }
        if ($sender->idAttribute !== false &&
                is_string($sender->idAttribute) &&
                is_int($sender->idAttributeLength) &&
                $sender->idAttributeLength > 0 &&
                $sender->idAttributeType != self::$idTypeAutoIncrement) {
            $idAttribute = $sender->idAttribute;
            $sender->$idAttribute = $sender->generateId();
        }
    }

    /**
     * Generate the ID. You can override this method to implement your own 
     * generation algorithm.
     * @return string the generated ID.
     */
    public function generateId() {
        if ($this->idAttributeType == self::$idTypeInteger) {
            do {
                $result = Number::randomNumber($this->idAttributePrefix, $this->idAttributeLength);
            } while ($this->checkIdExists((int)$result));
            return $result;
        }
        if ($this->idAttributeType == self::$idTypeString) {
            return $this->idAttributePrefix .
                    Yii::$app->security->generateRandomString(
                            $this->idAttributeLength - strlen($this->idAttributePrefix
                            )
            );
        }
        if ($this->idAttributeType == self::$idTypeAutoIncrement) {
            return null;
        }
        return false;
    }

    /**
     * Check if $id existed.
     * @param mixed $id
     * @return boolean
     */
    public function checkIdExists($id) {
        if ($id == null) {
            return false;
        }
        return (static::findOne([$this->idAttribute => $id]) !== null);
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
            if ($this->idAttributeType === self::$idTypeInteger) {
                $rules[] = [
                    [$this->idAttribute], 'number', 'integerOnly' => true
                ];
            }
            if ($this->idAttributeType === self::$idTypeString) {
                $rules[] = [[$this->idAttribute], 'string',
                    'length' => $this->idAttributeLength,];
            }
            if ($this->idAttributeType === self::$idTypeAutoIncrement) {
                $rules[] = [
                    [$this->idAttribute], 'safe',
                ];
            }
            return $rules;
        }
        return [];
    }

}
