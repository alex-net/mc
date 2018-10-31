<?php 

namespace app\assets;

use yii\web\AssetBundle;

class FASBundle extends AssetBundle
{
	public $sourcePath='@vendor/bower-asset/fontawesome/web-fonts-with-css';

	public $css=[
		'css/fontawesome-all.css',
	];
}
?>