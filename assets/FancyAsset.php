<?php 

namespace app\assets;

class FancyAsset extends \yii\web\AssetBundle
{
	//public $sourcePath = '@vendor/bower-asset/fancybox/src';
	public $js=[
		'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js',
	];

	public $css=[
		'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css',
	];

	public $depends=[
		'yii\web\JqueryAsset',
	];
}