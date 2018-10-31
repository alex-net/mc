<?php 

namespace app\widgets;

use Yii;
use yii\helpers\Html;

class SiteAdminWidget extends \yii\base\Widget
{
	public $cid;
	public $ctype;
	public function run()
	{
		if (!empty(Yii::$app->user->identity) && Yii::$app->user->identity->can('contentmanager')){
			$links=[
				['title'=>'Правка','route'=>['admin/content/ct-edit','type'=>$this->ctype,'id'=>$this->cid]],
			];
			foreach($links as $k=>$l)
				$links[$k]='<li>'.Html::a($l['title'],$l['route'],['target'=>'_blank']).'</li>';

			return '<ul class="admin-links">'.implode('',$links).'</ul>';
		}
		return '';
	}
}