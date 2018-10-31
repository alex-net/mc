<?php 

namespace app\controllers\admin;

use app\models\Menu;
use Yii;
use app\models\Content;

class MenusController extends \yii\web\Controller
{
	public $layout='main-admin';
	public function beforeAction($action)
	{
		$res=parent::beforeAction($action);
		if (!$res || Yii::$app->user->isGuest ||  !Yii::$app->user->identity->can('contentmanager'))
			$this->redirect(['admin/user/index']);
			//throw new \yii\web\HttpException(403,"недостаточно прав");
		return $res;
	}
	// список лементов с возможностью порулить уровнями и весами ..
	public function actionIndex()
	{


		// сохранение весов и расположений ..
		if (Yii::$app->request->isPost && Yii::$app->request->isAjax){
			Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
			$menu=Yii::$app->request->post('menudata',[]);
			if ($menu){
				Yii::info($menu,'$menu');
				$mm=[];
				$pmids=[];
				foreach($menu as $v){
					$pmids[$v['level']]=$v['id'];
					$data=[
						'pmid'=>intval($v['level'])?$pmids[$v['level']-1]:0,
						'weight'=>$v['weight'],
						'status'=>$v['active'],
					];
					Yii::$app->db->createCommand()->update('menuitems',$data,['mid'=>$v['id']])->execute();
					$mm[]=[
						'mid'=>$v['id'],
						'pmid'=>intval($v['level'])?$pmids[$v['level']-1]:0,
						'weight'=>$v['weight'],
						'status'=>$v['active'],
					];
				}
				return ['status'=>'ok'];
			}
			return ['status'=>'error',];
		}
		$mlist=Menu::fullmenulist();
		return $this->render('mitem-order',[
			'mlist'=>$mlist,
		]);
	}
	// редактирование пункта ..
	public function actionElitem($mid)
	{
		$m=Menu::findById($mid);
		
		if (Yii::$app->request->isPost){
			$post=Yii::$app->request->post();
			if (isset($post['save']) && $m->saveitem($post) || isset($post['kill']) && $m->killitem($post))
				return $this->redirect(['admin/menus']);
		}
		
		$m->scenario=Menu::SCENARIO_CREATE_FROM_MENU;
		
		return $this->render('menu-item-edit',[
			'm'=>$m,
			'cl'=>Content::getListptions(),
		]);
	}
	
}