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
    public $application;
    // api secret password:
    public $secret;
    // mode of api - diff mods use diff urls:
    public $mode = 'sandbox';
    // api urls for diff mods:
    public $urls = [];

    public function __construct( $config  = [] ) {

        if( empty( $config['application'] ) || empty( $config['secret'] ) )
            throw new InvalidConfigException(
                "This fields is required for Byfax API configure: application, secret."
            );

        parent::__construct( $config );
    }

    public function init() {

        if( empty( $this->urls[ $this->mode ] ) )
            throw new InvalidConfigException(
                "ByFax API url not found for mode '$this->mode'."
            );

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

    private function useStatic() {
        foreach( glob(dirname(__FILE__)."/static/*") as $f ) require_once( $f );
    }

    private function useInstance() {
        foreach( glob(dirname(__FILE__)."/instance/*") as $f ) require_once( $f );
    }

}