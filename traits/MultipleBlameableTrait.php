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
     * @var integer 
     */
    public $blamedLimited = 10;

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
        $jsonParser = new \yii\web\JsonParser();
        $guids = $jsonParser->parse($this->$multipleBlameableAttribute, true);
        if ($checkValid) {
            $checkedGuids = $this->unsetInvalidBlames($guids);
            $diff = array_diff($guids, $checkedGuids);
            if (!empty($diff)) {
                $guids = $this->setBlameGuids($checkedGuids, false);
            }
        }
        return $guids;
    }

    /**
     * Remove invalid blame guid from provided guid array.
     * @param array $guids guid array of blames.
     * @return array guid array of blames unset invalid.
     */
    protected function unsetInvalidBlames($guids) {
        $guids = \vistart\Helpers\Number::unsetInvalidUuids($guids);
        $multipleBlameableClass = $this->multipleBlameableClass;
        foreach ($guids as $key => $guid) {
            $blame = $multipleBlameableClass::findOne($guid);
            if (!$blame) {
                unset($guids[$key]);
            }
        }
        return $guids;
    }

    /**
     * Set the guid array of blames, it may check all guids if valid.
     * @param array $guids guid array of blames.
     * @param boolean $checkValid determines whether checking the blame is valid.
     * @return null|string all guids valid in json format.
     */
    public function setBlameGuids($guids = [], $checkValid = true) {
        if (!is_array($guids) || $this->multipleBlameableAttribute === false) {
            return null;
        }
        if ($checkValid) {
            $guids = $this->unsetInvalidBlames($guids);
        }
        $multipleBlameableAttribute = $this->multipleBlameableAttribute;
        return $this->$multipleBlameableAttribute = json_encode(array_values($guids));
    }
    
    /**
     * 
     * @param string $blameGuid
     * @return [multipleBlameableClass]
     */
    public function getBlame($blameGuid) {
        if (empty($this->multipleBlameableClass) || !is_string($this->multipleBlameableClass) || $this->multipleBlameableAttribute === false) {
            return null;
        }
        $mbClass = $this->multipleBlameableClass;
        return $mbClass::findOne($blameGuid);
    }
    
    /**
     * 
     * @param [multipleBlameableClass] $blame
     * @return array
     */
    public function getBlameds($blame) {
        $blameds = $this->getBlame($blame->guid);
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
