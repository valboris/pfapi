<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 24.8.17
 * Time: 13.39
 */

namespace byfax\api;
use yii\base\InvalidConfigException;

class Api extends \yii\base\Object {

    // type of included classes:
    public $type = 'static';
    // api application account:
    public $key;
    // api secret password:
    public $secret;
    // active url
    public $url;
    // active mode of api - diff mods use diff urls:
    public $activeMode = 'default';
    // list of available mods with keys, urls and secrets:
    public $mods = [];
    // template for mode configure:
    private $modeTemplate = [ 'key', 'secret', 'url' ];

    public function init() {

        $this->activeModeConfigure();

        switch( $this->type ) {
            case 'static': $this->useStatic(); break;
            case 'instance': $this->useInstance(); break;
            default:
                throw new InvalidConfigException(
                    "ByFax API must have static or instance type only."
                );
            break;
        }
    }

    private function validateMode() {
        return !( empty( $this->key ) || empty( $this->secret ) || empty( $this->url ) );
    }

    private function activeModeConfigure() {

        if( empty( $this->activeMode ) )
            if( !$this->validateMode() ) {
                throw new InvalidConfigException(
                    "ByFax API config required key, secret, and url fields or activeMode config."
                );
            } else {
                $this->activeMode = 'default';
                return $this;
            }

        if( empty( $this->mods[ $this->activeMode ] ) )
            if( !$this->validateMode() ) {
                throw new InvalidConfigException(
                    "ByFax API config for mode '$this->activeMode' not found!"
                );
            } else
                return $this;

        $mode = $this->mods[ $this->activeMode ];

        foreach( $this->modeTemplate as $key ) {
            if( !empty( $mode[ $key ] ) )
                $this->$key = $mode[ $key ];
            if( empty( $this->$key ) )
                throw new InvalidConfigException(
                    "ByFax API '$key' config not found for active mode '$this->activeMode'!"
                );
        }

        return $this;

    }

    private function useStatic() {
        foreach( glob(dirname(__FILE__)."/static/*") as $f ) require_once( $f );
    }

    private function useInstance() {
        foreach( glob(dirname(__FILE__)."/instance/*") as $f ) require_once( $f );
    }

}