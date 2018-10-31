<?php 

namespace app\models;

use yii\db\Query;
use Yii;

class Menu extends \yii\base\Model
{
	public $mid;// Ключик менюшки
    public $pmid;//Родительский элемент
    public $status;// Статус стрницы 
    public $title;// Заголовок пункта меню'
	public $weight; // Вес пункта меню
	public $contentid; // ссылка на еонтент ...
	public $url; // путь

	private $_children=[]; // дочерние пункты меню ..

	const SCENARIO_CREATE_FROM_CONTENT='menu-from-content';
	const SCENARIO_CREATE_FROM_MENU='manu-item-edit';
	const SCENARIO_KILL_PUNKT='menu-item-kill';


	public function init()
	{
		
		parent::init();
		/*$this->mid=0;
		$this->pmid='root';
		$this->status=1;
		$this->weight=0;*/
		// достать деток .. 
		$q=(new Query())->select('mid')->from('menuitems')->where(['pmid'=>$this->mid])->column();
		$this->_children=$q?$q:[];

	}

	public function scenarios()
	{
		$s=parent::scenarios();
		$s[static::SCENARIO_CREATE_FROM_CONTENT]=['mid','title','pmid'];
		$s[static::SCENARIO_CREATE_FROM_MENU]=['mid','title','status','url','contentid'];
		$s[static::SCENARIO_KILL_PUNKT]=['mid'];
		return $s;
	}
	public function attributeHints()
	{
		return [
			'url'=>'Актуально, если не указана ссылка на содержимое',
		]; 
	}
	public function attributeLabels()
	{
		return [
			'title'=>'Заголовок',
			'pmid'=>'Родительский элемент',
			'status'=>'Активен',
			'url'=>'Ссылка пукнта меню',
			'contentid'=>'Ссылка на содержимое',
		];
	}
	public function rules()
	{
		return [
			//['title','required','when'=>function($m){!empty($m->mid);}],
			['title','required','on'=>static::SCENARIO_CREATE_FROM_MENU],
			['title','yii\validators\StringValidator','max'=>128],
			['pmid','yii\validators\NumberValidator','min'=>0,
				'when'=>function($m){return $m->pmid!='root';},
				'whenClient'=>'function(a,v){return v!=="root";}',
			], // $m->pmid!='root';
			['mid','yii\validators\NumberValidator','min'=>0],
			//['mid','yii\validators\DefaultValueValidator','value'=>0],
			['contentid','yii\validators\NumberValidator','min'=>0,'on'=>static::SCENARIO_CREATE_FROM_MENU],
			['url','yii\validators\StringValidator','max'=>128,'on'=>static::SCENARIO_CREATE_FROM_MENU],
			['url','required','on'=>static::SCENARIO_CREATE_FROM_MENU,
				'when'=>function($m){return empty($m->contentid);},
				'whenClient'=>'function(a,v){return false;}',
			],
			['status','yii\validators\BooleanValidator','on'=>static::SCENARIO_CREATE_FROM_MENU],
		];
	}

	public function generatemMenuOptions()
	{
		$mlist=['root'=>'Корень'];
		Yii::info([$this->mid,$this->children],'mid ch');
		if (!$this->children){
			// собрать все пункты которые находятся на первом уровне.. 
			$q=(new Query())->select(['mid','title'])->from('menuitems')->where(['pmid'=>0]);
			if ($this->mid)// элемент не новый .. надо авыкинуть текщий 
				$q->andWhere(['!=','mid',$this->mid]);
			$q=$q->all();

			foreach($q as $v)
				$mlist[$v['mid']]=$v['title'];
		}

		
		return $mlist;
	}
	// насйти меню .. для элемента  контента
	public static function findByContent($cid)
	{
		if (empty($cid))
			return new static();
		$d=(new Query())->select('m1.*')->from(['m1'=>'menuitems'])->where(['m1.contentid'=>$cid])->limit(1);
		$d=$d->one();
		if (empty($d))
			return new static();
		return  new static($d);
	}
	// загржаем меню по идетификатору .
	public static function findById($mid)
	{
		$q=(new Query)->from('menuitems')->select('*')->where(['mid'=>$mid])->one();
		if($q)
			return new static($q);
		
		return new static ();

	}

