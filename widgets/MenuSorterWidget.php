<?php 

namespace app\widgets;



class MenuSorterWidget extends \yii\base\Widget
{
	public $menulist=[];
	public $level=0;
	public function run()
	{
		$out='';
		foreach($this->menulist as $v)
			$out.=$this->render('sorter-meni-element',['v'=>$v,'l'=>$this->level]);
		if (!$this->level)
			$out='<ul class="sorter-eleemnts-menu">'.$out.'</ul>';
		return $out;
	}
}