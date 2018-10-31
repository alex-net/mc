<?php

namespace app\assets;

use Yii;

class CKEditorAsset extends \yii\web\AssetBundle
{
	public $basePath = '@webroot/cke';
    public $baseUrl = '@web/cke';
	public $js=[
		'ckeditor.js',
		'config.js'
		//'https://cdn.ckeditor.com/4.9.2/full/ckeditor.js',

		//'https://cdn.ckeditor.com/ckeditor5/10.0.1/classic/ckeditor.js',
	];
	public function init()
	{
		parent::init();
		$presets=[];
		$type=Yii::$app->request->get('type','');
		if ($type)
			$presets[]=['Оригинал',$type];
		if (!empty(Yii::$app->params['presets']))
			foreach(Yii::$app->params['presets'] as $x=>$y){
				$crop=empty($y['crop'])?'':' (с обрезкой)';
				$presets[]=[$y['size'].$crop,$x];
			}
		Yii::$app->view->registerJsVar('imagepresets',$presets);
		
		
		
		//\Yii::info('init ckeditor');
	}
}
// https://docs.ckeditor.com/ckeditor4/latest/guide/index.html
// https://sdk.ckeditor.com/index.html