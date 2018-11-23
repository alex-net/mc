<?php 

namespace app\models;

use Yii;
use yii\helpers\FileHelper;
use yii\db\Query;

class FilesModel extends \yii\base\Model
{
	// допусимые зазрегения файлов ..
	
	const IMG_EXT=['png','jpg','jpeg'];
	// число картинок ..  0 = неограниченно
	const ContentTypesFileCount=['page'=>1,'new'=>1,'album'=>0];

	public $ct;// тип контента ..

	public $files; // загружаемые файлы .

	public $filename;// имя коечного файла... 
	public $cid;
	public $weight; // вес файла в списке ...
	public $uid;// номер учётки юзера ..


	public function attributeLabels()
	{
		return [
			'files'=>'Картинк'.(self::ContentTypesFileCount[$this->ct]==0?'и':'а'),
		];
	}

	public function rules()
	{
		return [
			['files','image','extensions'=>self::IMG_EXT,'skipOnEmpty'=>false,'maxFiles'=>20],
			[['ct','cid','uid'],'required'],
			[['cid','uid'],'integer'],
		];
	}

	/**
	 * сохранение файлов в базу . 
	 */
	public function save()
	{
		
		if (!$this->validate())
			return false;
		
		// id текущего пользователя .. 
		$uid=Yii::$app->user->id;
		// каталог загрузки контента ...
		$path=Yii::$aliases['@files'].'/'.$this->ct;

		if (!file_exists($path))
			FileHelper::createDirectory($path);
		
		
		$t=time();
		foreach($this->files as $k=>$f)
			if ($f){
				/// сохраняем файл ..
				$fn=$uid.'-'.$t.'-'.$k.'.'.$f->extension;
				if ($f->saveAs($path.'/'.$fn))// записываем файлик в базу ..
					Yii::$app->db->createCommand()->insert('files',[
						'cid'=>$this->cid,
						'ct'=>$this->ct,
						'filename'=>$fn,
						'uid'=>$uid
					])->execute();
			}

		Yii::$app->session->addFlash('info','Файл был загружен');
		return true;
		
	}
	/**
	 * удаление текущего элемента (файла) .. 
	 */
	public function kill()
	{
		$path=Yii::$aliases['@files'].'/'.$this->ct.'/'.$this->filename;
		// удалить файл физически ... 
		if (file_exists($path))
			unlink($path);
		// тереть запись .. 
		Yii::$app->db->createCommand()->delete('files',['filename'=>$this->filename])->execute();
	}

	/**
	* получить список файлов для заданного контента ..
	* @param int $cid  идентификатор сущности контента
	* 
	*/
	public static function findFilesPerCid($cid)
	{
		$q=(new Query())->select(['ct','filename'])->from('files')->where(['cid'=>$cid])->orderBy(['weight'=>SORT_ASC])->all();
		Yii::info($q,'files uploaded');
		$path=Yii::$aliases['@filesUrl'];
		foreach($q as $x=>$y){
			$q[$x]['url']=$path.'/'.$y['ct'].'/'.$y['filename'];
			$fp=Yii::$aliases['@files'].'/'.$y['ct'].'/'.$y['filename'];
			$q[$x]['isimage']=preg_match('#^image/#',FileHelper::getMimeType($fp));
		}
				
		return $q;
	}
	/**
	* загрузка файла . 
	**/
	public static function loadFile($fn)
	{
		// запрос .. 
		$q=(new Query())->select('*')->from('files')->where(['filename'=>$fn])->limit(1)->one();
		if ($q)
			return new static($q);
		return null;

	}
	/**
	*	 получить папку с файлом ..
	**/
	public static function filefolder($fn)
	{
		return (new Query())->select('ct')->from('files')->where(['filename'=>$fn])->limit(1)->scalar();
	}

	/**
	* уделение файла ..
	**/
	public static function killpercid($cid)
	{
		// запрашивам файлы .. 
		$fl=(new Query())->select(['concat(ct,\'/\',filename)'])->from('files')->where(['cid'=>$cid])->column();
		foreach($fl as $f)
			@unlink(Yii::$aliases['@files'].'/'.$f);	
		// бахнуть записи
		Yii::$app->db->createCommand()->delete('files',['cid'=>$cid])->execute();

	}


	public static function filespercontentids($cids=[])
	{
		if (!$cids)
			return [];	
		$q=(new Query())->select(['fl'=>'group_concat(filename)','cid'])->from('files')->where(['cid'=>$cids])->groupBy('cid')->indexBy('cid')->all();
		foreach($q as $x=>$y){
			$y=explode(',',$y['fl']);
			$q[$x]=reset($y);
		}
		return $q;
	}

	// загнать веса для файлов .. 
	public static function filesetweight($fl=[])
	{
		if(empty($fl))
			return false;
		foreach(array_values($fl) as $x=>$y)
			Yii::$app->db->createCommand()->update('files',['weight'=>$x],['filename'=>$y])->execute();
		return true;

	}


	/**
	 * обнвить cid для файлов  (вновь созданный контент .) 
	 * @param integer $cid номр только что дабавленной сущности
	 *
	 * @return void
	 */
	public function updateCid($cid)
	{
		Yii::$app->db->createCommand()->update('files',['cid'=>$cid],['uid'=>Yii::$app->user->id,'cid'=>0])->execute();

	}
}
