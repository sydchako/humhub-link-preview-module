<?php
namespace humhub\modules\sytvillinkpreview\controllers;
use humhub\modules\admin\components\Controller;
use humhub\modules\sytvillinkpreview\models\SettingsForm;
use humhub\modules\sytvillinkpreview\models\LinkPreview;
use humhub\modules\sytvillinkpreview\services\ImageCache;
use Yii;
class AdminController extends Controller
{
 public function actionIndex(){ $model=new SettingsForm();$model->loadSettings();if($model->load(Yii::$app->request->post())&&$model->validate()){$model->saveSettings();$this->view->saved();}
  $schema=Yii::$app->db->schema->getTableSchema(LinkPreview::tableName(),true);$migrationPending=$schema===null||!isset($schema->columns['fetch_count']);$stats=['total'=>(int)LinkPreview::find()->count(),'failed'=>(int)LinkPreview::find()->where(['status'=>0])->count(),'fresh'=>(int)LinkPreview::find()->where(['>', 'expires_at', time()])->count()];$recent=$migrationPending?[]:LinkPreview::find()->orderBy(['updated_at'=>SORT_DESC])->limit(25)->all();return $this->render('index',compact('model','stats','recent','migrationPending'));}
 public function actionPurge(){LinkPreview::deleteAll();ImageCache::clear();$this->view->saved();return $this->redirect(['index']);}
 public function actionDelete(int $id){if($m=LinkPreview::findOne($id))$m->delete();return $this->redirect(['index']);}
}
