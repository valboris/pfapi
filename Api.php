<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 24.8.17
 * Time: 13.39
 */

namespace pamfax\api;
use Yii;
use yii\helpers\Json;
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

    /** @var string $responseFormat -  public output format type: */
    public $responseFormat = 'json';
    /** @var int $_responseFormatCode -  private output format code: */
    private $_responseFormatCode;

    /** @var int $_status -  api runtime status: */
    private $_status = 0;

    /** @var array $_map - api full map */
    private $_map;

    const MAP_KEY = 'api-map';
    const MAP_COMMAND = 'Map';

    const ACTION_VERIFY_USER = 'Session/VerifyUser';
    const ACTION_NUMBER_INFO = 'NumberInfo/GetNumberInfo';
    const ACTION_CURRENCY = 'Common/GetFormattedPrice';
    const ACTION_LOGIN_IDENTIFIER = 'Session/CreateLoginIdentifier';
    const ACTION_CREATE_USER = 'UserInfo/CreateUser';
    const ACTION_VERIFY_PASSWORD = 'UserInfo/VerifyPassword';
    const ACTION_RESET_PASSWORD = 'UserInfo/SendPasswordResetMessage';
    const ACTION_CHECK_UNIQUE = 'UserInfo/ValidateNewUsername';
    const ACTION_PROFILE = 'UserInfo/ListProfiles';

    const CODE_SUCCESS = 'success';
    const CODE_NOT_FOUND = 'not_found';
    const CODE_INVALID_PASSWORD = 'bad_password';
    const CODE_USER_ALREADY_EXIST = 'user_email_already_exist';

    const PARAM_TOKEN = 'usertoken';
    const PARAM_USERNAME = 'username';
    const PARAM_NUMBER = 'faxnumber';
    const PARAM_LANGUAGE = 'language_code';
    const PARAM_PASSWORD = 'password';
    const PARAM_TIME = 'timetolifeminutes';

    const RESPONSE_IDENTIFIER = 'UserIdentifier';
    const RESPONSE_USER = 'User';
    const RESPONSE_PROFILE = 'UserProfile';
    const RESPONSE_TOKEN = 'UserToken';
    const RESPONSE_CURRENCY = 'CurCode';
    const RESPONSE_PRICE = 'Price';
    const RESPONSE_STRINGS = 'PortalStrings';

    const I18N_ERROR = 'pamfax/api/error';

    const ERROR_DEFAULT = 'Sorry, we have some error by Pamfax API request';
    const ERROR_INVALID_TYPE = "Pamfax API must have static or instance type only.";
    const ERROR_INVALID_CONFIG = "Pamfax API config required key, secret, and url fields or active profile config.";
    const ERROR_INVALID_PROFILE = "Pamfax API config for profile '{0}' not found!";
    const ERROR_CONFIG_KEY_NOT_FOUND = "Pamfax API '{key}' config not found for profile '{profile}'!";
    const ERROR_EMAIL_IN_USE = 'This email already in use by Pamfax API! Please, try another';
    const ERROR_COMMAND_NOT_SUPPORTED = "Command {0} which this params not supported by Byfax API";
    const ERROR_FORMAT_NOT_SUPPORTED = "Not supported response format type: {0}";
    const ERROR_NOT_FOUND_RESPONSE_CURRENCY = 'Currency data not found in pamfax response!';
    const ERROR_NOT_FOUND_RESPONSE_USER = 'User data not found in pamfax response!';
    const ERROR_NOT_FOUND_RESPONSE_TOKEN = 'User token not found in Pamfax API response!';
    const ERROR_NOT_FOUND_RESPONSE_PROFILE = 'User profile not found in Pamfax API response';
    const ERROR_NOT_FOUND_RESPONSE_IDENTIFIER = 'User identifier not found in Pamfax API response';

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
                    Yii::t( self::I18N_ERROR, self::ERROR_INVALID_TYPE )
                );
            break;
        }

        $this->setResponseFormatCode();

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
        $GLOBALS['PAMFAX_API_MODE'] = $this->_responseFormatCode;
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
                Yii::t( self::I18N_ERROR, self::ERROR_COMMAND_NOT_SUPPORTED, $command )
            );

        // prepare environments before request:
        $this->start();

        // check cache flag:
        $useCache = ( !is_null( $useCache ) && is_bool( $useCache ) )?
                        $useCache : $this->useCache;

        // run request:
        $raw = \ApiClient::StaticApi( $command, $params, $useCache );
        // parse response:
        $response = $this->parseResponse( $raw );
        // check and unset response result:
        $this->checkResult( $response );

        return $response;

    }

    /**
     * Check and unset response result
     *
     * @param $response
     * @throws PamfaxApiException
     */
    private function checkResult( &$response ) {

        // check result field not empty:
        if( empty( $response['result'] ) )
            throw new PamfaxApiException( PamfaxApiException::RESULT_NOT_FOUND );

        $result = $response['result'];
        unset( $response['result'] );

        // check result type field not empty:
        if( !empty( $result['code'] ) && empty( $result['type'] ) )
            switch( $result['code'] ) {
                case '500' :
                case '403' :
                    $result['type'] = 'error'; break;
                case 'success' : $result['type'] = true; break;
                default:
                    throw new PamfaxApiException( PamfaxApiException::RESULT_TYPE_NOT_FOUND );
                    break;
            }

        // check result for error:
        if( $result['type'] === 'error' )
            throw new PamfaxApiException( $result );

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
     * Parse api response to configured format:
     *
     * @param string $raw
     * @return array|mixed
     * @throws PamfaxApiException
     */
    private function parseResponse( string $raw ) {

        switch( $this->_responseFormatCode ) {
            case \ApiClient::API_MODE_JSON:
                $response = Json::decode( $raw );
                break;
            case \ApiClient::API_MODE_XML:
                $response = \ApiClient::ParseXmlResult( $raw );
                break;
            case \ApiClient::API_MODE_OBJECT:
                $response = $raw;
                break;
            default:
                throw new PamfaxApiException( PamfaxApiException::INVALID_FORMAT );
                break;
        }

        return $response;

    }

    /**
     * Set response format code from config field
     *
     * @throws NotSupportedException
     */
    private function setResponseFormatCode() {

        switch( $this->responseFormat ) {
            case 'json':
                $this->_responseFormatCode = \ApiClient::API_MODE_JSON;
                break;
            case 'xml':
                $this->_responseFormatCode = \ApiClient::API_MODE_XML;
                break;
            case 'object':
                $this->_responseFormatCode = \ApiClient::API_MODE_OBJECT;
                break;
            default:
                throw new NotSupportedException(
                    Yii::t( self::I18N_ERROR, self::ERROR_FORMAT_NOT_SUPPORTED, $this->responseFormat )
                );
                break;
        }
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
                    Yii::t( self::I18N_ERROR, self::ERROR_INVALID_CONFIG )
                );
            } else {
                $this->activeProfile = 'default';
                return $this;
            }

        if( empty( $this->profiles[ $this->activeProfile ] ) )
            if( !$this->validateProfile() ) {
                throw new InvalidConfigException(
                    Yii::t( self::I18N_ERROR, self::ERROR_INVALID_PROFILE, $this->activeProfile )
                );
            } else
                return $this;

        $mode = $this->profiles[ $this->activeProfile ];

        foreach($this->_profileTemplate as $key ) {
            if( !empty( $mode[ $key ] ) )
                $this->$key = $mode[ $key ];
            if( empty( $this->$key ) )
                throw new InvalidConfigException(
                    Yii::t(
                        self::I18N_ERROR,
                        self::ERROR_CONFIG_KEY_NOT_FOUND,
                        [ 'key' => $key, 'profile' => $this->activeProfile ]
                    )
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