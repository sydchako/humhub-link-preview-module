<?php

use humhub\modules\content\widgets\richtext\AbstractRichText;
use humhub\modules\sytvillinkpreview\Events;

return [
    'id' => 'sytvillinkpreview',
    'class' => 'humhub\\modules\\sytvillinkpreview\\Module',
    'namespace' => 'humhub\\modules\\sytvillinkpreview',
    'events' => [
        [AbstractRichText::class, AbstractRichText::EVENT_AFTER_OUTPUT, [Events::class, 'onRichTextAfterOutput']],
    ],
];
