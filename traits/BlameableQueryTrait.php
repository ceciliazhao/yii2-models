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
 * This trait is used for building blameable query class for blameable model,
 * which would be attached three conditions.
 * For example:
 * ```php
 * class BlameableQuery {
 *     use BlameableQueryTrait;
 * }
 * ```
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait BlameableQueryTrait
{
    use QueryTrait;

    /**
     * Specify confirmation.
     * @param boolean $isConfirmed
     * @return $this
     */
    public function confirmed($isConfirmed = true)
    {
        $model = $this->noInitModel;
        if (!is_string($model->confirmationAttribute)) {
            return $this;
        }
        return $this->andWhere([$model->confirmationAttribute => $isConfirmed]);
    }

    /**
     * Specify content.
     * @param mixed $content
     * @param false|string $like false, 'like', 'or like', 'not like', 'or not like'.
     * @return $this
     */
    public function content($content, $like = false)
    {
        $model = $this->noInitModel;
        return $this->likeCondition($content, $model->contentAttribute, $like);
    }

    /**
     * Specify parent.
     * @param array|string $guid parent guid or array of them. non-parent if
     * empty. If you don't want to specify parent, please do not access this
     * method.
     * @return $this
     */
    public function parentGuid($guid)
    {
        $model = $this->noInitModel;
        if (!is_string($model->parentAttribute)) {
            return $this;
        }
        if (empty($guid)) {
            return $this->andWhere([$model->parentAttribute => '']);
        }
        return $this->andWhere([$model->parentAttribute => $guid]);
    }
}
