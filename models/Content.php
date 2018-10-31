<?php 

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\helpers\Html;

class Content extends \yii\base\Model
{
	public $cid ;//Ключик контента
	public $title; //Заголоовк
	public $type;// 'тип контента: новости статьи альбомы
	public $alias;// 'Алиас'
	public $aliasisuser=0;  /// автогенерация алиаса 
	public $owner; // 'Кто создал',
    public $status=1; // Статус
	public $body;// Содержимое
	public $teaser;// Содержимое
	public $created;// Создан
	public $nomenuitem; // без пункта меню 

	const TeaserSize=12;/// число слов попадающих в тизер .. из body


	const ContentTypes=['page','new','album'];
	const ContentTypesNames=['page'=>'Страница','new'=>'Новость','album'=>'Альбом'];
	const IMG_EXT=['png','jpg'];
	// число картинок ..  0 = неограниченно
	const ContentTypesFileCount=['page'=>1,'new'=>1,'album'=>100];

	
	const SCENARIO_EDIT='edit-content';
	const SCENARIO_KILL='kill-content';


	public function scenarios(){
		$s=parent::scenarios();
		$s[static::SCENARIO_EDIT]=['cid','title','type','alias','status','body','teaser','aliasisuser','nomenuitem'];
		$s[static::SCENARIO_KILL]=['cid'];
		return $s;
	}

	public function init()
	{
		if (empty($this->cid)){ // для новых элментов ..
			$this->nomenuitem=true;
		}

	}
	public function attributeLabels()
	{
		return [
			'title'=>'Заголовок',
			'body'=>'Содержимое',
			'teaser'=>'Анонс',
			'status'=>'Опублковано',
			'aliasisuser'=>'Ползовательский алиас',
			'alias'=>'Алиас',
			'file'=>'Картнка',
			'nomenuitem'=>'без пункта меню',
		];
	}
	public function attributeHints()
	{
		return [
			'alias'=>'Для клавной страницы укажите "front-page"',
			'body'=>'в контенте доступны следующие токены: %%title%% = вывести заголовок; %%img[:preset]%% вывести первую загруженую картинку через пресет/или оригинальную' 
		];
	}
	

	public function rules()
	{
		$rules=[
			[['title','type'],'required'],
			['body',$this->type=='album'?'safe':'required'],
			['title',\yii\validators\StringValidator::className(),'max'=>100],
			['title','aliasvalidate'],
			['type','yii\validators\RangeValidator','range'=>static::ContentTypes],
			['teaser','safe'],
			
			['cid','\yii\validators\DefaultValueValidator','value'=>0],
			['cid','\yii\validators\NumberValidator','min'=>0],
			['status','yii\validators\BooleanValidator'],
			
			['aliasisuser','\yii\validators\BooleanValidator'],
			['alias','yii\validators\StringValidator','max'=>120],
			['nomenuitem','yii\validators\BooleanValidator'],
		];
		if ($this->type=='album'){
			$rules[]=['file','required'];
		}
		return $rules;
	}
	// проверка алиаса на совпадение .. 
	public function aliasvalidate($attr,$params=[])
	{
		$alias=\URLify::filter($this->$attr);
	}
	// загрузка .  контента..  
	public static function findById($id=0)
	{
		if ($id && $d=(new Query())->select('*')->from('content')->where(['cid'=>$id])->limit(1)->one())
			return new static($d);
		if (empty($id))
			return new static();
		return null;
		
	}
	// запрос заголовков по id 
	public static function findperids($ids=[])
	{
		if (!$ids)
			return [];
		$q= (new Query())->select(['cid','title','type'])->from('content')->where(['cid'=>$ids])->indexBy('cid')->all();
		foreach($q as $x=>$y)
			if ($y)

			$q[$x]=new static ($y);
		
		return $q;
	}
	// загрузка по алиасу .. 
	public static function loadByAlias($path)
	{
		$path=array_reverse(explode('/',$path));
		if (isset($path[1]) && in_array($path[1],['news','albums']))
			array_pop($path);
		$q=(new Query());
		$q->select('c0.*')->from(['c0'=>'content']);

		$q->andWhere(['c0.alias'=>$path[0]]);
		if (count($path)>1){
			$q->leftjoin(['m0'=>'menuitems'],'m0.contentid=c0.cid');
			$q->leftjoin(['m1'=>'menuitems'],'m0.pmid=m1.mid');
			$q->leftJoin(['c1'=>'content'],'m1.contentid=c1.cid');
			$q->andWhere(['c1.alias'=>$path[1]]);
		}
			
		$d=$q->limit(1)->one();

		if (!$d)
			return null;
		return new Content($d);
	}
	


	
	// сохранение контента .. 
	public function save($data)
	{
		$this->setScenario(static::SCENARIO_EDIT);
		if ($this->load($data) && $this->validate()){

			if (!empty($this->aliasisuser) && !empty($this->alias))
				$this->alias=\URLify::filter($this->alias);
			else
				$this->alias=\URLify::filter($this->title);
			 
			$data=$this->attributes;
			foreach($data as $x=>$y)
				if (!isset($y))
					unset($data[$x]);

			$data['created']=time();
			$data['owner']=Yii::$app->user->id;

			if ($this->cid)
				unset($data['created'],$data['owner']);
			
			unset($data['cid']);
			
			if ($this->cid)// старый  элемент ..
				Yii::$app->db->createCommand()->update('content',$data,['cid'=>$this->cid])->execute();
			else{
				Yii::$app->db->createCommand()->insert('content',$data)->execute();
				$this->cid=Yii::$app->db->lastInsertId;
			}
			
			return true;
		}
		return false;
	}
	/// удаление ... 
	public function kill ($data)
	{
		$this->setScenario(static::SCENARIO_KILL);
		if ($this->load($data) && $this->validate() && $this->cid){
			Yii::$app->db->createCommand()->delete('content',['cid'=>$this->cid])->execute();

			Yii::$app->session->AddFlash('info','Удалено '.$this->cid);
			return true;
		}
		return false;
	}

