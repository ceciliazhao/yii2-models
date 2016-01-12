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
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait UserRelationGroupTrait {

    /**
     * @return \yii\db\Query
     */
    abstract public function getMembers();
}
