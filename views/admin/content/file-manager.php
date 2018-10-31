<?php 
// подготовка ка загрузке ...

/*defined('FM_ROOT_PATH') || define('FM_ROOT_PATH', $root_path);
defined('FM_ROOT_URL') || define('FM_ROOT_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . (!empty($root_url) ? '/' . $root_url : ''));
defined('FM_SELF_URL') || define('FM_SELF_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . $_SERVER['PHP_SELF']);*/

//Yii::info(Yii::$aliases,'aliases');;


?>
<h1>Файловый менеджер</h1>
<div class="site-file-manager">
<?php 
require_once \Yii::getAlias('@app/components/filemanager.php');
?>
</div>