<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 24.8.17
 * Time: 12.50
 */

namespace pamfax\api;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface {

    /** @param \yii\web\Application $app */
    public function bootstrap( $app ) {

        $app->i18n->translations['pamfax*'] = require( __DIR__ . '/locale/translate.php' );

        require_once( dirname(__FILE__) . "/lib/apiclient.class.php");
        require_once( dirname(__FILE__) . "/lib/apierror.class.php");
        require_once( dirname(__FILE__) . "/lib/apilist.class.php");
        require_once( dirname(__FILE__) . "/lib/errorcode.class.php");

    }

}