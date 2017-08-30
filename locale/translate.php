<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 30.8.17
 * Time: 15.10
 */
use \pamfax\api\Api;

return [
        'class' => 'yii\i18n\PhpMessageSource',
        'basePath' => __DIR__,
        'fileMap' => [
            'pamfax'            => 'pamfax.php',
            'pamfax/api'        => 'api.php',
            Api::I18N_ERROR     => 'error.php',
        ]
];