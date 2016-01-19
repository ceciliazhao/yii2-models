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

namespace vistart\Models\traits;

/**
 * Description of EntityQueryTrait
 *
 * @author vistart <i@vistart.name>
 */
trait EntityQueryTrait {

    public $noInitModel;

    public function initNoModel() {
        if (empty($this->noInitModel) && is_string($this->modelClass)) {
            $modelClass = $this->modelClass;
            $this->noInitModel = $modelClass::buildNoInitModel();
        }
    }

}
