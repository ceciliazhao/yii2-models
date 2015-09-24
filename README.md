Models for Yii 2
================

This extension provide a BaseEntityModel.

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
    public $guidAttribute = 'Your GUID Attribute Name';

    public $createdAtAttribute = 'Your createdAt Attribute Name';

    public $updatedAtAttribute = 'Your updatedAt Attribute Name';
    
    public $ipAttribute1 = 'Your IP_1 Attribute Name';
    public $ipAttribute2 = 'Your IP_2 Attribute Name';
    public $ipAttribute3 = 'Your IP_3 Attribute Name';
    public $ipAttribute4 = 'Your IP_4 Attribute Name';
    public $ipTypeAttribute = 'Your IP type Attribute Name';
    
    public function rules()
    {
        $rules = ['Your Rules'];
        return array_merge(parent::rules(), $rules);
    }

    public function behaviors()
    {
        $behaviors = ['Your Behaviors'];
        return array_merge(parent::behaviors(), $behaviors);
    }

    protected function initDefaultValues()
    {
        'Initialize attributes...'
        parent::initDefaultValues();
    }
...
}
```

Contact Us
----------

[![Join the chat at https://gitter.im/vistart/yii2-models](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/vistart/yii2-models?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

If you have any problems or good ideas about yii2-models, please discuss there, or send an email to i@vistart.name. Thank you!

If you want to send an email with your issues, please briefly introduce yourself first, for instance including your title and github homepage.
