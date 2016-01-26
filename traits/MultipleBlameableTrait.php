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

use vistart\Helpers\Number;
use vistart\Models\events\MultipleBlameableEvent;
use yii\web\JsonParser;

/**
 * 一个模型的某个属性可能对应多个责任者，该 trait 用于处理此种情况。此种情况违反
 * 了关系型数据库第一范式，因此此 trait 只适用于责任者属性修改不频繁的场景，在开
 * 发时必须严格测试数据一致性，并同时考量性能。
 * 
 * Basic Principles:
 * <ol>
 * <li>when adding blame, it will check whether each of blames including to be
 * added is valid.
 * </li>
 * <li>when removing blame, as well as counting, getting or setting list of them,
 * it will also check whether each of blames is valid.
 * </li>
 * <li>By default, once blame was deleted, the guid of it is not removed from
 * list of blames immediately. It will check blame if valid when adding, removing,
 * counting, getting and setting it. You can define a blame model and attach it
 * events triggered when inserting, updating and deleting a blame, then disable
 * checking the validity of blames.
 * </li>
 * </ol>
 * Notice:
 * <ol>
 * <li>You must specify two properties: $multiBlamesClass and $multiBlamesAttribute.
 * <ul>
 * <li>$multiBlamesClass specify the class name of blame.</li>
 * <li>$multiBlamesAttribute specify the field name of blames.</li>
 * </ul>
 * </li>
 * <li>You should rename each name of following methods to be needed optionally.</li>
 * </ol>
 * @property-read array $multiBlamesAttributeRules
 * @property array $blameGuids
 * @property-read array $allBlames
 * @property-read array $nonBlameds
 * @property-read integer $blamesCount
 * 
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait MultipleBlameableTrait
{

    /**
     * @var string class name of multiple blameable class.
     */
    public $multiBlamesClass = '';

    /**
     * @var string name of multiple blameable attribute.
     */
    public $multiBlamesAttribute = 'blames';

    /**
     * @var integer the limit of blames. it should be greater than or equal 1, and
     * less than or equal 10.
     */
    public $blamesLimit = 10;

    /**
     * @var boolean determines whether blames list has been changed.
     */
    public $blamesChanged = false;

    /**
     * @var string event name.
     */
    public static $eventMultipleBlamesChanged = 'multipleBlamesChanged';

    /**
     * Get the rules associated with multiple blameable attribute.
     * @return array rules.
     */
    public function getMultipleBlameableAttributeRules()
    {
        return is_string($this->multiBlamesAttribute) ? [
            [[$this->multiBlamesAttribute], 'required'],
            [[$this->multiBlamesAttribute], 'string', 'max' => $this->blamesLimit * 39 + 1],
            [[$this->multiBlamesAttribute], 'default', 'value' => '[]'],
                ] : [];
    }

    /**
     * Add specified blame.
     * @param [multiBlamesClass]|string $blame
     * @return false|array
     */
    public function addBlame($blame)
    {
        if (!is_string($this->multiBlamesAttribute))
        {
            return false;
        }
        $blameGuid = '';
        if (is_string($blame))
        {
            $blameGuid = $blame;
        }
        if ($blame instanceof $this->multiBlamesClass)
        {
            $blameGuid = $blame->guid;
        }
        $blameGuids = $this->getBlameGuids(true);
        if (array_search($blameGuid, $blameGuids))
        {
            throw new \yii\base\InvalidParamException('the blame has existed.');
        }
        if ($this->getBlamesCount() >= $this->blamesLimit)
        {
            throw new \yii\base\InvalidCallException("the limit($this->blamesLimit) of blames has been reached.");
        }
        $blameGuids[] = $blameGuid;
        $this->setBlameGuids($blameGuids);
        return $this->getBlameGuids();
    }

    /**
     * Remove specified blame.
     * @param [multiBlamesClass] $blame
     * @return false|array all guids in json format.
     */
    public function removeBlame($blame)
    {
        if (!is_string($this->multiBlamesAttribute))
        {
            return false;
        }
        $blameGuid = '';
        if (is_string($blame))
        {
            $blameGuid = $blame;
        }
        if ($blame instanceof $this->multiBlamesClass)
        {
            $blameGuid = $blame->guid;
        }
        $blameGuids = $this->getBlameGuids(true);
        if (($key = array_search($blameGuid, $blameGuids)) !== false)
        {
            unset($blameGuids[$key]);
            $this->setBlameGuids($blameGuids);
        }
        return $this->getBlameGuids();
    }

    /**
     * Remove all blames.
     */
    public function removeAllBlames()
    {
        $this->setBlameGuids();
    }

    /**
     * Count the blames.
     * @return integer
     */
    public function getBlamesCount()
    {
        return count($this->getBlameGuids(true));
    }

    /**
     * Get the guid array of blames. it may check all guids if valid before return.
     * @param boolean $checkValid determines whether checking the blame is valid.
     * @return array all guids in json format.
     */
    public function getBlameGuids($checkValid = false)
    {
        $multiBlamesAttribute = $this->multiBlamesAttribute;
        if ($multiBlamesAttribute === false)
        {
            return [];
        }
        $jsonParser = new JsonParser();
        $guids = $jsonParser->parse($this->$multiBlamesAttribute, true);
        if ($checkValid)
        {
            $guids = $this->unsetInvalidBlames($guids);
        }
        return $guids;
    }

    /**
     * Event triggered when blames list changed.
     * @param \vistart\Models\events\MultipleBlameableEvent $event
     */
    public function onBlamesChanged($event)
    {
        $sender = $event->sender;
        $sender->blamesChanged = $event->blamesChanged;
    }

    /**
     * Remove invalid blame guid from provided guid array.
     * @param array $guids guid array of blames.
     * @return array guid array of blames unset invalid.
     */
    protected function unsetInvalidBlames($guids)
    {
        $checkedGuids = Number::unsetInvalidUuids($guids);
        $multiBlamesClass = $this->multiBlamesClass;
        foreach ($checkedGuids as $key => $guid)
        {
            $blame = $multiBlamesClass::findOne($guid);
            if (!$blame)
            {
                unset($checkedGuids[$key]);
            }
        }
        $diff = array_diff($guids, $checkedGuids);
        $this->trigger(static::$eventMultipleBlamesChanged, new MultipleBlameableEvent(['blamesChanged' => !empty($diff)]));
        return $checkedGuids;
    }

    /**
     * Set the guid array of blames, it may check all guids if valid.
     * @param array $guids guid array of blames.
     * @param boolean $checkValid determines whether checking the blame is valid.
     * @return false|array all guids.
     */
    public function setBlameGuids($guids = [], $checkValid = true)
    {
        if (!is_array($guids) || $this->multiBlamesAttribute === false)
        {
            return null;
        }
        if ($checkValid)
        {
            $guids = $this->unsetInvalidBlames($guids);
        }
        $multiBlamesAttribute = $this->multiBlamesAttribute;
        $this->$multiBlamesAttribute = json_encode(array_values($guids));
        return $guids;
    }

    /**
     * 
     * @param string $blameGuid
     * @return [multiBlamesClass]
     */
    public static function getBlame($blameGuid)
    {
        $self = static::buildNoInitModel();
        if (empty($self->multiBlamesClass) || !is_string($self->multiBlamesClass) || $self->multiBlamesAttribute === false)
        {
            return null;
        }
        $mbClass = $self->multiBlamesClass;
        return $mbClass::findOne($blameGuid);
    }

    /**
     * Get all ones to be blamed by `$blame`.
     * @param [multiBlamesClass] $blame
     * @return array
     */
    public function getBlameds($blame)
    {
        $blameds = static::getBlame($blame->guid);
        if (empty($blameds))
        {
            return null;
        }
        $createdByAttribute = $this->createdByAttribute;
        return static::find()->where([$createdByAttribute => $this->$createdByAttribute])
                        ->andWhere(['like', $this->multiBlamesAttribute, $blame->guid])->all();
    }

    /**
     * Get all the blames of record.
     * @return array all blames.
     */
    public function getAllBlames()
    {
        if (empty($this->multiBlamesClass) ||
                !is_string($this->multiBlamesClass) ||
                $this->multiBlamesAttribute === false)
        {
            return null;
        }
        $multiBlamesClass = $this->multiBlamesClass;
        $createdByAttribute = $this->createdByAttribute;
        return $multiBlamesClass::findAll([$createdByAttribute => $this->$createdByAttribute]);
    }

    /**
     * Get all records which without any blames.
     * @return array all non-blameds.
     */
    public function getNonBlameds()
    {
        $createdByAttribute = $this->createdByAttribute;
        return static::find()->where([$createdByAttribute => $this->$createdByAttribute, $this->multiBlamesAttribute => static::getEmptyBlamesJson()])->all();
    }

    /**
     * Initialize blames limit.
     * @param \yii\base\Event $event
     */
    public function onInitBlamesLimit($event)
    {
        $sender = $event->sender;
        if (!is_int($sender->blamesLimit) || $sender->blamesLimit < 1 || $sender->blamesLimit > 10)
        {
            $sender->blamesLimit = 10;
        }
    }

    /**
     * Get the json of empty blames array.
     * @return string
     */
    public static function getEmptyBlamesJson()
    {
        return json_encode([]);
    }
}
