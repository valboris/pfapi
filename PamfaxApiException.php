<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 25.8.17
 * Time: 14.49
 */

namespace pamfax\api;
use yii\base\Exception;

class PamfaxApiException extends Exception {

    /** @var mixed $responseCode  */
    public $responseCode;

    const DEFAULT_MESSAGE = "Pamfax API response with non comment error";

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName() {
        return 'Pamfax API Exception';
    }

    /**
     * PamfaxApiException constructor.
     *
     * @param string|array $response
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct( $response = self::DEFAULT_MESSAGE, $code = 0, \Throwable $previous = null ) {

        $message = !empty( $response['message'] )? $response['message'] :
            ( is_string( $response )? $response : self::DEFAULT_MESSAGE );
        if( !empty( $response['code'] ) ) {
            $this->responseCode = $response['code'];
            if( is_int( (int) $this->responseCode ) )
                $code = (int) $this->responseCode;
        }
        return parent::__construct( $message, $code, $previous );

    }

}