<?php 

namespace app\controllers;
use Yii;
use app\models\FilesModel;
class ImageController extends \yii\web\Controller
{
	public function beforeAction($action)
	{
		$res=parent::beforeAction($action);
		if ((!$res || Yii::$app->user->isGuest ||  !Yii::$app->user->identity->can('image manager') ) && in_array($action, ['file-kill','set-files-weight']))
			$this->redirect(['admin/user/index']);
			//throw new \yii\web\HttpException(403,"недостаточно прав");
		return $res;
	}
	public function actionFile($preset,$fn)
	{
		$ps=Yii::$app->params['presets'];
		if (empty($ps[$preset]))
			throw new \yii\web\HttpException(404,"Картинка не найдена");
		$ps=$ps[$preset];
		
		// если файл ещё не создан .. надо его сгенерить
		$dest=Yii::$aliases['@presets'].'/'.$preset.'/'.$fn;
		if (!file_exists(Yii::$aliases['@presets'].'/'.$preset)) // нет папки для пресета . надо создать 
			\yii\helpers\FileHelper::createDirectory(Yii::$aliases['@presets'].'/'.$preset);
		if (!file_exists($dest)){
			$folder=FilesModel::filefolder($fn);
			if (empty($folder))
				throw new \yii\web\HttpException(404,"Картинка не найдена1");
			$from=Yii::$aliases['@files'].'/'.$folder.'/'.$fn;
			if (!file_exists($from))
				throw new \yii\web\HttpException(404,"Картинка не найдена2");

			// грузим картинку .. 
			$im=new \Imagine\Imagick\Imagine();
			$img=$im->open($from);
			$size=$img->getSize();
			

			list($w,$h)=explode('x',$ps['size']);
			if ($w=='auto')
				$w=$size->getWidth()/$size->getHeight()*$h;
			if ($h=='auto')
				$h=$size->getHeight()/$size->getWidth()*$w;

			$mode=!empty($ps['crop'])?\Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND:\Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET;
			$im=$img->thumbnail(new \Imagine\Image\Box($w,$h),$mode); /// THUMBNAIL_INSET
			$im->save($dest);

			

			//print_r($ps);
		}
		header('Content-Type:image/'.explode('.',$fn)[1].';');
		readfile($dest);
		///$dest

		//echo Yii::$aliases['@presets'].'/'.$fn;


		//return $preset.'  dsa '.$fn;
	}

	/// удаление файлов .. 
	public function actionFileKill()
	{
		if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost)
			throw new yii\web\HttpException(400,"Некорректный запрос");
		$post=Yii::$app->request->post();
		Yii::$app->response->format=yii\web\Response::FORMAT_JSON;
		$f=FilesModel::loadFile($post['fn']);
		if ($f)
			$f->kill();
		// собтвенно удаление... 
		
		return [
			'status'=>'ok',
			'fn'=>$post['fn'],
		];	
	}

	// загнать вес файлов ... по их порядку в массиве 
	public function actionSetFilesWeight()
	{
		if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost)
			return;
		$post=Yii::$app->request->post();
		// нет списка файлов 
		Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
		if (empty($post['fl']) || !is_array($post['fl']) )
			return ['err'=>1]; // уходим 
		if(FilesModel::filesetweight($post['fl']))
			return ['ok'=>1];

		return ['err'=>1];
		Yii::info($post['fl'],'fl');

		///if ($post['act']=='set-files-weight' && !empty() )

	}
}