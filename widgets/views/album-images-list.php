<?php 
use yii\helpers\Html;
use yii\helpers\Url;
\app\assets\FancyAsset::register($this);
?>
<ul class="list-files-widget-of-album">
<?php foreach($list as $y):?>
	<li class='item' data-fn="<?=$y['filename'];?>">
	 	<?=Html::a(Html::img(Url::to(['image/file','preset'=>$preset,'fn'=>$y['filename']])),'/files/album/'.$y['filename'],['data-fancybox'=>"images"]);?>
		
	</li>
	
<?php endforeach;?>