<?php
namespace humhub\modules\sytvillinkpreview;
use humhub\libs\ParameterEvent;
use humhub\modules\content\widgets\richtext\AbstractRichText;
use humhub\modules\sytvillinkpreview\assets\LinkPreviewAsset;
use humhub\modules\sytvillinkpreview\services\UrlExtractor;
use humhub\helpers\Html;
use yii\helpers\Url;
use Yii;
class Events
{
    public static function onRichTextAfterOutput(ParameterEvent $event):void
    {
        if(Yii::$app->request->isConsoleRequest)return;
        $sender=$event->sender;if(!$sender instanceof AbstractRichText||!empty($sender->edit))return;
        $raw=(string)($sender->text??'');if($raw===''||stripos($raw,'http')===false)return;
        $m=Yii::$app->getModule('sytvillinkpreview');$max=max(1,min(5,(int)$m->settings->get('maxPreviews',3)));
        $urls=UrlExtractor::previewableUrls($raw,$max);if(!$urls)return;
        LinkPreviewAsset::register(Yii::$app->view);$endpoint=Url::to(['/sytvillinkpreview/preview/fetch']);$refresh=Url::to(['/sytvillinkpreview/preview/refresh']);
        $wrap=''; foreach($urls as $url){$wrap.=Html::tag('div','',['class'=>'sytvil-link-preview','data-url'=>$url,'data-endpoint'=>$endpoint,'data-refresh-endpoint'=>$refresh,'aria-live'=>'polite']);}
        $event->parameters['output'].=Html::tag('div',$wrap,['class'=>'sytvil-link-previews'.((bool)$m->settings->get('mobileCompact',1)?' is-mobile-compact':'')]);
    }
}
