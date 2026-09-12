<?php
namespace humhub\modules\sytvillinkpreview\services;
use Yii;
class DomainPolicy
{
    private static function lines(string $key):array{
        $raw=(string)Yii::$app->getModule('sytvillinkpreview')->settings->get($key,'');
        return array_values(array_filter(array_map(static fn($v)=>strtolower(trim($v)),preg_split('/\R+/',$raw)?:[])));
    }
    public static function host(string $url):string{return strtolower((string)(parse_url($url,PHP_URL_HOST)?:''));}
    public static function matches(string $host,string $rule):bool{$rule=ltrim(strtolower(trim($rule)),'.');return $host===$rule||str_ends_with($host,'.'.$rule);}
    public static function isAllowed(string $url):bool{
        $host=self::host($url); if($host==='')return false;
        foreach(self::lines('blockedDomains') as $r)if(self::matches($host,$r))return false;
        $allow=self::lines('allowedDomains'); if(!$allow)return true;
        foreach($allow as $r)if(self::matches($host,$r))return true; return false;
    }
    public static function imagesAllowed(string $url):bool{$host=self::host($url);foreach(self::lines('noImageDomains') as $r)if(self::matches($host,$r))return false;return true;}
    public static function ttl(string $url):int{
        $host=self::host($url);$raw=(string)Yii::$app->getModule('sytvillinkpreview')->settings->get('domainTtls','');
        foreach(preg_split('/\R+/',$raw)?:[] as $line){if(!str_contains($line,'='))continue;[$d,$days]=array_map('trim',explode('=',$line,2));if(self::matches($host,$d)&&is_numeric($days))return max(1,min(30,(int)$days))*86400;}
        return max(1,min(30,(int)Yii::$app->getModule('sytvillinkpreview')->settings->get('cacheDays',7)))*86400;
    }
    public static function canonicalForCache(string $url):string{
        $parts=parse_url(trim($url)); if(!is_array($parts)||empty($parts['scheme'])||empty($parts['host']))return trim($url);
        unset($parts['fragment']); if(isset($parts['query'])){parse_str($parts['query'],$q); foreach(array_keys($q) as $k){$l=strtolower($k);if(str_starts_with($l,'utm_')||in_array($l,['fbclid','gclid','mc_cid','mc_eid'],true))unset($q[$k]);}$parts['query']=http_build_query($q);if($parts['query']==='')unset($parts['query']);}
        $u=strtolower($parts['scheme']).'://'.strtolower($parts['host']);if(isset($parts['port']))$u.=':'.$parts['port'];$u.=$parts['path']??'';if(isset($parts['query']))$u.='?'.$parts['query'];return $u;
    }
}
