<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 24.8.17
 * Time: 13.39
 */

namespace byfax\api;
use Yii;
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

    // active profile for api:
    public $activeProfile = 'default';
    // list of available profiles with keys, urls and secrets:
    public $profiles = [];

    // template for profile configure:
    private $_profileTemplate = [ 'key', 'secret', 'url' ];

    // output format mode:
    private $_formatMode;

    // api runtime status:
    private $_status = 0;

    public function init() {

        $this->profileConfigure();

        switch( $this->type ) {
            case 'static': $this->useStatic(); break;
            case 'instance': $this->useInstance(); break;
            default:
                throw new InvalidConfigException(
                    "ByFax API must have static or instance type only."
                );
            break;
        }

        $this->setFormatMode();

    }

    public function setFormatMode( $mode = \ApiClient::API_MODE_JSON, $global = false ) {
        $this->_formatMode = $mode;
        if( $global )
            $GLOBALS['PAMFAX_API_MODE'] = $this->_formatMode;
    }

    public function start() {

        if( $this->_status > 0 )
            return $this;

        $GLOBALS['PAMFAX_API_URL'] = $this->url;
        $GLOBALS['PAMFAX_API_APPLICATION'] = $this->key;
        $GLOBALS['PAMFAX_API_SECRET_WORD'] = $this->secret;
        $GLOBALS['PAMFAX_API_MODE'] = $this->_formatMode;
        $GLOBALS['PAMFAX_API_USERTOKEN'] = Yii::$app->session['UserToken'];

        $this->_status = 1;
        return $this;

    }

    private function validateProfile() {
        return !( empty( $this->key ) || empty( $this->secret ) || empty( $this->url ) );
    }

    private function profileConfigure() {

        if( empty( $this->activeProfile ) )
            if( !$this->validateProfile() ) {
                throw new InvalidConfigException(
                    "ByFax API config required key, secret, and url fields or active profile config."
                );
            } else {
                $this->activeProfile = 'default';
                return $this;
            }

        if( empty( $this->profiles[ $this->activeProfile ] ) )
            if( !$this->validateProfile() ) {
                throw new InvalidConfigException(
                    "ByFax API config for profile '$this->activeProfile' not found!"
                );
            } else
                return $this;

        $mode = $this->profiles[ $this->activeProfile ];

        foreach($this->_profileTemplate as $key ) {
            if( !empty( $mode[ $key ] ) )
                $this->$key = $mode[ $key ];
            if( empty( $this->$key ) )
                throw new InvalidConfigException(
                    "ByFax API '$key' config not found for profile '$this->activeProfile'!"
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