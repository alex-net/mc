<?php 

namespace app\controllers;

use yii\web\Controller;
use Yii;
use app\models\Content;
use yii\data\ActiveDataProvider;

class McController extends Controller
{
	public function init()
	{
		parent::init();
		Yii::$app->params['classes']=[];
	}
	public function actionIndex()
	{
		return $this->render('test');
	}
	// отображение контента на сайте .. 
	public function actionShowContent($path='')
	{
		Yii::$app->params['isfront']=empty($path);
		Yii::$app->params['classes'][]=empty($path)?'front':'not-front';
		if (empty($path) && !empty(Yii::$app->params['front-is']))
			$path=Yii::$app->params['front-is'];
	
		


		$c=Content::loadByAlias($path);
		if (!$c)
			throw new \yii\web\HttpException(404,"Страница не найдена");
		$c->preparebody();
		$this->view->title=$c->title.' | '.Yii::$app->name;
		return $this->render('show-'.$c->type.'-content',[
			'path'=>$path,
			'model'=>$c,
		]);
	}

	public function actionNewsList()
	{
		$dp=new ActiveDataProvider([
			'query'=>Content::NewsList(),
			'pagination'=>[
				'pageSize'=>20,
			],
			'sort'=>[
				'attributes'=>['created'],
				'defaultOrder'=>[
					'created'=>SORT_DESC,
				],
			],
		]);
		$this->view->title='Новости | '.Yii::$app->name;
		return $this->render('news-page-list',['list'=>$dp]);	
	}

	// форма обратной связи .. отдаём контент только по аяксу .. 
	public function actionWriteUns()
	{
		$req=Yii::$app->request;
		Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
		
		if (!$req->isAjax )
			return $this->redirect('/');

		switch(true){
			case  $req->isGet:
				$f=new \app\models\WriteUnsForm();
				return [
					'status'=>'ok',
					'html'=>$this->renderPartial('write-uns-form',['model'=>$f]),
				];
			case $req->isPost: // форма отправлена
				$post=$req->post();
				$f=new \app\models\WriteUnsForm();
				// загружае данные и валидим . 
				$f->load($post);
				if ($f->validate()){
					$mail=Yii::$app->mailer->compose('write-uns',['f'=>$f]);
					$mail->setFrom(Yii::$app->params['writeuns']['from']);
					$mail->setTo(Yii::$app->params['writeuns']['to']);
					$mail->setSubject(Yii::$app->params['writeuns']['theme']);
					//$mail->setHtmlBody('ad<b>adasd</b>asdad');
					$mail->send();
					return [
						'status'=>'ok',
						'mess'=>'Спасибо за сообщение',
					];
				}
				else
					return [
						'errs'=>$f->errors,
						'status'=>'nook',
					];
		}
	}

	// альбомы 
	public function actionAlbumsList()
	{
		$dp=new ActiveDataProvider([
			'query'=>Content::AlbumsList(),
			'pagination'=>[
				'pageSize'=>20,
			],
		]);
		$this->view->title='Альбомы | '.Yii::$app->name;
		return $this->render('albums-page-list',['list'=>$dp]);
	}
}