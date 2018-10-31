<?php 
use yii\helpers\Html;
use app\widgets\MenuSorterWidget;
?>
<li class='level-<?=$l;?>' data-mid="<?=$v['mid'];?>">
	<?=Html::a($v['title'],['admin/menus/elitem','mid'=>$v['mid']]);?>
	(<?=Html::a($v['contentid']?$v['ctitle']:'Сылка',($v['contentid']?'/':'').$v['url']);?>)
	<?php if ($v['contentid']):?>
		
	<?php else:?>

	<?php endif; ?>
	<?=HTML::checkbox('',$v['status'],['title'=>'Активен','class'=>'status-control']);?>
	<?php if (!empty($v['childs'])):?>
		<?=MenuSorterWidget::widget(['menulist'=>$v['childs'],'level'=>$l+1]); ?>
	<?php endif;?>
</li>