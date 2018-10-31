<?php 

namespace app\widgets;
use app\models\Content;
class EventsNewsWidget extends \yii\base\Widget
{
	public $count; // количестово эдементов .. 
	public function run()
	{
		$els=Content::NewsListLast(2);
		//подгружаем картинки ... 
		$files=\app\models\FilesModel::filespercontentids(array_keys($els));
		foreach($files as $x=>$y)
			$els[$x]['img']='/files/news-slider/'.$y;
		return $this->render('events-list',['els'=>$els]);
	}
}

