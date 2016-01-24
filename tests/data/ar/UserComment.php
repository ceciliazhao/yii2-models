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

namespace vistart\Models\tests\data\ar;

/**
 * Description of UserComment
 *
 * @author vistart <i@vistart.name>
 */
class UserComment extends \vistart\Models\models\BaseBlameableModel
{

    public $parentAttribute = 'parent_guid';

    public static function tableName()
    {
        return '{{%user_comment}}';
    }
}
