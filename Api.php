<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 24.8.17
 * Time: 13.39
 */

namespace pamfax\api;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

class Api extends \yii\base\Object {

    /** @var string $type - type of included classes:*/
    public $type = 'static';
    /** @var string $key - active api key: */
    public $key;
    /** @var string $secret - active api secret: */
    public $secret;
    /** @var string $url -  active url */
    public $url;

    /** @var string $activeProfile - active profile for api: */
    public $activeProfile = 'default';
    /** @var array $profiles list of available profiles with keys, urls and secrets: */
    public $profiles = [];

    /** @var boolean $useCache - use cache in api requests by default */
    public $useCache = false;

    /** @var array $_profileTemplate -  template for profile configure: */
    private $_profileTemplate = [ 'key', 'secret', 'url' ];

    /** @var int $_formatMode -  output format mode: */
    private $_formatMode;

    /** @var int $_status -  api runtime status: */
    private $_status = 0;

    /** @var array $_map - api full map */
    private $_map;

    const MAP_KEY = 'api-map';
    const MAP_COMMAND = 'Map';

    /**
     * @inheritdoc
     *
     * @throws InvalidConfigException
     */
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

    /**
     *
     * Set api response format mode
     *
     * @param int $mode
     * @param bool $global
     */
    public function setFormatMode( $mode = \ApiClient::API_MODE_JSON, $global = false ) {
        $this->_formatMode = $mode;
        if( $global )
            $GLOBALS['PAMFAX_API_MODE'] = $this->_formatMode;
    }

    /**
     * Prepare api before request
     *
     * @return $this
     */
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

    /**
     * Run api request
     *
     * @param string $command
     * @param array $params
     * @param null $useCache
     * @return array|bool|mixed|string
     * @throws NotSupportedException
     */
    public function request( string $command, array $params = [], $useCache = null ) {

        // validate command and params before request:
        if( !$this->validateRequest( $command, $params ) )
            // todo throw more informative message
            throw new NotSupportedException(
                "Command $command which this params not supported by Byfax API"
            );

        // prepare environments before request:
        $this->start();

        // check cache flag:
        $useCache = ( !is_null( $useCache ) && is_bool( $useCache ) )?
                        $useCache : $this->useCache;

        // run request:
        return \ApiClient::StaticApi( $command, $params, $useCache );

    }

    /**
     * Retrieve api map
     *
     * @return array|bool|mixed|string
     */
    public function map() {

        if( !empty( $this->_map ) )
            return $this->_map;

        if( Yii::$app->cache->exists( self::MAP_KEY ) ) {
            $this->_map = Yii::$app->cache->exists( self::MAP_KEY );
            return $this->_map;
        }

        $map = $this->request( self::MAP_COMMAND );

        // todo validate response here before map caching

        Yii::$app->cache->set( self::MAP_KEY, $map );
        return $this->_map = $map;

    }

    /**
     * Validate api command and params before request
     *
     * @param string $command
     * @param array $params
     * @return bool
     */
    private function validateRequest( string $command, array $params ) {

        return ( empty( $command ) )? false : true; // todo validate by api full map

    }

    /**
     * Validate api profile for ready to use
     *
     * @return bool
     */
    private function validateProfile() {
        return !( empty( $this->key ) || empty( $this->secret ) || empty( $this->url ) );
    }

    /**
     * Configure api profile before use
     *
     * @return $this
     * @throws InvalidConfigException
     */
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

    /**
     * Initialize api libs by static classes
     */
    private function useStatic() {
        foreach( glob(dirname(__FILE__)."/static/*") as $f ) require_once( $f );
    }

    /**
     * Initialize api libs by instance classes
     */
    private function useInstance() {
        foreach( glob(dirname(__FILE__)."/instance/*") as $f ) require_once( $f );
    }

}