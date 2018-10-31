<?php 

namespace app\controllers\admin;

use app\models\Blocks;
use app\models\Content;
use Yii;

class BlocksController extends \yii\web\Controller
{
	public $layout='main-admin';
	public function beforeAction($act)
	{
		$res=parent::beforeAction($act);
		if (!$res || Yii::$app->user->isGuest || !Yii::$app->user->identity->can('blockmanager'))
			$this->redirect(['admin/user/index']);
		return $res;
	} 
	public function actionIndex()
	{
		if (Yii::$app->request->isPost){
			Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
			$post=Yii::$app->request->post();
			if (!empty($post['act']) && $post['act']=='updateblocks' && !empty($post['blocksdata']))
				Blocks::updateblocksdata($post['blocksdata']); 
			return ['post'=>$post];
		}


		$bl=\app\models\Blocks::blockslist();
		$cids=[];
		foreach($bl as $b)
			if ($b->cid)
				$cids[]=$b->cid;
		$ctitles=Content::findperids($cids);

		
		return $this->render('list',['list'=>$bl,'ctitls'=>$ctitles]);
	}
	/**
	* новый блок или редактиование существующего 
	**/
	public function actionEdit($bid=0)
	{
		$b=Blocks::findById($bid);
		if (Yii::$app->request->isPost){
			$post=Yii::$app->request->post();
			Yii::info($post,'sad');
			if (isset($post['save']) && $b->save($post) || 
				isset($post['kill']) && $b->kill($post)
			)
				return $this->redirect(['admin/blocks/index']);
			
		}
		
		return $this->render('edit',['bid'=>$bid,'b'=>$b]);
	}
}