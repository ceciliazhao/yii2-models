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

namespace vistart\Models\components;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Description of SSOIdentity
 * This component needs MultiDomainsManager component.
 * Usage:
 * config/web.php (basic template) or config/main.php (advanced template):
 * ```php
 * $config = [
 *     ...
 *     'components' => [
 *         ...
 *         'multiDomainsManager' => [
 *              'baseDomain' => <Base Domain>,
 *         ],
 *         'user' => [
 *             'class' => 'vistart\Models\components\SSOIdentity',
 *             'enableAutoLogin' => true,
 *             'identityClass' => <User Identity Class>,
 *             'identityCookie' => [
 *                 'name' => '_identity',
 *                 'httpOnly' => true,
 *                 'domain' => '.' . <Base Domain>,    // same as Multi-Domains Manager's `baseDomain` property.
 *             ],
 *         ],
 *         'session' => [
 *             ...
 *             'cookieParams' => [
 *                 'domain' => '.' . <Base Domain>,    // same as Multi-Domains Manager's `baseDomain` property.
 *                 'lifetime' => 0,
 *             ],
 *             ...
 *         ],
 *         ...
 *     ],
 * ];
 * ```
 * @since 2.0
 * @author vistart <i@vistart.name>
 */
class SSOIdentity extends \yii\web\User
{

    public $ssoDomain = 'sso';
    public $loginUrl = ['sso/login'];
    public $multiDomainsManagerId = 'multiDomainsManager';

    public function loginRequired($checkAjax = true)
    {
        $request = Yii::$app->getRequest();

        if ($this->enableSession && (!$checkAjax || !$request->getIsAjax())) {
            $this->setReturnUrl($request->getAbsoluteUrl());
        }
        if ($this->loginUrl !== null) {
            $loginUrl = (array) $this->loginUrl;
            if ($loginUrl[0] !== Yii::$app->requestedRoute) {
                $ssoUrlManager = $this->getMultiDomainsManager()->get($this->ssoDomain);
                return Yii::$app->getResponse()->redirect($ssoUrlManager->createAbsoluteUrl($this->loginUrl));
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Login Required'));
    }

    protected function getMultiDomainsManager()
    {
        if (!empty($this->multiDomainsManagerId) && is_string($this->multiDomainsManagerId)) {
            $mdId = $this->multiDomainsManagerId;
            return Yii::$app->$mdId;
        }
        return Yii::$app->multiDomainsManager;
    }
}
