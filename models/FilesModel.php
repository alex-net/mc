<?php 

namespace app\models;

use Yii;
use yii\helpers\FileHelper;
use yii\db\Query;

class FilesModel extends \yii\base\Model
{
	const IMG_EXT=['png','jpg','jpeg'];
	// число картинок ..  0 = неограниченно
	const ContentTypesFileCount=['page'=>1,'new'=>1,'album'=>0];

	public $ct;// тип контента ..

	public $files; // загружаемые файлы .

	public $filename;// имя коечного файла... 
	public $cid;
	public $weight; // вес файла в списке ...


	public function attributeLabels()
	{
		return [
			'files'=>'Картинк'.(self::ContentTypesFileCount[$this->ct]==0?'и':'а'),
		];
	}

	public function rules()
	{
		return [
			['files','yii\validators\ImageValidator','extensions'=>self::IMG_EXT,'maxFiles'=>self::ContentTypesFileCount[$this->ct]],
		];
	}


	public function save($data,$cid)
	{
		if ($this->load($data) && $this->validate()){
			// пробуем залить файл ..
			Yii::info($_FILES,'FILES1');
			if ($this->ct=='album')
				$fil=\yii\web\UploadedFile::getInstances($this,'files');
			else
				$fil=[\yii\web\UploadedFile::getInstance($this,'files')];
			$path=Yii::$aliases['@files'].'/'.$this->ct;
			if (!file_exists($path))
				FileHelper::createDirectory($path);
			foreach($fil as $k=>$f)
				if ($f){
					/// сохраняем файл ..
					$fn=$cid.'-'.time().'-'.$k.'.'.$f->extension;
					if ($f->saveAs($path.'/'.$fn))// записываем файлик в базу ..
						Yii::$app->db->createCommand()->insert('files',['cid'=>$cid,'ct'=>$this->ct,'filename'=>$fn])->execute();
				}

			Yii::$app->session->addFlash('info','Фал был загружен');
			return true;
		}
		return false;
	}
	// удаление одного файла .. 
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
	*/
	public static function findFilesPerCid($cid)
	{
		$q=(new Query())->select(['ct','filename'])->from('files')->where(['cid'=>$cid])->orderBy(['weight'=>SORT_ASC])->all();
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
}
