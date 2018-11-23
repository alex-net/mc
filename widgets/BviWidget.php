<?php 

namespace app\widgets;

use Yii;

class BviWidget extends \yii\base\Widget
{

	public function init(){
		\app\assets\BviInitAsset::register($this->view);

	}

	public function run()
	{

		return '<div class="bvi-panel-wrapp"><a href="#" class="fa fa-eye  bvi-panel-open">Версия для слабовидящих</a></div> ';
	}
}