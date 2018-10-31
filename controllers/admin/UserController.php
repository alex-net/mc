<?php 

namespace app\controllers\admin;

use yii\web\Controller;
use Yii;
use app\models\User;

class UserController extends Controller 
{
	public $layout='main-admin';
	public function actionTest()
	{
		return $this->render('test');
	}


	public function actionIndex()
	{
		if (Yii::$app->user->isGuest){ // покаываем форму входа .. 
			$u=new User();
			$post=Yii::$app->request->post();
			if ($post && $u->login($post)){
				Yii::$app->session->addFlash('info','Вы вошли в систему как '.$u->name);
				return $this->refresh();
			}
			return $this->render('login',['u'=>$u]);
		}
		return $this->render('cabinete');
	}
	public function actionLogout()
	{
		Yii::$app->user->logout();
		return $this->goHome();
	}
}