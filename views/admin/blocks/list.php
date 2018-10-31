<?php 
use \yii\helpers\Html;

?>
<?=Html::a('Новый блок',['admin/blocks/edit']);?>

<?php if ($list):?>
	
	<ul class="sortable-blocks">
	<?php foreach($list as $b ):?>
		<li class='bolck-<?=$b->id;?>' data-bid="<?=$b->id;?>">
			<?=Html::a('Блок № '.$b->id,['admin/blocks/edit','bid'=>$b->id]);?>
			<?=Html::checkBox('status',$b->status,['title'=>'Активный','class'=>'status-control']);?>	
			<div class='content'>

				<?php if ($b->cid):?>
					Тизер для <?=Html::a($ctitls[$b->cid]->title,['admin/content/ct-edit','type'=>$ctitls[$b->cid]->type,'id'=>$b->cid]);?>
				<?php else:?>

					<?=strip_tags($b->content);?>
				<?php endif;?>
			</div>
		</li>
	<?php endforeach;?>
	</ul>
	
	
<?php endif;?>