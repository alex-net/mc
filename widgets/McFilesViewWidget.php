<?php 

namespace app\widgets;

use app\models\FilesModel;
use Yii;

class McFilesViewWidget extends \yii\base\Widget
{
	public $ct;// тип контента .. 
	public $cid; // идетификатор контента ..
	public $preset;// пресет отображения  thumb
	public function run()
	{
		$flist=FilesModel::findFilesPerCid($this->cid);
		
		$fl=[];
		foreach($flist as $y)
			$fl[]=$y['filename'];
		
		$this->view->registerJsVar('filesuploadedlist',$fl);
		$this->view->registerJsVar('filesloadurl',Yii::$aliases['@filesUrl']);
		return $this->render('files-list',[
			'list'=>$flist,
			'preset'=>$this->preset,
		]);

	}
}