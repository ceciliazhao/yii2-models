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
 * 一个模型的某列可能对应多个责任者，该 trait 用于处理此种情况。此种情况违反了关系型数据库第一范式。
 * 此 trait 需要 PHP 版本大于等于 5.5.
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
    public $multipleBlameableAttribute = 'owners';

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
     * 
     * @param type $blameGuid
     * @return boolean
     */
    public function addBlameByGuid($blameGuid) {
        if (!is_string($this->multipleBlameableAttribute)) {
            return false;
        }
        $blameGuids = $this->getBlameGuids(true);
        if (array_search($blameGuid, $blameGuids) === false) {
            $blameGuids[] = $guid;
            $this->setBlameGuids($blameGuids);
        }
        return $this->getBlameGuids();
    }

    /**
     * 
     * @param type $blame
     * @return type
     */
    public function addBlame($blame) {
        return $this->addBlamedByGuid($blame->guid);
    }

    /**
     * 
     * @param type $blameGuid
     * @return boolean
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
     * 
     * @param type $blame
     * @return type
     */
    public function removeBlame($blame) {
        return $this->removeBlamedByGuid($blame->guid);
    }

    /**
     * 
     */
    public function removeAllBlames() {
        $this->setBlameGuids();
    }

    /**
     * 
     * @param boolean $checkValid
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
            if (!empty(array_diff($guids, $checkedGuids))) {
                $guids = $this->setBlameGuids($checkedGuids, false);
            }
        }
        return $guids;
    }
    
    /**
     * Remove invalid group guid from provided guid array.
     * @param array $guids
     * @return array
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
     * 
     * @param type $guids
     * @param boolean $checkValid
     * @return type
     */
    public function setBlameGuids($guids = [], $checkValid = true) {
        if (!is_array($guids) || $this->multipleBlameableAttribute === false) {
            return null;
        }
        if ($checkValid) {
            $guids = $this->unsetInvalidUuids($guids);
        }
        $multipleBlameableAttribute = $this->multipleBlameableAttribute;
        return $this->$multipleBlameableAttribute = json_encode(array_values($guids));
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
        return static::find()->where([$createdByAttribute => $this->$createdByAttribute, $this->groupsAttribute => json_encode([])])->all();
    }

}
