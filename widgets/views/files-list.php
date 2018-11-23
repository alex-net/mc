<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\FileHelper;
\app\assets\FileManAsset::register($this);;
?>
<div class="widget-fileman">
	<input type='file' multiple="multiple" class="files-input-element"/>
	<hr/>
	<div class='list-files-wrapper'>
	<?php  /*if ( $list):?>
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
	<?php else:?>
		Файлы не загружены 
	<?php endif;*/?>
	</div>
</div>