<?php

namespace humhub\modules\sytvillinkpreview;

use humhub\components\Module as BaseModule;
use Yii;

class Module extends BaseModule
{
    public $version = '2.0.0';

    public function getConfigUrl()
    {
        return Yii::$app->urlManager->createUrl(['/sytvillinkpreview/admin']);
    }

    public function disable()
    {
        parent::disable();
    }
}
