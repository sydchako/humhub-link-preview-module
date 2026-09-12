<?php
namespace humhub\modules\sytvillinkpreview\services;
class UrlExtractor
{
    public static function previewableUrls(string $text,int $limit=3):array
    {
        $text=preg_replace('/\[[^\]]*\]\(oembed:[^)]+\)/i','',$text)??$text;
        if(!preg_match_all('~https?://[^\s<>()\[\]"\']+~iu',$text,$m)) return [];
        $out=[];
        foreach($m[0] as $c){$c=rtrim($c,'.,;:!?)}]'); if(filter_var($c,FILTER_VALIDATE_URL) && !in_array($c,$out,true)){$out[]=$c;if(count($out)>=$limit)break;}}
        return $out;
    }
    public static function firstPreviewableUrl(string $text):?string{return self::previewableUrls($text,1)[0]??null;}
}
