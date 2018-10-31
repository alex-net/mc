<?php 

namespace app\assets;

class jQueryUIAsset extends \yii\web\AssetBundle
{
	public $sourcePath='@bower/jqueryui';
	public $css=[
		'themes/base/all.css',
	];
	public $js=[
		'jquery-ui.min.js',
	];
}