<?php

/*
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models;

use Yii;
use yii\behaviors\TimestampBehavior;
use vistart\Helpers\Number;
use vistart\Helpers\Ip;
use yii\db\ActiveRecord;
/**
 * This Base Entity Model is used for being extended to the other Active Record, 
 * which include `createdAt`, `updatedAt`, `ipAddress`, `ipType`. `GUID` 
 * attributes, and so on. The `createdAt` and `updatedAt` attributes' meaning 
 * are same as those of TimestampBehavior.
 * The `ipAddress` store the standard IP Address, which is of IPv4 or IPv6.
 * You should set the IP Address before using it. the setIpAddress() method
 * will judge the IP Address type automatically.
 * The `GUID` is used for the primary key of this model.
 * 
 * You should extend this class, for your own ActiveRecord class(es). Then you
 * need to specify the following attributes' name:
 * * guidAttribute
 * * createdAtAttribute
 * * updatedAtAttribute
 * * ipAttribute1
 * * ipAttribute2
 * * ipAttribute3
 * * ipAttribute4
 * * ipTypeAttribute
 * 
 * The above attributes' name also have the default name.
 * 
 * @property string $ipAddress IP Address. You should get before setting it.
 * 
 * @author vistart <i@vistart.name>
 */
abstract class BaseEntityModel extends ActiveRecord
{
    /**
     * @var string REQUIRED. The attribute that will receive the GUID value.
     */
    public $guidAttribute = 'guid';
    
    /**
     * @var string OPTIONAL.The attribute that will receive the IDentifier No.
     * You can set this property to false if you don't use this feature.
     * @since 1.1
     */
    public $idAttribute = false;
    
    /**
     * @var integer OPTIONAL. The length of id attribute value.
     * If you set $idAttribute to false, this property will be ignored.
     * @since 1.1
     */
    public $idAttributeLength = 4;
    
    /**
     * @var boolean Determine whether the ID is safe for validation.
     * @since 1.1
     */
    protected $idAttributeSafe = false;
    
    /**
     * @var string the attribute that will receive datetime value
     * Set this property to false if you do not want to record the creation time.
     */
    public $createdAtAttribute = 'create_time';
    
    /**
     * @var string the attribute that will receive datetime value.
     * Set this property to false if you do not want to record the update time.
     */
    public $updatedAtAttribute = 'update_time';
    
    /**
     * @var string REQUIRED. Determine whether enableing the IP attributes and 
     * features. If you set this property to false, the ipAttribute* and 
     * ipTypeAttribute will be ignored, and getIpAddress & setIpAddress will be 
     * skipped.
     * @since 1.1
     */
    public $enableIP = true;

    /**
     * @var string The attribute name that will receive the beginning 32 bits of
     * IPv6, or 0 of IPv4. The default value is 'ip_1'.
     */
    public $ipAttribute1 = 'ip_1';

    /**
     * @var string The attribute name that will receive the 33 - 64 bits of IPv6,
     * or 0 of IPv4. The default value is 'ip_2'.
     */
    public $ipAttribute2 = 'ip_2';

    /**
     * @var string The attribute name that will receive the 65 - 96 bits of IPv6,
     * or 0 of IPv4. The default value is 'ip_3'.
     */
    public $ipAttribute3 = 'ip_3';

    /**
     * @var string The attribute name that will receive the last 32 bits of IPv6,
     * or IPv4. The default value is 'ip_4'.
     */
    public $ipAttribute4 = 'ip_4';