	// узнать связанные элменты наследники 
	public function getChildren()
	{
		return $this->_children;
	}
	// вернуть ролителя .. 
	public function getParent()
	{
		return $this->pmid=='root'?0:$this->pmid;
	}
	// загрузка контентной менюшки .. 
	/*public function loadfromcontent($cid)
	{
		$mid=(new Query())->select('mid')->from('menuitems')->where(['contentid'=>$cid])->limit(1)->scalar();
		if ($mid)
			$this->loadById($mid);
		
	}*/
	// создание менюшки (пункта меню) 
	public function updateContentItemFromPost($post,$cid)
	{
		$this->scenario=static::SCENARIO_CREATE_FROM_CONTENT;
		if (!empty($cid) && $this->load($post) && $this->validate()){
			$this->contentid=intval($cid);
			$this->pmid=$this->pmid=='root'?0:intval($this->pmid);
			// создаём новый пункт меню 
			if (empty($this->mid) && !empty($this->title))
				Yii::$app->db->createCommand()->insert('menuitems',$this->attributes)->execute();
			// обновление пункта меню .. 
			if (!empty($this->mid)){
				$m=Menu::findById($this->mid);// текущий элемент ..
				
				Yii::info([$m->attributes,$m->children,$m->parent,$m->scenario],'test');

				$data=$this->attributes;
				foreach($data as $x=>$y)
					if (!isset($y))
						unset($data[$x]);

				unset($data['mid']);
				// нужно обновить пункт .. 
				if ($this->title)// надо обновить ...
					Yii::$app->db->createCommand()->update('menuitems',$data,'mid=:mid',[':mid'=>$this->mid])->execute();
				else {
					// удалить пункт...
					Yii::$app->db->createCommand()->delete('menuitems',['mid'=>$this->mid])->execute();
					// надо обновить потомков если есть 
					if ($m->children)
						Yii::$app->db->createCommand()->update('menuitems',['pmid'=>$m->parent],['mid'=>$m->children])->execute();
				}
			}
			// надо поискать ункт меню в базе .. по cid 
			Yii::trace($this->attributes,'menu values');
			return true;
		}
		return false;
	}

	public function killContentItem($data)
	{
		$this->scenario=static::SCENARIO_KILL_PUNKT;
		if ($this->load($data) && $this->validate() && $this->mid){
			// запросить данные о пункте меню ...
			$m=(new \yii\db\Query())->select(['m0.pmid'])->from(['m0'=>'menuitems'])->where(['m0.mid'=>$this->mid]);
			$d=$m->leftjoin(['m1'=>'menuitems'],'m1.pmid=m0.mid')->addSelect(['c'=>'group_concat(m1.mid)'])->limit(1)->one();
			if (!empty($d['c']))
				Yii::$app->db->createCommand()->update('menuitems',['pmid'=>$d['pmid']],['mid'=>explode(',',$d['c'])])->execute();
			// удалить пункт
			Yii::$app->db->createCommand()->delete('menuitems',['mid'=>$this->mid])->execute();
			Yii::$app->session->addFlash('info',sprintf('Элемент меню %d удалён',$this->mid));

			return true;
		}
		return false;

		
	}

	// менюшка полностьбю для рендринга 
	public static function fullmenulist($status=null)
	{
		$q=(new Query())->from(['m'=>'menuitems'])->select('m.*')->orderBy(['m.pmid'=>SORT_ASC,'m.weight'=>SORT_ASC])->indexBy('mid');
		if (isset($status))
			$q->andWhere(['m.status'=>intval($status)]);

		$q->leftjoin(['c'=>'content'],'c.cid=m.contentid')->addSelect(['ctitle'=>'c.title','calias'=>'c.alias']);

		$q=$q->all();
		foreach($q as $x=>$y){
			if (!empty($y['contentid']))
				$y['url']=$y['calias'];
			if (!empty($y['pmid'])){
				$y['url']=$q[$y['pmid']]['calias'].'/'.$y['url'];
				$q[$y['pmid']]['childs'][$x]=$y;
				unset($q[$x]);
			}
			else
				$q[$x]=$y;
		}
		
		return $q ;
	}


	// сохранение формы меню ... 
	public function saveitem($data)
	{
		$this->scenario=static::SCENARIO_CREATE_FROM_MENU;
		if ($this->load($data) && $this->validate() ){
			$data=$this->attributes;
			$mid=$data['mid'];
			unset($data['mid']);
			
			if (empty($mid))// новый элемент . 
				Yii::$app->db->createCommand()->insert('menuitems',$data)->execute();
			else
				Yii::$app->db->createCommand()->update('menuitems',$data,['mid'=>$mid])->execute();
			Yii::$app->session->addFlash('info','Пункт меню сохранён');
			return true;
		}
		
		return false;
	}

	// удаление элемента меню ...
	public function killitem($data)
	{
		if ($this->load($data) && $this->validate()){
			
			// обнвляем дочерним этементам родителя если есть ... 
			if ($this->children)
				Yii::$app->db->createCommand()->update('menuitems',['pmid'=>$this->parent],['mid'=>$this->children])->execute();
			// удаляем элемент ...
			Yii::$app->db->createCommand()->delete('menuitems',['mid'=>$this->mid])->execute();
			Yii::$app->session->addFlash('info','Элемент удалён');
			return true;
		}
		return false;
		
	}
}
?>