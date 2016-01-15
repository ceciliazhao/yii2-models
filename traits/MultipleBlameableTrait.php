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
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait MultipleBlameableTrait {

    /**
     * @var string 
     */
    public $multipleBlameableClass;

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
    public function addBlamedByGuid($blameGuid) {
        if (!is_string($this->multipleBlameableAttribute)) {
            return false;
        }
    }

    /**
     * 
     * @param type $blame
     * @return type
     */
    public function addBlamed($blame) {
        return $this->addBlamedByGuid($blame->guid);
    }

    /**
     * 
     * @param type $blameGuid
     * @return boolean
     */
    public function removeBlamedByGuid($blameGuid) {
        if (!is_string($this->multipleBlameableAttribute)) {
            return false;
        }
    }

    /**
     * 
     * @param type $blame
     * @return type
     */
    public function removeBlamed($blame) {
        return $this->removeBlamedByGuid($blame->guid);
    }

    /**
     * 
     */
    public function removeAllBlamed() {
        
    }

    /**
     * 
     */
    public function getBlamedGuids() {
        
    }

    /**
     * 
     * @param array $guids
     */
    public function setBlamedGuids($guids) {
        
    }

    /**
     * Get all the blames of record.
     * @return array all blames.
     */
    public function getAllBlames() {
        
    }

    /**
     * Get all records which without any blames.
     * @return array all non-blameds.
     */
    public function getNonBlameds() {
        
    }

}
