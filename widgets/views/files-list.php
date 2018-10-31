<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\FileHelper;

?>
<ul class="list-files-widget">
<?php foreach($list as $y):?>
	<li class='item' data-fn="<?=$y['filename'];?>">
		<span title='Удалить' class="kill"></span>
		<?=Html::a($y['filename'],$y['url']);?>
		<?php if($y['isimage']):?>
			<?=Html::img(Url::to(['files/'.$preset.'/'.$y['filename']]));?>
		<?php endif;?>
		
	</li>
	
<?php endforeach;?>
</ul>