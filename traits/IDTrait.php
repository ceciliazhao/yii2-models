<?php
/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
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
trait IDTrait
{
    /**
     * @var string OPTIONAL.The attribute that will receive the IDentifier No.
     * You can set this property to false if you don't use this feature.
     * @since 1.1
     */
    public $idAttribute = false;
    
    public $idAttributeTypeString = 0;
    public $idAttributeTypeInteger = 1;
    
    /**
     * @var integer 
     * @since 2.0
     */
    public $idAttributeType = 0;
    
    /**
     * @var string 
     * @since 2.0
     */
    public $idAttributePrefix = '';
    
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
     * This method is ONLY used for being triggered by event. DON'T call it 
     * directly.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onInitIdAttribute($event)
    {
        $sender = $event->sender;
        if ($sender->idAttribute !== false && 
            is_string($sender->idAttribute) && 
            is_int($sender->idAttributeLength) && 
            $sender->idAttributeLength > 0)
        {
            $idAttribute = $sender->idAttribute;
            $sender->$idAttribute = $sender->generateId();
        }
    }
        
    /**
     * Generate the ID. You can override this method to implement your own 
     * generation algorithm.
     * @return string the generated ID.
     */
    public function generateId()
    {
        if ($this->idAttributeType == $this->idAttributeTypeInteger)
        {
            return Number::randomNumber($this->idAttributePrefix, 
                                        $this->idAttributeLength);
        }
        if ($this->idAttributeType == $this->idAttributeTypeString)
        {
            return $this->idAttributePrefix . 
                   Yii::$app->security->generateRandomString(
                       $this->idAttributeLength - strlen($this->idAttributePrefix
                       )
                   );
        }
        return false;
    }
    
    /**
     * 
     * @return array
     */
    public function getIdRules()
    {
        if ($this->idAttribute == false)
        {
            return [];
        }
        if ($this->idAttributeSafe)
        {
            return [
                [[$this->idAttribute], 'safe'],
            ];
        }
        if (is_string($this->idAttribute) && 
            is_int($this->idAttributeLength) && 
            $this->idAttributeLength > 0)
        {
            $rules = [
                [[$this->idAttribute], 'required'],
                [[$this->idAttribute], 'string', 
                    'length' => $this->idAttributeLength,],
                [[$this->idAttribute], 'unique'],
            ];
            if ($this->idAttributeType === $this->idAttributeTypeInteger)
            {
                $rules[] = [
                    [$this->idAttribute], 'integer',
                ];
            }
            return $rules;
        }
        return [];
    }
}
