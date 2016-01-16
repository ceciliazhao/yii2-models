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
 * Notice:
 * <ol>
 * <li>You must specify two properties: $multipleBlameableClass and $multipleBlameableAttribute.
 * <ul>
 * <li>$multipleBlameableClass specify the class name of blame.</li>
 * <li>$multipleBlameableAttribute specify the field name of blames.</li>
 * </ul>
 * </li>
 * <li>You should rename each name of following methods optionally.</li>
 * </ol>
 * @property-read array $multipleBlameableAttributeRules
 * @property array $blameGuids
 * @property-read array $allBlames
 * @property-read array $nonBlameds
 * 
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait MultipleBlameableTrait {

    /**
     * @var string 
     */
    public $multipleBlameableClass = '';

    /**
     * @var string 
     */
    public $multipleBlameableAttribute = 'blames';

    /**
     * @var integer the limit of blames. it should be greater than or equal 1, and
     * less than or equal 10.
     */
    public $blamedLimited = 10;

    /**
     * @var boolean 
     */
    public $blamesChanged = false;

    /**
     * @var string event name.
     */
    public static $eventMultipleBlamesChanged = 'multipleBlamesChanged';

    /**
     * 
     * @return array
     */
    public function getMultipleBlameableAttributeRules() {
        return is_string($this->multipleBlameableAttribute) ? [
            [[$this->multipleBlameableAttribute], 'required'],
            [[$this->multipleBlameableAttribute], 'string', 'max' => $this->blamedLimited * 37 + 1],
            [[$this->multipleBlameableAttribute], 'default', 'value' => '[]'],
                ] : [];
    }

    /**
     * Add specified blame.
     * @param string $blameGuid
     * @return false|array blames after adding $blameGuid, or false if disable this feature.
     */
    public function addBlameByGuid($blameGuid) {
        if (!is_string($this->multipleBlameableAttribute)) {
            return false;
        }
        $blameGuids = $this->getBlameGuids(true);
        if (array_search($blameGuid, $blameGuids) === false) {
            $blameGuids[] = $blameGuid;
            $this->setBlameGuids($blameGuids);
        }
        return $this->getBlameGuids();
    }

    /**
     * Add specified blame.
     * @param [multipleBlameableClass] $blame
     * @return false|array
     */
    public function addBlame($blame) {
        return $this->addBlameByGuid($blame->guid);
    }

    /**
     * Remove specified blame.
     * @param string $blameGuid
     * @return false|array
     */
    public function removeBlameByGuid($blameGuid) {
        if (!is_string($this->multipleBlameableAttribute)) {
            return false;
        }
        $blameGuids = $this->getBlameGuids(true);
        if (($key = array_search($blameGuid, $blameGuids)) !== false) {
            unset($blameGuids[$key]);
            $this->setBlameGuids($blameGuids);
        }
        return $this->getBlameGuids();
    }

    /**
     * Remove specified blame.
     * @param [multipleBlameableClass] $blame
     * @return false|array all guids in json format.
     */
    public function removeBlame($blame) {
        return $this->removeBlamedByGuid($blame->guid);
    }

    /**
     * Remove all blames.
     */
    public function removeAllBlames() {
        $this->setBlameGuids();
    }

    /**
     * Get the guid array of blames. it may check all guids if valid before return.
     * @param boolean $checkValid determines whether checking the blame is valid.
     * @return array all guids in json format.
     */
    public function getBlameGuids($checkValid = false) {
        $multipleBlameableAttribute = $this->multipleBlameableAttribute;
        if ($multipleBlameableAttribute === false) {
            return [];
        }
        $jsonParser = new JsonParser();
        $guids = $jsonParser->parse($this->$multipleBlameableAttribute, true);
        if ($checkValid) {
            $guids = $this->unsetInvalidBlames($guids);
        }
        return $guids;
    }

    /**
     * 
     * @param \vistart\Models\events\MultipleBlameableEvent $event
     */
    public function onBlamesChanged($event) {
        $sender = $event->sender;
        $sender->blamesChanged = $event->blamesChanged;
    }

    /**
     * Remove invalid blame guid from provided guid array.
     * @param array $guids guid array of blames.
     * @return array guid array of blames unset invalid.
     */
    protected function unsetInvalidBlames($guids) {
        $checkedGuids = Number::unsetInvalidUuids($guids);
        $multipleBlameableClass = $this->multipleBlameableClass;
        foreach ($checkedGuids as $key => $guid) {
            $blame = $multipleBlameableClass::findOne($guid);
            if (!$blame) {
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
    public function setBlameGuids($guids = [], $checkValid = true) {
        if (!is_array($guids) || $this->multipleBlameableAttribute === false) {
            return null;
        }
        if ($checkValid) {
            $guids = $this->unsetInvalidBlames($guids);
        }
        $multipleBlameableAttribute = $this->multipleBlameableAttribute;
        $this->$multipleBlameableAttribute = json_encode(array_values($guids));
        return $guids;
    }

    /**
     * 
     * @param string $blameGuid
     * @return [multipleBlameableClass]
     */
    public static function getBlame($blameGuid) {
        $self = static::buildNoInitModel();
        if (empty($self->multipleBlameableClass) || !is_string($self->multipleBlameableClass) || $self->multipleBlameableAttribute === false) {
            return null;
        }
        $mbClass = $self->multipleBlameableClass;
        return $mbClass::findOne($blameGuid);
    }

    /**
     * Get all ones to be blamed by `$blame`.
     * @param [multipleBlameableClass] $blame
     * @return array
     */
    public function getBlameds($blame) {
        $blameds = static::getBlame($blame->guid);
        if (empty($blameds)) {
            return null;
        }
        $createdByAttribute = $this->createdByAttribute;
        return static::find()->where([$createdByAttribute => $this->$createdByAttribute])
                        ->andWhere(['like', $this->multipleBlameableAttribute, $blame->guid])->all();
    }

    /**
     * Get all the blames of record.
     * @return array all blames.
     */
    public function getAllBlames() {
        if (empty($this->multipleBlameableClass) ||
                !is_string($this->multipleBlameableClass) ||
                $this->multipleBlameableAttribute === false) {
            return null;
        }
        $multipleBlameableClass = $this->multipleBlameableClass;
        $createdByAttribute = $this->createdByAttribute;
        return $multipleBlameableClass::findAll([$createdByAttribute => $this->$createdByAttribute]);
    }

    /**
     * Get all records which without any blames.
     * @return array all non-blameds.
     */
    public function getNonBlameds() {
        $createdByAttribute = $this->createdByAttribute;
        return static::find()->where([$createdByAttribute => $this->$createdByAttribute, $this->multipleBlameableAttribute => json_encode([])])->all();
    }

}
