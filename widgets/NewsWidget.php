<?php 

namespace app\widgets;
use Yii;
use app\models\Content;
use app\models\FilesModel;
class NewsWidget extends \yii\base\Widget
{
	public $ctype;
	public $count=2;
	public function run()
	{
		$q=Content::QueryForblocks($this->ctype);
		$q->limit($this->count)->indexBy('cid');
		$q=$q->all();
		// запрос .. фалов для контента . .
		$ff=FilesModel::filespercontentids(array_keys($q));

		
		$out=[];

		foreach($q as $y)
			$out[]=$this->render('news-taser-block',[
				'content'=>$y,
				'fileurl'=>Yii::$aliases['@filesUrl'].'/mews-block-teaser/'.$ff[$y['cid']],
			]);
		
		return implode('',$out);
	}
}