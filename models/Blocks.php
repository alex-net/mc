<?php 

namespace app\models;

use \yii\db\Query;
use Yii;

class Blocks extends \yii\base\Model
{
	public $id;// id блока 
	public $content;// содержимое ///
	public $weight; // вес 
	public $status; // статус 
	public $cid;// сслка на контент 



	public function attributeLabels()
	{
		return[
			'content'=>'Содержимое блока',
			'status'=>'Активен',
			'cid'=>'Ссылка на контент',
		];
	}

	public function rules()
	{
		$rr=parent::rules();
		$rr[]=[['id','cid'],'\yii\validators\NumberValidator','min'=>0];

		$rr[]=['status','\yii\validators\BooleanValidator'];
		$rr[]=['content','required','when'=>function($m){return empty($m->cid);},'whenClient'=>'function(a,v){ return false;}'];
		
		return $rr;
	}

	public static function findById($id=0)
	{
		if (!$id)
			return new static(['weight'=>0,'status'=>1]);
		
		$q=(new Query())->select('*')->from('blocks')->limit(1)->where(['id'=>$id])->one();
		if ($q)
			return new static($q);
		return null;
	}

	public static function blockslist($publishedonly=false)
	{
		$bl=[];
		$q=(new Query())->select(['id','content','status','cid'])->from('blocks')->orderBy(['weight'=>SORT_ASC ])->indexBy('id');
		if ($publishedonly)
			$q->where(['status'=>1]);
		$q=$q->all();
		foreach($q  as $x=>$y)
			$bl[$x]=new static($y);
		return $bl;
	}

	/**
	* сохранение 
	**/
	public function save($post)
	{
		if ($this->load($post) && $this->validate()){
			$data=$this->attributes;
			unset($data['id']);
			if (empty($this->id)){ // новый элемент 
				Yii::$app->db->createCommand()->insert('blocks',$data)->execute();
			}
			else
				Yii::$app->db->createCommand()->update('blocks',$data,['id'=>$this->id])->execute();
			Yii::$app->session->addFlash('info','Данные блока сохранены');
			return true;
		}
		return false;
	}

	// удаление блока
	public function kill($post)
	{
		if ($this->load($post) && $this->validate(['id'])){
			Yii::$app->db->createCommand()->delete('blocks',['id'=>$this->id])->execute();
			Yii::$app->session->addFlash('info',sprintf('Блок %d удалён',$this->id));
			return true;
		}
		return false;
	}
	//  обновить информацию по статусам .и  всам блоков 
	public static function updateblocksdata($bds=[])
	{
		Yii::info($bds,'$bds');
		foreach($bds as $b)
			Yii::$app->db->createCommand()->update('blocks',['weight'=>intval($b['weight']),'status'=>!empty($b['active'])],['id'=>$b['id']])->execute();
		return true;
	}

}