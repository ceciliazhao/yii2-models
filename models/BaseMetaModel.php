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

namespace vistart\Models\models;

/**
 * Description of BaseMetaModel
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseMetaModel extends BaseBlameableModel
{

    public $idAttribute = 'key';
    public $idPreassigned = true;
    public $createdAtAttribute = false;
    public $updatedAtAttribute = false;
    public $enableIP = false;
    public $contentAttribute = 'value';
    public $updatedByAttribute = false;
    public $confirmationAttribute = false;

    /**
     * Store the guid of blame.
     * @var string 
     */
    public $blameGuid = '';

    public function behaviors()
    {
        return $this->getMetaBehaviors();
    }

    /**
     * Skip all behaviors of parent class.
     * @return array
     */
    public function getMetaBehaviors()
    {
        return [];
    }

    public function init()
    {
        $this->on(static::EVENT_INIT, [$this, 'onInitBlameGuid']);
        parent::init();
    }

    /**
     * Initialize blame's guid.
     * @param \yii\base\Event $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onInitBlameGuid($event)
    {
        $sender = $event->sender;
        if (empty($sender->blameGuid)) {
            throw new \yii\base\InvalidConfigException('Empty blame guid is not allowed.');
        }
        $createdByAttribute = $sender->createdByAttribute;
        $sender->$createdByAttribute = $sender->blameGuid;
    }
}
