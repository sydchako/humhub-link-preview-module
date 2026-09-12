<?php
namespace humhub\modules\sytvillinkpreview\controllers;
use humhub\components\Controller;
use humhub\modules\sytvillinkpreview\models\LinkPreview;
use humhub\modules\sytvillinkpreview\services\PreviewFetcher;
use humhub\modules\sytvillinkpreview\services\DomainPolicy;
use humhub\modules\sytvillinkpreview\services\ImageCache;
use Yii;use yii\filters\AccessControl;use yii\web\BadRequestHttpException;use yii\web\Response;use Throwable;
class PreviewController extends Controller
{
    public $enableCsrfValidation=true;
    public function behaviors(){ $b=parent::behaviors();$b['access']=['class'=>AccessControl::class,'rules'=>[['allow'=>true,'roles'=>['@']]]];return $b; }
    public function actionFetch(string $url=''){Yii::$app->response->format=Response::FORMAT_JSON;if(!$this->v2Ready())return ['ok'=>false,'error'=>'Sytvil Link Preview database upgrade is pending.'];return $this->loadPreview($url,false);}
    public function actionRefresh(){Yii::$app->response->format=Response::FORMAT_JSON;if(!$this->v2Ready())return ['ok'=>false,'error'=>'Sytvil Link Preview database upgrade is pending.'];$url=(string)Yii::$app->request->post('url','');return $this->loadPreview($url,true);}
    public function actionImage(int $id,string $kind='image'){
        if(!$this->v2Ready())throw new BadRequestHttpException('Module database upgrade is pending.');
        $m=LinkPreview::findOne($id);if(!$m)throw new BadRequestHttpException('Preview not found.');$url=$kind==='favicon'?$m->favicon_url:$m->image_url;if(!$url)throw new BadRequestHttpException('Image unavailable.');$path=ImageCache::path($url);if(!$path)throw new BadRequestHttpException('Image unavailable.');return Yii::$app->response->sendFile($path,null,['inline'=>true]);
    }
    private function v2Ready():bool{$schema=Yii::$app->db->schema->getTableSchema(LinkPreview::tableName(),true);return $schema!==null&&isset($schema->columns['favicon_url'],$schema->columns['fetch_count']);}
    private function loadPreview(string $url,bool $force):array{
        $url=trim($url);if($url===''||strlen($url)>2048)throw new BadRequestHttpException('Invalid URL.');if(!DomainPolicy::isAllowed($url))return ['ok'=>false,'error'=>'Preview disabled for this domain.'];
        $canonicalInput=DomainPolicy::canonicalForCache($url);$this->enforceRateLimit($canonicalInput,$force);$hash=hash('sha256',$canonicalInput);$model=LinkPreview::findOne(['url_hash'=>$hash]);if(!$force&&$model&&$model->isFresh())return $this->payload($model);
        if(!$model)$model=new LinkPreview(['url'=>$canonicalInput,'url_hash'=>$hash,'created_at'=>time(),'fetch_count'=>0]);
        try{$d=(new PreviewFetcher())->fetch($canonicalInput);$canonical=DomainPolicy::canonicalForCache($d['url']?:$canonicalInput);$canonicalHash=hash('sha256',$canonical);if($canonicalHash!==$model->url_hash){$existing=LinkPreview::findOne(['url_hash'=>$canonicalHash]);if($existing&&$existing->id!==$model->id){if($model->id)$model->delete();$model=$existing;}}
            $model->url=$canonical;$model->url_hash=$canonicalHash;foreach(['title','description','image_url','favicon_url','site_name','content_type','provider','author','section','og_type','locale','published_at','image_width','image_height'] as $k)$model->$k=$d[$k]??null;$model->status=1;$model->error_message=null;$model->expires_at=time()+DomainPolicy::ttl($canonical);$model->fetch_count=(int)$model->fetch_count+1;
        }catch(Throwable $e){$model->status=0;$model->error_message=substr($e->getMessage(),0,500);$model->expires_at=time()+3600;$model->fetch_count=(int)$model->fetch_count+1;}
        $model->updated_at=time();$model->save(false);return $this->payload($model);
    }
    private function payload(LinkPreview $m):array{
        if((int)$m->status!==1)return ['ok'=>false,'error'=>$m->error_message?:'Preview unavailable.','url'=>$m->url,'retryAfter'=>$m->expires_at];
        $cacheImages=(bool)Yii::$app->getModule('sytvillinkpreview')->settings->get('cacheImages',1);$image=$m->image_url;$favicon=$m->favicon_url;if($cacheImages){if($image)$image=Yii::$app->urlManager->createUrl(['/sytvillinkpreview/preview/image','id'=>$m->id,'kind'=>'image']);if($favicon)$favicon=Yii::$app->urlManager->createUrl(['/sytvillinkpreview/preview/image','id'=>$m->id,'kind'=>'favicon']);}
        return ['ok'=>true,'preview'=>['id'=>$m->id,'url'=>$m->url,'title'=>$m->title,'description'=>$m->description,'image'=>$image,'favicon'=>$favicon,'siteName'=>$m->site_name,'provider'=>$m->provider,'author'=>$m->author,'section'=>$m->section,'type'=>$m->og_type,'locale'=>$m->locale,'publishedAt'=>$m->published_at,'imageWidth'=>$m->image_width,'imageHeight'=>$m->image_height]];
    }
    private function enforceRateLimit(string $url,bool $force):void{
        $cache=Yii::$app->cache;$uid=(int)Yii::$app->user->id;$minute=(int)floor(time()/60);$key='sytvil_lp_user_'.$uid.'_'.$minute;$count=(int)$cache->get($key);if($count>=30)throw new BadRequestHttpException('Too many preview requests.');$cache->set($key,$count+1,75);
        $host=DomainPolicy::host($url);$limit=max(1,min(120,(int)Yii::$app->getModule('sytvillinkpreview')->settings->get('domainRateLimit',20)));$hour=(int)floor(time()/3600);$dkey='sytvil_lp_domain_'.sha1($host).'_'.$hour;$dc=(int)$cache->get($dkey);if($dc>=$limit&&$force)throw new BadRequestHttpException('This domain has reached its hourly refresh limit.');if($force||$dc<$limit)$cache->set($dkey,$dc+1,3700);
    }
}
