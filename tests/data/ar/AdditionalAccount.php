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

namespace vistart\Models\tests\data\ar;

/**
 * Description of AdditionalAccount
 *
 * @author vistart <i@vistart.name>
 */
class AdditionalAccount extends \vistart\Models\models\BaseAdditionalAccountModel {
    public static function tableName() {
        return '{{user_additional_account}}';
    }
}
