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
 * @property boolean $isFavorite
 */
trait UserRelationTrait {

    public $otherGuidAttribute = 'other_guid';
    public $groupGuidAttribute = 'group_guid';
    public $groupClass = '';
    public $relationTypeAttribute = 'type';
    public $remarkAttribute = 'remark';
    public $favoriteAttribute = 'favorite';
    public static $relationTypeNormal = 0x00;
    public static $relationTypeSuspend = 0x01;
    public static $relationTypeBanned = 0x10;
    public $relationTypes = [
        0x00 => 'Normal',
        0x01 => 'Suspend',
        0x10 => 'Banned',
    ];

    public function getIsFavorite() {
        $favoriteAttribute = $this->favoriteAttribute;
        return $this->$favoriteAttribute > 0;
    }

    public function setIsFavorite($fav) {
        $favoriteAttribute = $this->favoriteAttribute;
        $this->$favoriteAttribute = ($fav ? 1 : 0);
    }

    public function getOtherGuidRules() {
        $rules = [
            [[$this->otherGuidAttribute, $this->relationTypeAttribute], 'required'],
            [[$this->otherGuidAttribute], 'string', 'max' => 36],
            [[$this->relationTypeAttribute], 'boolean'],
            [[$this->relationTypeAttribute], 'default', 'value' => static::$relationTypeNormal],
        ];
        if ($this->groupGuidAttribute) {
            $rules = array_merge([
                [[$this->groupGuidAttribute], 'required'],
                [[$this->groupGuidAttribute], 'string', 'max' => 36],
                    ], $rules);
        }
        if ($this->remarkAttribute) {
            $rules = array_merge([
                [[$this->remarkAttribute], 'string',],
                [[$this->remarkAttribute], 'default', 'value' => ''],
                    ], $rules);
        }
        if ($this->favoriteAttribute) {
            $rules = array_merge([
                [[$this->favoriteAttribute], 'boolean'],
                [[$this->favoriteAttribute], 'default', 'value' => 1],
                    ], $rules);
        }
        return $rules;
    }

    abstract public function getGroup();
}
