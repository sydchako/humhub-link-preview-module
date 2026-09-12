<?php
use yii\db\Migration;
class m260912_130000_link_preview_v2 extends Migration
{
 public function safeUp(){
  $t='{{%sytvil_link_preview}}';
  foreach(['favicon_url'=>$this->text()->null(),'provider'=>$this->string(150)->null(),'author'=>$this->string(150)->null(),'section'=>$this->string(150)->null(),'og_type'=>$this->string(100)->null(),'locale'=>$this->string(50)->null(),'published_at'=>$this->integer()->null(),'image_width'=>$this->integer()->null(),'image_height'=>$this->integer()->null(),'fetch_count'=>$this->integer()->notNull()->defaultValue(0)] as $c=>$type){$this->addColumn($t,$c,$type);}
  $this->createIndex('ix_sytvil_link_preview_status','{{%sytvil_link_preview}}','status');
  $this->createIndex('ix_sytvil_link_preview_updated','{{%sytvil_link_preview}}','updated_at');
 }
 public function safeDown(){foreach(['ix_sytvil_link_preview_updated','ix_sytvil_link_preview_status'] as $i)$this->dropIndex($i,'{{%sytvil_link_preview}}'); foreach(['fetch_count','image_height','image_width','published_at','locale','og_type','section','author','provider','favicon_url'] as $c)$this->dropColumn('{{%sytvil_link_preview}}',$c);}
}
