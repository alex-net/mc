<?php 

namespace app\controllers\admin;

use yii\web\Controller;
use Yii;
use app\models\Content;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\models\Menu;
use app\models\FilesModel;

class ContentController extends Controller 
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



	// просмотр контента .. 
	public function actionCtList($type)
	{
		$dp=new ActiveDataProvider([
			'query'=>Content::contentquerylist($type),
			'pagination'=>[
				'pageSize'=>20,
				'totalCount'=>6,
			],
		]);
		return $this->render('ct-list',[
			'type'=>$type,
			'dp'=>$dp
		]);
	}
	//  добавление нового контента 
	public function actionCtAdd($type,$id=0)
	{
		return $this->actionCtEdit($type,$id);
	} 
	// Редактироание старного ...
	public function actionCtEdit($type,$id=0)
	{
		// загрузка ...
		$c=Content::findById($id);
		if(!$c)// не получилось загрузить 
			$c=new Content(['type'=>$type]);

		// отправка формы ..
		if (Yii::$app->request->isPost){
			$post=Yii::$app->request->post();
			switch(true){
				case isset($post['save']):
					$res=$c->save($post);
					break;
				case isset($post['kill']):
					$res=$c->kill($post);
					break;
			}
			if ($res)// если всё прошло успешно .. редиректим на список 
				return $this->redirect(['ct-list','type'=>$type]);
		}

		return $this->render('ct-edit',[
			'type'=>$type,
			'id'=>$id,
			'model'=>$c,
			//'menu'=>$m,
			//'files'=>$f,
		]);

		if (Yii::$app->request->isPost){ // данные пришли из формы ..
			$post=Yii::$app->request->post();
			$c=new Content(['type'=>$type]);
			$m=new Menu();
			$f=new FilesModel(['ct'=>$type]);
			// сохраение .. 
			if (isset($post['save']) && $c->save($post) && ($c->nomenuitem || $m->updateContentItemFromPost($post,$c->cid)) && $f->save($post,$c->cid))
				return $this->redirect(['ct-list','type'=>$type]);
			// удаелние .. 
			if (isset($post['kill'])){
				$c->kill($post);  // бахам содержимое .. 
				$m->killContentItem($post); // удаляем пункт меню 
				FilesModel::killpercid($c->cid); //  мочим файлы ..
				return $this->redirect(['ct-list','type'=>$type]);
			}

			//if ( ( ( && $c->kill($post) || isset($post['save']) && $c->save($post)) ) && $m->updateContentItemFromPost($post,$c->cid)  )
				
		}
		else{
			$c=Content::findById($id);
			if (!$c)
				return $this->redirect(['ct-list','type'=>$type]);
			$c->type=$type;
			$m=Menu::findByContent($id);
			$f=new FilesModel(['ct'=>$type]);
		}


		return $this->render('ct-edit',[
			'type'=>$type,
			'id'=>$id,
			'model'=>$c,
			'menu'=>$m,
			'files'=>$f,
		]);
	}


	/// файловый менеджер  .... 
	public function actionFileManager()
	{
		$get=Yii::$app->request->get();
		define('FM_EMBED',1);
		define('FM_ROOT_PATH', 'uploads');
		define('FM_ROOT_URL', '/uploads');
		define('FM_SELF_URL', \yii\helpers\Url::to(['admin/content/file-manager']));
		
		if ( !isset($get['p'])) {
			
			require_once \Yii::getAlias('@app/components/filemanager.php');
			return ;
		}

		return $this->render('file-manager');	
	}
}