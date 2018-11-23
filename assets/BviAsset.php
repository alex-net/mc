<?php 

namespace app\assets;

class BviAsset extends \yii\web\AssetBundle
{
	public $basePath='@webroot/bvi/button-visually-impaired';
	public $baseUrl='@web/bvi/button-visually-impaired';

	public $css=[
		'css/bvi.min.css',
	];
	public $js=[
		//'//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js',
		'js/responsivevoice.min.js',
		'js/js.cookie.js',
		
		'js/bvi.min.js',
		
	];

	public $depends=[
		\yii\web\JqueryAsset::class
	];
}