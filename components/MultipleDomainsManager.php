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
 * MultipleDomainsManager is used to process multiple domains web application.
 * This class does not apply to the basic template, otherwise you know consequenses.
 * @property-read \yii\web\UrlManager $current
 * @since 2.0
 * @author vistart <i@vistart.name>
 */
class MultipleDomainsManager extends \yii\base\Component
{

    /**
     * @var string the base domain.
     */
    public $baseDomain = '';

    /**
     * <Sub Domain Name> => [
     *     'component' => <URL Manager Component Configuration Array>,
     *     'schema' => 'http'(default) or 'https',
     * ]
     * For example:
     * ```php
     * $baseDomain = 'example.com';
     * $subDomains = [
     *    '' => [
     *         'component' => [
     *              // `class` could be ignored as it is `vistart\Models\components\MultipleDomainsUrlManager`.
     *             'class' => 'yii\web\UrlManager',
     *             'enablePrettyUrl' => true,
     *             'showScriptName' => false,
     *             'suffix' => '.html',
     *             // the other properties...
     *         ],
     *         'schema' => 'http', // `schema` could be ignored as it is 'http'.
     *     ],
     *     'my' => [
     *         'component' => [
     *             'enablePrettyUrl' => true,
     *             'showScriptName' => false,
     *             'suffix' => '.html',
     *             'rules' => [
     *                 'posts/<year:\d{4}>/<category>' => 'post/index',
     *                 'posts' => 'post/index',
     *                 'post/<id:\d+>' => 'post/view',
     *             ],
     *         ],
     *         'schema' => 'https',
     *     ],
     *     'sso' => [
     *         'component' => [
     *             'enablePrettyUrl' => true,
     *             'showScriptName' => false,
     *             'rules' => [
     *                 '' => 'sso/login',
     *                 'logout' => 'sso/logout',
     *             ],
     *         ],
     *         'schema' => 'https',
     *     ],
     * ];
     * ```
     * and you can configure the `config/web.php` (basic template) or `config/main.php` (advanced template)
     * `components` section to add an element named `multipleDomainsManager`:
     * ```php
     * $config = [
     *     ...
     *     'components' => [
     *         ...
     *         'multipleDomainsManager' => [
     *             'baseDomain' => 'example.com',
     *             'subDomains' => [
     *                 'my' => [
     *                     'component' => require(<the path of 'my' web application UrlManager component configuration file>),
     *                 ],
     *                 ...
     *             ],
     *         ],
     *         ...
     *     ],
     * ];
     * ```
     * @var array array of sub-domains.
     */
    public $subDomains = [];

    /**
     * @var string Current sub-domain name. If current sub-domain does not exist
     * in `$this->subDomains`, return `Yii::$app->urlManager` instead.
     */
    public $currentDomain = '';

    /**
     * Get UrlManager component of specified sub-domain application.
     * @param string $subdomain
     * @return \yii\web\UrlManager
     */
    public function get($subdomain = '')
    {
        if (!isset($this->subDomains[$subdomain])) {
            return null;
        }
        $subDomainConfig = $this->subDomains[$subdomain];
        if (!isset($subDomainConfig['component'])) {
            return null;
        }
        if (!isset($subDomainConfig['component']['class'])) {
            $subDomainConfig['component']['class'] = MultipleDomainsUrlManager::className();
        }
        if (!isset($subDomainConfig['component']['hostInfo'])) {
            if (!isset($subDomainConfig['schema'])) {
                $subDomainConfig['schema'] = 'http';
            }
            $subDomainConfig['component']['hostInfo'] = $subDomainConfig['schema'] . // 'http' or 'https'
                "://" . // delimiter
                ($subdomain === '' ? '' : "$subdomain.") . // attach subdomain
                $this->baseDomain;                                               // base domain
        }
        return Yii::createObject($subDomainConfig['component']);
    }

    /**
     * Get URL Manager of current domain web application.
     * @return \yii\web\UrlManager
     */
    public function getCurrent()
    {
        return $this->get($this->currentDomain) ? : Yii::$app->urlManager;
    }
}
