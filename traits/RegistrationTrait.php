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
/**
 * Description of RegistrationTrait
 *
 * @property array $sourceRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait RegistrationTrait
{    
    public static $EVENT_AFTER_REGISTER = "afterRegister";
    public static $EVENT_BEFORE_REGISTER = "beforeRegister";
    public static $EVENT_REGISTER_FAILED = "registerFailed";
    public static $EVENT_AFTER_DEREGISTER = "afterDeregister";
    public static $EVENT_BEFORE_DEREGISTER = "beforeDeregister";
    public static $EVENT_DEREGISTER_FAILED = "deregisterFailed";
    
    public $sourceAttribute = 'source';
    private $_sourceRules = [];
    public $sourceSelf = '0';
    
    /**
     * 
     * @param type $associateModels
     * @return \yii\db\Exception|boolean
     * @throws \yii\db\IntegrityException
     */
    public function register($associateModels = [])
    {
        if (!$this->isNewRecord){
            return false;
        }
        $this->trigger(self::EVENT_BEFORE_REGISTER);
        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            if (!$this->save())
            {
                throw new \yii\db\IntegrityException('Registration Error(s) Occured.', $this->errors);
            }
            foreach ($associateModels as $model){
                if (!$model->save()){
                    throw new \yii\db\IntegrityException('Registration Error(s) Occured.', $model->errors);
                }
            }
            $transaction->commit();
        } catch (\yii\db\Exception $ex) {
            $transaction->rollBack();
            $this->trigger(self::EVENT_REGISTER_FAILED);
            return false;
        }
        $this->trigger(self::EVENT_AFTER_REGISTER);
        return true;
    }
    
    /**
     * 
     * @return boolean
     */
    public function deregister()
    {
        $this->trigger(self::EVENT_BEFORE_DEREGISTER);
        $result = $this->delete();
        if ($result)
        {
            $this->trigger(self::EVENT_AFTER_DEREGISTER);
        } else {
            $this->trigger(self::EVENT_DEREGISTER_FAILED);
        }
        return $result;
    }
    
    /**
     * 
     * @return array
     */
    public function getSourceRules()
    {
        if (empty($this->_sourceRules))
        {
            $this->_sourceRules = [
                [[$this->sourceAttribute], 'required'],
                [[$this->sourceAttribute], 'string'],
            ];
        }
        return $this->_sourceRules;
    }
    
    /**
     * 
     * @param array $rules
     */
    public function setSourceRules($rules)
    {
        if (!empty($rules) && is_array($rules))
        {
            $this->_sourceRules = $rules;
        }
    }
    
    /**
     * 
     * @param type $event
     */
    public function onInitSourceAttribute($event)
    {
        $sender = $event->sender;
        $sourceAttribute = $sender->sourceAttribute;
        $sender->$sourceAttribute = $sender->sourceSelf;
    }
}
