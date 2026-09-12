<?php
namespace humhub\modules\sytvillinkpreview\models;
use yii\base\Model;
use Yii;
class SettingsForm extends Model
{
    public int $cacheDays = 7;
    public int $maxPreviews = 3;
    public int $domainRateLimit = 20;
    public int $cacheImages = 1;
    public int $mobileCompact = 1;
    public string $allowedDomains = '';
    public string $blockedDomains = '';
    public string $noImageDomains = '';
    public string $domainTtls = '';
    public function rules(){return [
        [['cacheDays'],'integer','min'=>1,'max'=>30],
        [['maxPreviews'],'integer','min'=>1,'max'=>5],
        [['domainRateLimit'],'integer','min'=>1,'max'=>120],
        [['cacheImages','mobileCompact'],'boolean'],
        [['allowedDomains','blockedDomains','noImageDomains','domainTtls'],'string'],
    ];}
    public function attributeLabels(){return [
        'cacheDays'=>'Default preview cache lifetime (days)','maxPreviews'=>'Maximum previews per post/comment',
        'domainRateLimit'=>'Per-domain fetch limit per hour','cacheImages'=>'Cache preview images locally',
        'mobileCompact'=>'Use compact cards on phones','allowedDomains'=>'Allowed domains (optional, one per line)',
        'blockedDomains'=>'Blocked domains (one per line)','noImageDomains'=>'Domains where images are disabled (one per line)',
        'domainTtls'=>'Per-domain cache lifetime, e.g. example.com=2 (days)',
    ];}
    public function loadSettings():void{$s=Yii::$app->getModule('sytvillinkpreview')->settings; foreach(['cacheDays'=>7,'maxPreviews'=>3,'domainRateLimit'=>20,'cacheImages'=>1,'mobileCompact'=>1,'allowedDomains'=>'','blockedDomains'=>'','noImageDomains'=>'','domainTtls'=>''] as $k=>$d){$this->$k=is_int($d)?(int)$s->get($k,$d):(string)$s->get($k,$d);}}
    public function saveSettings():void{$s=Yii::$app->getModule('sytvillinkpreview')->settings; foreach(['cacheDays','maxPreviews','domainRateLimit','cacheImages','mobileCompact','allowedDomains','blockedDomains','noImageDomains','domainTtls'] as $k){$s->set($k,$this->$k);}}
}
