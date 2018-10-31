<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'fas/css/font-awesome.min.css',
        'js/slick/slick.css',
        'js/slick/slick-theme.css',
        'css/style.css',
    ];
    public $js = [
        //'js/jquery-1.8.3.min.js',
        'js/slick/slick.min.js',
        'js/script.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\FancyAsset',
        //'rmrevin\yii\fontawesome\AssetBundle',  
        //'app\assets\FASBundle',
        //'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
