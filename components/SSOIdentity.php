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

/**
 * Description of SSOIdentity
 * This component needs MultipleDomainsManager component.
 * @author vistart <i@vistart.name>
 */
class SSOIdentity extends \yii\web\User {

    public $baseDomain;
    public $ssoScope = '';
    public $loginDomain = 'login';
    public $returnUrlServerNameParam = '__returnUrlServerName';
    
    public function loginRequired($checkAjax = true)
    {
        $request = Yii::$app->getRequest();
        Yii::$app->getSession()->set($this->returnUrlServerNameParam, $request->serverName);
        Yii::$app->getSession()->cookieParams = ['domain' => $request->serverName, 'lifetime' => 0];
        
        if ($this->enableSession && (!$checkAjax || !$request->getIsAjax())) {
            $this->setReturnUrl($request->getAbsoluteUrl());
        }
        if ($this->loginUrl !== null) {
            $loginUrl = (array) $this->loginUrl;
            if ($loginUrl[0] !== Yii::$app->requestedRoute) {
                $loginUrlManager = Yii::$app->multipleDomainsManager->get($this->loginDomain);//var_dump($loginUrlManager);die();
                return Yii::$app->getResponse()->redirect($loginUrlManager->createUrl($this->loginUrl));
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Login Required'));
    }

    protected function sendIdentityCookie($identity, $duration)
    {
        $serverName = Yii::$app->getSession()->get($this->returnUrlServerNameParam, $this->ssoScope . '.' . $this->baseDomain);
        $this->identityCookie['domain'] = $serverName;
        parent::sendIdentityCookie($identity, $duration);
    }

}
