<?php 
use yii\helpers\Html;
?>
<li class="<?=empty($el['childs'])?'':'submenu';?>">
	<?=Html::a($el['title'],'/'.ltrim($el['url'],'/'));?>
	<?php if (!empty($el['childs'])):?>
		<?=\app\widgets\MenuSiteWidget::widget(['mlist'=>$el['childs']]); ?>
	<?php endif;?>
</li>