    /**
     * @var string The attribute name that will receive the type of IP address.
     * The default value is 'ip_type'.
     */
    public $ipTypeAttribute = 'ip_type';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->isNewRecord){
            $this->on(self::EVENT_INIT, [$this, 'onSetGuidAttribute']);
            $this->on(self::EVENT_INIT, [$this, 'onSetIdAttribute']);
            $this->on(self::EVENT_INIT, [$this, 'onSetIpAddress']);
            $this->initDefaultValues();
        }
        parent::init();
    }
    
    /**
     * Initialize the default value of all attributes.
     * You should override this method to specify the above Attribute Name
     * attributes, and call the parent's method in the end of your own.
     * This method does not return anything.
     */
    protected function initDefaultValues()
    {
    }
    
    /**
     * This method is ONLY used for being triggered by event. DON'T call it 
     * directly.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onSetGuidAttribute($event)
    {
        $sender = $event->sender;
        $guidAttribute = $sender->guidAttribute;
        $sender->$guidAttribute = self::GenerateUuid();
    }
    
    /**
     * This method is ONLY used for being triggered by event. DON'T call it 
     * directly.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onSetIdAttribute($event)
    {
        $sender = $event->sender;
        if ($sender->idAttribute !== false && 
            is_string($sender->idAttribute) && 
            is_int($sender->idAttributeLength) && 
            $sender->idAttributeLength > 0)
        {
            $idAttribute = $sender->idAttribute;
            $sender->$idAttribute = self::GenerateId($sender->idAttributeLength);
        }
    }
    
    /**
     * This method is ONLY used for being triggered by event. DON'T call it 
     * directly.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onSetIpAddress($event)
    {
        $sender = $event->sender;
        if ($sender->enableIP) {
            $sender->ipAddress = Yii::$app->request->userIP;
        }
    }
    
    /**
     * Generate GUID. It will check if the generated GUID existed in database
     * table, if existed, it will regenerate one.
     * @return string the generated GUID.
     */
    public static function GenerateUuid()
    {
        do {
            $uuid = Number::guid();
        } while (self::CheckUuidExists($uuid));
        return $uuid;
    }
    
    /**
     * Check if the $uuid existed in current database table.
     * @param string $uuid the GUID to be checked.
     * @return boolean Whether the $guid exists or not.
     */
    public static function CheckUuidExists($uuid)
    {
        return (self::findOne($uuid) !== null);
    }
    
    /**
     * Generate the ID. You can override this method to implement your own 
     * generation algorithm.
     * @param integer $length the generated ID's length.
     * @return string the generated ID.
     */
    public static function GenerateId($length)
    {
        return Yii::$app->security->generateRandomString($length);
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors() 
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => $this->createdAtAttribute,
                'updatedAtAttribute' => $this->updatedAtAttribute,
                'value' => [$this, 'onUpdateCurrentDatetime'],
            ]
        ];
    }
    
    /**
     * Get the current date & time in format of "Y-m-d H:i:s".
     * This method is used for being triggered. DO NOT call it directly.
     * @param \yii\base\Event $event
     * @return string Date & Time.
     * @since 1.1
     */
    public function onUpdateCurrentDatetime($event)
    {
        return self::getCurrentDatetime();
    }
    
    /**
     * Get the current date & time in format of "Y-m-d H:i:s".
     * You can override this method to customize the return value.
     * @return string Date & Time.
     * @since 1.1
     */
    public static function getCurrentDatetime()
    {
        return date('Y-m-d H:i:s');
    }
    
    const VALIDATOR_REQUIRED = 'required';
    const VALIDATOR_UNIQUE = 'unique';
    const VALIDATOR_STRING = 'string';
    const VALIDATOR_INTEGER = 'integer';
    const VALIDATOR_RANGE = 'in';
    const VALIDATOR_SAFE = 'safe';
    
    /**
     * Returns the validation rules for attributes which are in this model.
     * We don't recommend you to override this method, You should merge the 
     * validation rules in your own rules() method.
     * @return array validation rules
     * @see scenarios()
     * @since 1.1
     */
    public function rules()
    {
        $rules = [];
        // The GUID attribute is required.
        $requiredRule = [
            [$this->guidAttribute], self::VALIDATOR_REQUIRED,
        ];
        $rules[] = $requiredRule;
        
        // Check and attach the ID attribute rule(s).
        if (!$this->idAttributeSafe && 
            $this->idAttribute !== false && 
            is_string($this->idAttribute) && 
            is_int($this->idAttributeLength) && 
            $this->idAttributeLength > 0)
        {
            $requiredRule[0][] = $this->idAttribute;
            $rules[] = $requiredRule;
            
            $idAttributeRule = [
                [$this->idAttribute], 
                self::VALIDATOR_STRING, 
                'max' => $this->idAttributeLength, 'min' => $this->idAttributeLength,
            ];
            $rules[] = $idAttributeRule;
        }
        
        // The GUID is unique among the whole records of current table.
        $uniqueRules = [
            [[$this->guidAttribute], self::VALIDATOR_UNIQUE,]
         ];
        $rules = array_merge($rules, $uniqueRules);
        
        $guidRule = [
            [$this->guidAttribute], self::VALIDATOR_STRING, 'max' => 36,
        ];
        $rules[] = $guidRule;
        
        if ($this->enableIP){
            $ipAttributeRule = [
                [$this->ipAttribute1, 
                 $this->ipAttribute2, 
                 $this->ipAttribute3, 
                 $this->ipAttribute4], 
                self::VALIDATOR_INTEGER,
            ];
            $ipTypeAttributeRule = [
                [$this->ipTypeAttribute], self::VALIDATOR_RANGE, 'range' => [Ip::IPv4, Ip::IPv6]
            ];
            $rules[] = $ipAttributeRule;
            $rules[] = $ipTypeAttributeRule;
        }
        $createdAndUpdatedAtAttributeRule = [
            [$this->createdAtAttribute, $this->updatedAtAttribute], self::VALIDATOR_SAFE,
        ];
        $rules[] = $createdAndUpdatedAtAttributeRule;
        return $rules;
    }
        
    /**
     * Return the IP address.
     * The IP address is converted from ipAttribute*.
     * If you disable($this->enableIP = false) the IP feature, this method will
     * return null, or return the significantly IP address(Colon hexadecimal of
     * IPv6 or Dotted decimal of IPv4).
     * @return string|integer|null
     */
    public function getIpAddress()
    {
        if (!$this->enableIP){
            return null;
        }
        $ipTypeAttribute = $this->ipTypeAttribute;
        $ipAttribute1 = $this->ipAttribute1;
        $ipAttribute2 = $this->ipAttribute2;
        $ipAttribute3 = $this->ipAttribute3;
        $ipAttribute4 = $this->ipAttribute4;
        if ($this->$ipTypeAttribute == Ip::IPv4){
            return Ip::long2ip($this->$ipAttribute4);
        }
        if ($this->$ipTypeAttribute == Ip::IPv6){
            return Ip::LongtoIPv6(Ip::populateIPv6([
                $this->$ipAttribute1, 
                $this->$ipAttribute2, 
                $this->$ipAttribute3, 
                $this->$ipAttribute4
            ]));
        }
        return $this->$ipAttribute4;
    }
    
    /**
     * Convert the IP address to integer, and store it(them) to ipAttribute*.
     * If you disable($this->enableIP = false) the IP feature, this method will
     * be skipped(return null).
     * @param string $ip the significantly IP address.
     * @return string|integer|null Integer when succeeded to convert.
     */
    public function setIpAddress($ip)
    {
        if (!$this->enableIP){
            return null;
        }
        $ipTypeAttribute = $this->ipTypeAttribute;
        $ipAttribute1 = $this->ipAttribute1;
        $ipAttribute2 = $this->ipAttribute2;
        $ipAttribute3 = $this->ipAttribute3;
        $ipAttribute4 = $this->ipAttribute4;
        $this->$ipTypeAttribute = Ip::judgeIPtype($ip);
        if ($this->$ipTypeAttribute == Ip::IPv4){
            $this->$ipAttribute1 = 0;
            $this->$ipAttribute2 = 0;
            $this->$ipAttribute3 = 0;
            $this->$ipAttribute4 = Ip::ip2long($ip);
            return Ip::IPv4;
        }
        if ($this->$ipTypeAttribute == Ip::IPv6){
            $ips = Ip::splitIPv6(Ip::IPv6toLong($ip));
            $this->$ipAttribute1 = bindec($ips[0]);
            $this->$ipAttribute2 = bindec($ips[1]);
            $this->$ipAttribute3 = bindec($ips[2]);
            $this->$ipAttribute4 = bindec($ips[3]);
            return Ip::IPv6;
        }
        return $ip;
    }
}
