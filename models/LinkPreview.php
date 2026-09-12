<?php
namespace humhub\modules\sytvillinkpreview\models;
use yii\db\ActiveRecord;
class LinkPreview extends ActiveRecord
{
    public static function tableName(){return '{{%sytvil_link_preview}}';}
    public function rules(){return [
        [['url','url_hash'],'required'],[['url','description','image_url','favicon_url','error_message'],'string'],
        [['title'],'string','max'=>300],[['site_name','content_type','provider','author','section','og_type','locale'],'string','max'=>150],
        [['status','created_at','updated_at','expires_at','published_at','image_width','image_height','fetch_count'],'integer'],[['url_hash'],'string','max'=>64],
    ];}
    public function isFresh():bool{return (int)$this->expires_at>time();}
}
