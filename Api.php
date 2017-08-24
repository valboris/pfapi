<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 24.8.17
 * Time: 13.39
 */

namespace byfax\api;


class Api extends \yii\base\Object {

    public $type = 'static';

    public function init() {
        switch( $this->type ) {
            case 'static': $this->useStatic(); break;
            case 'instance': $this->useInstance(); break;
            default: throw new \yii\base\InvalidConfigException; break;
        }
    }

    private function useStatic() {
        foreach( glob(dirname(__FILE__)."/static/*") as $f ) require_once( $f );
    }

    private function useInstance() {
        foreach( glob(dirname(__FILE__)."/instance/*") as $f ) require_once( $f );
    }

}