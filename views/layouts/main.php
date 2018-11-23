<?php 
use \yii\helpers\Html;
use app\assets\AppAsset;
use app\models\Menu;
AppAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?= Yii::$app->charset ?>">	
		<title><?= Html::encode($this->title) ?></title>
		<?php $this->head();?>
	</head>
	<body class="<?=implode(' ',Yii::$app->params['classes']);?>">
		<?php $this->beginBody();?>
		<div class='all-page'>
			<div class='header-tail sizer'>
				<a class='logo' href="/">
					<img src="/imgs/logo.png" alt='logo'/>
					<span><?=Yii::$app->name;?></span>
				</a>
				<?php $this->beginBlock('soc-nets',true);?>
				<div class="soc-nets">
					<?php foreach(Yii::$app->params['socnets'] as $k=>$v):?>
						<a href="<?=$v['link'];?>" class="<?=$v['class'];?> soclink-<?=$k+1;?>" target="_blank"></a>
					<?php endforeach;?>
				</div>
				<?php $this->endblock();?>
			</div>
			<?php $this->beginBlock('main-menu',true);?>
			<div class='menu-tail'><div class="sizer">
				<?=\app\widgets\MenuSiteWidget::widget(['mlist'=>Menu::fullmenulist(true)]);?>
			</div></div>
			<!-- <div class='menu-tail'>
				<div class="sizer">
				<ul >
					<li><a href="#">Новости</a></li>
					<li class='submenu'><a href="#">Кружки и секции</a>
						<ul>
							<li><a href="#">Танцевальные студии</a></li>
							<li><a href="#">Творческие студии</a></li>
							<li><a href="#">Секции настольных игр</a></li>
							<li><a href="#">Другие секции</a></li>
							<li><a href="#">Расписание кружков и секций</a></li>

						</ul>
					</li>
					<li><a href="#">Медиа</a></li>
					<li><a href="#">Подростково-молодежные клубы</a></li>
					<li><a href="#">Награды</a></li>
					<li><a href="#">Документы</a></li>
					<li><a href="#">Контакты</a></li>
				</ul>
				</div>
			</div>-->
			<?php $this->endBlock();?>
			

			<div class='footer'>
				<?php echo $this->blocks['main-menu'];?>

				<div class="row sizer">
					<div class='cell'></div>
					<div class='write-uns'>
						<a href="#" >Написать нам </a>
					</div>
					<div class='cell'>
						<a class='logo' href="/">
							<img src="/imgs/logo-f.png" alt='logo'/>
							<span><?=Yii::$app->name; ?></span>
						</a>
					</div>
					<?php echo $this->blocks['soc-nets']; ?>
					
					<div class='copyr'>© <?=date('Y');?> Все права защищены.</div>
				</div>
			</div>

			<div class='site-content sizer'>
				<div class='sitebar'>
					<?=\app\widgets\BlocksView::widget();?>
					<?php //\app\widgets\NewsWidget::widget(['ctype'=>'new']);?>
				</div>
				<div class="content">
					<?php if (!empty(Yii::$app->params['isfront'])):?>
						<?=\app\widgets\EventsNewsWidget::widget(['count'=>4]);?>
					<?php endif;?>
					
					
					<div class="content-tail">

						<?php echo $content;?>
						
					</div>
				</div>
			</div>
			
		</div>
	
		<?php $this->endBody();?>
	</body>
	
</html>
<?php $this->endPage();?>