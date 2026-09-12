<?php
namespace humhub\modules\sytvillinkpreview\services;
use Yii;
use RuntimeException;
class ImageCache
{
    public static function path(string $url):?string{
        if($url===''||!DomainPolicy::imagesAllowed($url))return null;
        $dir=Yii::getAlias('@webroot/uploads/sytvil-link-preview'); if(!is_dir($dir)&&!@mkdir($dir,0775,true)&&!is_dir($dir))return null;
        $base=$dir.'/'.hash('sha256',$url); foreach(['jpg','png','webp','gif'] as $ext){if(is_file($base.'.'.$ext))return $base.'.'.$ext;}
        return self::download($url,$base);
    }
    private static function download(string $url,string $base):?string{
        if(!function_exists('curl_init'))return null;
        (new PreviewFetcher())->assertPublicUrl($url);
        $body='';$type='';$tooLarge=false;$ch=curl_init($url);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>false,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>7,CURLOPT_USERAGENT=>'SytvilLinkPreview/2.0 (+HumHub)',CURLOPT_HEADERFUNCTION=>static function($ch,$line)use(&$type){if(stripos($line,'Content-Type:')===0)$type=trim(substr($line,13));return strlen($line);},CURLOPT_WRITEFUNCTION=>static function($ch,$chunk)use(&$body,&$tooLarge){if(strlen($body)+strlen($chunk)>2097152){$tooLarge=true;return 0;}$body.=$chunk;return strlen($chunk);}]);
        $ok=curl_exec($ch);$code=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE);if(!$type)$type=(string)curl_getinfo($ch,CURLINFO_CONTENT_TYPE);curl_close($ch);
        if(($ok===false&&!$tooLarge)||$tooLarge||$code<200||$code>=300)return null;
        $type=strtolower(trim(explode(';',$type)[0]));$map=['image/jpeg'=>'jpg','image/jpg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];if(!isset($map[$type]))return null;
        $path=$base.'.'.$map[$type];return @file_put_contents($path,$body)!==false?$path:null;
    }
    public static function clear():void{$dir=Yii::getAlias('@webroot/uploads/sytvil-link-preview');if(!is_dir($dir))return;foreach(glob($dir.'/*')?:[] as $f)if(is_file($f))@unlink($f);}
}
