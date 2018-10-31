<?php 

namespace app\assets;

use yii\web\AssetBundle;

class AppAdminAsset extends AssetBundle
{
	public $basePath='@webroot';
	public $baseUrl='@web';

	public $css=[
		'css/site.css',
		'css/admin-site.css',
	];
	
	public $js=[
		'js/admin-site.js',
	];
	public $depends=[
		'yii\bootstrap\BootstrapAsset',
		'yii\web\YiiAsset',
		//'yii\web\JqueryAsset',
		'app\assets\jQueryUIAsset',
	];
}
