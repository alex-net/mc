<?php

namespace app\widgets;

use app\models\FilesModel;
use Yii;

class AlbumImageslist extends \yii\base\widget
{
	public $cid;
	public $preset;

	public function run()
	{
		$ff=FilesModel::findFilesPerCid($this->cid);
		yii::info($ff,'ff');
		return $this->render('album-images-list',[
			'list'=>$ff,
			'preset'=>$this->preset,
		]);
	}
}