<?php 

namespace app\widgets;
use Yii;
use app\models\Blocks;
use app\models\Content;
use app\models\FilesModel;
class BlocksView extends \yii\base\widget
{
	public function run()
	{
		$bb=Blocks::blockslist(true);
		// собрать со всех cid 
		$cids=[];
		foreach($bb as $b)
			if ($b->cid)
				$cids[]=$b->cid;
		// запро тизеров этих самых контентов . 
		$out=[];
		if ($cids){
			$cids=Content::ContentForblocks($cids);
			
			// запрос .. фалов для контента . .
			$ff=FilesModel::filespercontentids(array_keys($cids));

			
			

			foreach($cids as $x=>$y)
				$out[$x]=$this->render('news-taser-block',[
					'content'=>$y,
					'fileurl'=>Yii::$aliases['@filesUrl'].'/mews-block-teaser/'.$ff[$x],
				]);

		}

		return $this->render('blocks-list',['bb'=>$bb,'out'=>$out]);
	}
}