	/**
	* Запрос в базу на просмотр контента ... 
	*/
	public static function contentquerylist($type)
	{
		$list=(new Query())->select(['c.cid','c.title','c.created','c.status','c.type','owner'=>'u.name'])->from(['c'=>'content'])->leftJoin(['u'=>'users'],'c.owner=u.uid')->distinct(true);
		// пристыковываем меню 
		$list->andWhere(['c.type'=>$type]);
		$list->leftjoin(['m1'=>'menuitems'],'m1.contentid=c.cid');// текущий уровнь 
		$list->leftjoin(['m2'=>'menuitems'],'m2.mid=m1.pmid'); // ровень выше .. 
		$list->leftjoin(['c0'=>'content'],'c0.cid=m2.contentid'); // статья предок ....
		$list->addSelect(['calias'=>'concat(if(isnull(c0.alias),"",concat(c0.alias,"/")),c.alias)']);// 
		
		return $list;
	}

	// получаем список контента .. для вставки в меню 
	public static function getListptions($ct='')
	{
		$list=['Не указано'];
		$q=(new Query())->from('content')->select(['cid','title','type','status'])->orderBy('title',SORT_ASC);
		if ($ct)
			$q->whee(['type'=>$ct]);
		$q=$q->all();
		foreach($q as $v)
			$list[$v['cid']]=sprintf('%s (%s, %s)',$v['title'],$v['type'],$v['status']);// ContentTypesNames

		return $list;
	}

	/**
	* поготовка к выводу данных
	*
	**/
	public function preparebody()
	{
		if($this->type=='new')
			$this->body='<div class="content-img">%%img%%</div><h1>%%title%%</h1>'.$this->body;
		// ищем и заменяем токен title и image 
		if (preg_match('#%%title%%#',$this->body)){
			$this->body=preg_replace('#%%title%%#', $this->title, $this->body);

		}

		// ищем токены картинки ..
		while(preg_match('#%%img:?(.*?):?(\w*)%%#', $this->body,$params)){
			$files =\app\models\FilesModel::findFilesPerCid($this->cid);
			Yii::info($files,'$files');
			$plac='';
			if(!empty($files)){
				list($source,$fn,$preset)=$params;
				if (empty($fn))
					$fn=$files[0]['filename'];
				foreach($files as $x=>$f)
					if ($f['filename']==$fn){
						if(!$preset || $preset=='original')
							$plac=$f['url'];
						else
							$plac=Yii::$aliases['@filesUrl'].'/'.$preset.'/'.$f['filename'];		
					}

			}
			
			$this->body=str_replace($source, Html::img($plac), $this->body);
			//preg_replace('#%%img:?(.*?):?(\w*)%%#',Html::img($plac),$this->body);
		}

		
	}

	// запрос запроса .. на получение контента для блоков .. 
	public static function ContentForblocks($cids)
	{
		$q=(new Query())->select(['title','cid','alias','type'])->from('content')->indexBy('cid');
		if ($cids)
			$q->where(['in','cid',$cids]);
		return $q->all();
	}

	// список новостей ... 
	public function NewsList()
	{
		$res=(new Query())->select(['c.cid','c.title','c.teaser','c.body','created'=>'c.created','c.alias'])->from(['c'=>'content'])->where(['type'=>'new','status'=>1]);
		$res->groupby('c.cid');
		$res->leftjoin(['f'=>'files'],'f.cid=c.cid');
		$res->addSelect(['fn'=>'group_concat(f.filename)']);
		return $res;
	}

	// список альбомов ... 
	public function AlbumsList()
	{
		$res=(new Query())->select(['c.cid','c.title','c.teaser','c.body','c.created','c.alias'])->from(['c'=>'content'])->where(['type'=>'album','status'=>1]);
		$res->groupby('c.cid');
		$res->leftjoin(['f'=>'files'],'f.cid=c.cid');
		$res->addSelect(['fn'=>'group_concat(f.filename)']);

		return $res;
	}

	// получить последние N Новостей ... 
	public static function NewsListLast($count=0)
	{
		$res=(new Query())->from('content')->select(['cid','title','created','alias','teaser','body'])->where(['type'=>'new','status'=>1])->orderBy(['created'=>SORT_DESC])->indexBy('cid');
		if($count)
			$res->limit($count);
		$res=$res->all();
		foreach($res as $x=>$y){
			if (!trim($y['teaser']))
				$res[$x]['teaser']=\yii\helpers\StringHelper::truncateWords(strip_tags($y['body']),self::TeaserSize);
			unset($res[$x]['body']);
			$res[$x]['url']='/news/'.$y['alias'];
			unset($res[$x]['alias']);
		}
		return $res;

	}
}