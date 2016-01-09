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

namespace vistart\Models\models;

use Yii;

/**
 * MultipleDomainsManager is used to process multiple domains web application.
 * This class does not apply to the basic template, otherwise you know consequenses.
 * @author vistart <i@vistart.name>
 */
class MultipleDomainsManager {

    /**
     * @var string the base domain.
     */
    public $baseDomain = '';

    /**
     * <Sub Domain Name> => [
     *     'component' => <URL Manager Component Configuration Array>,
     *     'schema' => 'http'(default) or 'https',
     * ]
     * @var array 
     */
    public $subDomains = [];

    /**
     * 
     * @param string $subdomain
     * @return \yii\web\UrlManager
     */
    public function get($subdomain) {
        if (!isset($this->subDomains[$subdomain])) {
            return null;
        }
        $subDomainConfig = $this->subDomains[$subdomain];
        if (!isset($subDomainConfig['component']['hostInfo'])) {
            if (!isset($subDomainConfig['schema'])) {
                $subDomainConfig['schema'] = 'http';
            }
            $subDomainConfig['component']['hostInfo'] = $subDomainConfig['schema'] . "://" . ($subdomain === '' ? '' : "$subdomain.") . $this->baseDomain;
        }
        return Yii::createObject($subDomainConfig['component']);
    }

}
