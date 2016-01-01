<?php
/* 
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\traits;
use vistart\Helpers\Ip;
use Yii;
/**
 * 
 * @property string|integer|null $ipAddress
 * @proeprty array $ipRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait IPTrait
{    
    /**
     * @var string REQUIRED. Determine whether enableing the IP attributes and 
     * features. If you set this property to false, the ipAttribute* and 
     * ipTypeAttribute will be ignored, and getIpAddress & setIpAddress will be 
     * skipped.
     * @since 1.1
     */
    public $enableIP = true;

    /**
     * @var string The attribute name that will receive the beginning 32 bits of
     * IPv6, or 0 of IPv4. The default value is 'ip_1'.
     */
    public $ipAttribute1 = 'ip_1';

    /**
     * @var string The attribute name that will receive the 33 - 64 bits of IPv6,
     * or 0 of IPv4. The default value is 'ip_2'.
     */
    public $ipAttribute2 = 'ip_2';

    /**
     * @var string The attribute name that will receive the 65 - 96 bits of IPv6,
     * or 0 of IPv4. The default value is 'ip_3'.
     */
    public $ipAttribute3 = 'ip_3';

    /**
     * @var string The attribute name that will receive the last 32 bits of IPv6,
     * or IPv4. The default value is 'ip_4'.
     */
    public $ipAttribute4 = 'ip_4';

    /**
     * @var string The attribute name that will receive the type of IP address.
     * The default value is 'ip_type'.
     */
    public $ipTypeAttribute = 'ip_type';
    
    /**
     * This method is ONLY used for being triggered by event. DON'T call it 
     * directly.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onInitIpAddress($event)
    {
        $sender = $event->sender;
        if ($sender->enableIP) {
            $sender->ipAddress = Yii::$app->request->userIP;
        }
    }
        
    /**
     * Return the IP address.
     * The IP address is converted from ipAttribute*.
     * If you disable($this->enableIP = false) the IP feature, this method will
     * return null, or return the significantly IP address(Colon hexadecimal of
     * IPv6 or Dotted decimal of IPv4).
     * @return string|integer|null
     */
    public function getIpAddress()
    {
        if (!$this->enableIP){
            return null;
        }
        $ipTypeAttribute = $this->ipTypeAttribute;
        $ipAttribute1 = $this->ipAttribute1;
        $ipAttribute2 = $this->ipAttribute2;
        $ipAttribute3 = $this->ipAttribute3;
        $ipAttribute4 = $this->ipAttribute4;
        if ($this->$ipTypeAttribute == Ip::IPv4){
            return Ip::long2ip($this->$ipAttribute4);
        }
        if ($this->$ipTypeAttribute == Ip::IPv6){
            return Ip::LongtoIPv6(Ip::populateIPv6([
                $this->$ipAttribute1, 
                $this->$ipAttribute2, 
                $this->$ipAttribute3, 
                $this->$ipAttribute4
            ]));
        }
        return $this->$ipAttribute4;
    }
    
    /**
     * Convert the IP address to integer, and store it(them) to ipAttribute*.
     * If you disable($this->enableIP = false) the IP feature, this method will
     * be skipped(return null).
     * @param string $ip the significantly IP address.
     * @return string|integer|null Integer when succeeded to convert.
     */
    public function setIpAddress($ip)
    {
        if (!$this->enableIP){
            return null;
        }
        $ipTypeAttribute = $this->ipTypeAttribute;
        $ipAttribute1 = $this->ipAttribute1;
        $ipAttribute2 = $this->ipAttribute2;
        $ipAttribute3 = $this->ipAttribute3;
        $ipAttribute4 = $this->ipAttribute4;
        $this->$ipTypeAttribute = Ip::judgeIPtype($ip);
        if ($this->$ipTypeAttribute == Ip::IPv4){
            $this->$ipAttribute1 = 0;
            $this->$ipAttribute2 = 0;
            $this->$ipAttribute3 = 0;
            $this->$ipAttribute4 = Ip::ip2long($ip);
            return Ip::IPv4;
        }
        if ($this->$ipTypeAttribute == Ip::IPv6){
            $ips = Ip::splitIPv6(Ip::IPv6toLong($ip));
            $this->$ipAttribute1 = bindec($ips[0]);
            $this->$ipAttribute2 = bindec($ips[1]);
            $this->$ipAttribute3 = bindec($ips[2]);
            $this->$ipAttribute4 = bindec($ips[3]);
            return Ip::IPv6;
        }
        return $ip;
    }
    
    public function getIpRules()
    {
        if ($this->enableIP) {
            return [
                [[$this->ipAttribute1, 
                  $this->ipAttribute2, 
                  $this->ipAttribute3, 
                  $this->ipAttribute4], 
                 'integer', 'min' => 0
                ],
                [[$this->ipTypeAttribute], 'in', 'range' => [Ip::IPv4, Ip::IPv6]]
            ];
        }
        return [];
    }
}