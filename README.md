Models for Yii 2
================

This extension provide a BaseEntityModel for relational database ActiveRecord, a BaseBlameableEntityModel that inherited from BaseEntityModel for some classes needed to be blamed.

[![Latest Stable Version](https://poser.pugx.org/vistart/yii2-models/v/stable)](https://packagist.org/packages/vistart/yii2-models)
[![Total Downloads](https://poser.pugx.org/vistart/yii2-models/downloads)](https://packagist.org/packages/vistart/yii2-models)
[![Monthly Downloads](https://poser.pugx.org/vistart/yii2-models/d/monthly)](https://packagist.org/packages/vistart/yii2-models)
[![Daily Downloads](https://poser.pugx.org/vistart/yii2-models/d/daily)](https://packagist.org/packages/vistart/yii2-models)
[![Latest Unstable Version](https://poser.pugx.org/vistart/yii2-models/v/unstable)](https://packagist.org/packages/vistart/yii2-models)
[![License](https://poser.pugx.org/vistart/yii2-models/license)](https://packagist.org/packages/vistart/yii2-models)

The BaseEntityModel would help you to fill the following attribute(s) automatically:
* GUID
* ID
* createdAt and updatedAt
* IP address

Please see detailed usage in the comments of code.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vistart/yii2-models "*"
```

or add

```
"vistart/yii2-models": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
use vistart\Models\BaseEntityModel;

class Example extends BaseEntityModel
{
    /**
     * @var string The GUID attribute name. This property is REQUIRED, because 
     * we believe that this property corresponds to the primary key in the 
     * database.
     */
    public $guidAttribute = 'Your GUID Attribute Name';

    /**
     * @var string The ID attribute name. This property is OPTIONAL, if you do
     * not want to use this feature, please set it to false.
     */
    public $idAttribute = false;
    
    /**
     * @var integer The length of id attribute value. This property is OPTIONAL.
     * If you set $idAttribute to false, this property will be ignored.
     */
    public $idAttributeLength = 4;

    /**
     * @var string the attribute that will receive datetime value
     * Set this property to false if you do not want to record the creation time.
     */
    public $createdAtAttribute = 'Your createdAt Attribute Name';

    /**
     * @var string the attribute that will receive datetime value.
     * Set this property to false if you do not want to record the update time.
     */
    public $updatedAtAttribute = 'Your updatedAt Attribute Name';
    
    /**
     * @var boolean Determine to enable the IP address feature. The default
     * value of this property is true, if you do not want to use this feature, 
     * please set it to false, then the five subsequence properties and 
     * correspoding validation rules will be ignored.
     */
    public $enableIP = true.

    /**
     * @var string The attribute name that will receive the beginning 32 bits of
     * IPv6, or 0 of IPv4.
     */
    public $ipAttribute1 = 'Your IP_1 Attribute Name';

    /**
     * @var string The attribute name that will receive the 33 - 64 bits of IPv6,
     * or 0 of IPv4.
     */
    public $ipAttribute2 = 'Your IP_2 Attribute Name';

    /**
     * @var string The attribute name that will receive the 65 - 96 bits of IPv6,
     * or 0 of IPv4.
     */
    public $ipAttribute3 = 'Your IP_3 Attribute Name';

    /**
     * @var string The attribute name that will receive the last 32 bits of IPv6,
     * or IPv4.
     */
    public $ipAttribute4 = 'Your IP_4 Attribute Name';

    /**
     * @var string The attribute name that will receive the type of IP address.
     */
    public $ipTypeAttribute = 'Your IP type Attribute Name';
    
    /**
     * This method has been implemented in BaseEntityModel, if you want to add 
     * validation rule(s), please implement your own rules array first, and 
     * merge the parent's and your's, then return it, DO NOT override it directly.
     * @return array Validation rules.
     */
    public function rules()
    {
        $rules = ['Your Rules'];
        return array_merge(parent::rules(), $rules);
    }

    /**
     * This method has been implemented in BaseEntityModel, if you want to add
     * behaviors, please implement your own behaviors array first, and merge
     * the parent's and your's, then return it, DO NOT override it directly.
     * @return array Behaviors.
     */
    public function behaviors()
    {
        $behaviors = ['Your Behaviors'];
        return array_merge(parent::behaviors(), $behaviors);
    }

    /**
     * This method has been implemented in BaseEntityModel, please DO NOT 
     * override it.
     * This method does not return a value.
     */
    protected function initDefaultValues()
    {
        'Initialize attributes...'
        parent::initDefaultValues();
    }
...
}
```

If you have some ActiveRecord need to be blamed, maybe you are interested in BaseBlameableEntityModel.

BaseBlameableEntityModel automatically fills the specified attributes with the current user's GUID. The basic usage is following:
~~~php
/**
 * @property string $comment
 */
class CommentExample extends BaseBlameableEntityModel
{
    /**
     * @var string the attribute that will receive current user's GUID value.
     * Set this property to false if you do not want to record the creator ID.
     */
    public $createdByAttribute = 'user_uuid';

    /**
     * @var string the attribute that will receive current user's GUID value.
     * Set this property to false if you do not want to record the updater ID.
     */
    public $updatedByAttribute = 'user_uuid';

    /**
     * @var string the attribute that specify the name of id of 
     * Yii::$app->user->identity. Or same as $createByAttribute.
     */
    public $identityUuidAttribute = 'user_uuid';

    public $enableIP = false;

    public static function tableName()
    {
        return <table_name>;
    }

    // the usage of rules(), behaviors, and initDefaultValues() are same as 
    // those of BaseEntityModel.
    public function rules()
    {
        $rules = [
            [['comment'], 'required'],
            [['comment'], 'string', 'max' => 140], 
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function behaviors()
    {
        $behaviors = <Your Behaviors>;
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels()
    {
        return [
            ...
        ];
    }
}
~~~

Well, when you are signed-in, you can save a new `Example` instance:
~~~php
$example = new CommentExample();
$example->comment = 'New Comment.';
$example->save();
// Then it will automatically fill the GUID, Creator's GUID, create time &
// update time, but except IP address, because you disabled the IP features.
~~~

or update an existing one:
~~~php
$example = Example::find()
                  ->where([$this->createdByAttribute => $user_uuid])
                  ->one();
if ($example)
{
    $example->comment = 'Updated Comment.';
    $example->save();
}
// Then it will automatically update the update time of current item.
~~~

Before you use it, you must make sure the Example model that corresponds a database table include the following attributes:
* guid
* user_uuid
* create_time
* update_time
* comment

Contact Us
----------

[![Join the chat at https://gitter.im/vistart/yii2-models](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/vistart/yii2-models?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

If you have any problems or good ideas about yii2-models, please discuss there, or send an email to i@vistart.name. Thank you!

If you want to send an email with your issues, please briefly introduce yourself first, for instance including your title and github homepage.
