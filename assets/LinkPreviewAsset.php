<?php

namespace humhub\modules\sytvillinkpreview\assets;

use yii\web\AssetBundle;

class LinkPreviewAsset extends AssetBundle
{
    public $sourcePath = '@sytvillinkpreview/resources';
    public $css = ['css/linkpreview.css'];
    public $js = ['js/linkpreview.js'];
    public $depends = [
        'humhub\\assets\\AppAsset',
    ];
}
