<?php 

namespace app\widgets;

use app\models\FilesModel;
use Yii;
/**
 * класс позволяет загрузить файлы в контент ...
 */
class McFilesViewWidget extends \yii\base\Widget
{
	public $ct;// тип контента .. 
	public $cid; // идетификатор контента ..
	public $preset;// пресет отображения  thumb
	public function run()
	{
		$flist=FilesModel::findFilesPerCid($this->cid);
		
		
		$this->view->registerJsVar('fileman',[
			'filesuploadedlist'=>$flist,
			'filesloadurl'=>Yii::$aliases['@filesUrl'],
			'filesexts'=>FilesModel::IMG_EXT,
			'contentnum'=>(int)$this->cid,
			'smallpreset'=>$this->preset,

		]);
		//$this->view->registerJsVar('filesuploadedlist',$fl);
		//$this->view->registerJsVar('filesloadurl',Yii::$aliases['@filesUrl']);
		//$this->view->registerJsVar('filesexts',FilesModel::IMG_EXT);
		//$this->view->registerJsVar();
		//$this->view->registerJsVar('contenttype',$this->ct);
		return $this->render('files-list',[
			'list'=>$flist,
			'preset'=>$this->preset,
		]);

	}
}