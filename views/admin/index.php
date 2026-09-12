<?php
use humhub\modules\ui\form\widgets\ActiveForm;use humhub\helpers\Html;use yii\helpers\Url;
/** @var \humhub\modules\sytvillinkpreview\models\SettingsForm $model */
$this->title='Sytvil Link Preview';
?>
<div class="panel panel-default"><div class="panel-heading"><strong>Sytvil Link Preview v2.0</strong></div><div class="panel-body">
<p>Rich previews are cached and fetched only when needed. Private/reserved network addresses are blocked. Images can be proxied through your own server for privacy and reliability.</p><?php if (!empty($migrationPending)): ?><div class="alert alert-warning"><strong>Database upgrade pending.</strong> Apply the v2.0 module migration before using the new preview features.</div><?php endif; ?>
<div class="row"><div class="col-md-4"><div class="well"><strong><?= $stats['total'] ?></strong><br>Cached previews</div></div><div class="col-md-4"><div class="well"><strong><?= $stats['fresh'] ?></strong><br>Fresh previews</div></div><div class="col-md-4"><div class="well"><strong><?= $stats['failed'] ?></strong><br>Failed previews</div></div></div>
<?php $form=ActiveForm::begin(); ?>
<div class="row"><div class="col-md-4"><?= $form->field($model,'cacheDays')->input('number',['min'=>1,'max'=>30]) ?></div><div class="col-md-4"><?= $form->field($model,'maxPreviews')->input('number',['min'=>1,'max'=>5]) ?></div><div class="col-md-4"><?= $form->field($model,'domainRateLimit')->input('number',['min'=>1,'max'=>120]) ?></div></div>
<div class="row"><div class="col-md-6"><?= $form->field($model,'cacheImages')->checkbox() ?></div><div class="col-md-6"><?= $form->field($model,'mobileCompact')->checkbox() ?></div></div>
<div class="row"><div class="col-md-6"><?= $form->field($model,'allowedDomains')->textarea(['rows'=>5,'placeholder'=>'Optional. Leave blank to allow all public domains.']) ?></div><div class="col-md-6"><?= $form->field($model,'blockedDomains')->textarea(['rows'=>5]) ?></div></div>
<div class="row"><div class="col-md-6"><?= $form->field($model,'noImageDomains')->textarea(['rows'=>5]) ?></div><div class="col-md-6"><?= $form->field($model,'domainTtls')->textarea(['rows'=>5,'placeholder'=>"news.example.com=1\nexample.org=14"]) ?></div></div>
<?= Html::submitButton('Save settings',['class'=>'btn btn-primary']) ?> <?php ActiveForm::end(); ?>
<?= Html::a('Purge all cached previews',['purge'],['class'=>'btn btn-danger','data-method'=>'post','data-confirm'=>'Delete all cached previews and locally cached images?']) ?>
<hr><h4>Recent cache entries</h4><div class="table-responsive"><table class="table table-condensed"><thead><tr><th>Site / title</th><th>Status</th><th>Updated</th><th>Fetches</th><th></th></tr></thead><tbody>
<?php foreach($recent as $p): ?><tr><td><strong><?= Html::encode($p->site_name?:parse_url($p->url,PHP_URL_HOST)) ?></strong><br><small><?= Html::encode($p->title?:$p->url) ?></small></td><td><?= $p->status?'<span class="label label-success">OK</span>':'<span class="label label-danger">Failed</span>' ?></td><td><?= Yii::$app->formatter->asDatetime($p->updated_at) ?></td><td><?= (int)$p->fetch_count ?></td><td><?= Html::a('Delete',['delete','id'=>$p->id],['class'=>'btn btn-xs btn-default','data-method'=>'post']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
