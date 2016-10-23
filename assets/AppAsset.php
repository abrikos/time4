<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = ['position' => View::POS_HEAD];
    public $css = [
        'css/site.css',
        'css/paper-theme.css',
        'css/jquery.datetimepicker.css',
    ];
    public $js = [
        'js/master-stack.js',
        'js/reserve-stack.js',
        'js/expense.js',
        'js/income.js',
        'js/sale.js',
        'js/shift.js',
        'js/haircut.js',
        'js/tmpl.min.js',
        'js/total-table.js',
        'js/jquery.datetimepicker.full.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
