<?php 

namespace app\assets;

class BviInitAsset extends \yii\web\AssetBundle
{
	public $basePath='@webroot/js';
	public $baseUrl='@web/js';

	public $js=[
		'bvi-init.js',
	];

	public $depends=[
		BviAsset::class,
	];
}