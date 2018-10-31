<?php 

namespace app\widgets;

class MenuSiteWidget extends \yii\base\Widget
{
	public $mlist=[];
	public $level=0;
	public function run()
	{
		if (empty($this->mlist))
			return '';
		$out='';
		foreach($this->mlist as $v)
			$out.=$this->render('menu-item',['el'=>$v]);
		

		return '<ul>'.$out.'</ul>';
	}
}