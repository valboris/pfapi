<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 30.8.17
 * Time: 15.29
 */

use \pamfax\api\Api;
use \pamfax\api\PamfaxApiException;

return [
    PamfaxApiException::DEFAULT_MESSAGE => "",
    PamfaxApiException::RESULT_NOT_FOUND => "",
    PamfaxApiException::RESULT_TYPE_NOT_FOUND => "",
    PamfaxApiException::INVALID_FORMAT => "",
    Api::ERROR_DEFAULT => '',
    Api::ERROR_INVALID_TYPE => "",
    Api::ERROR_INVALID_CONFIG => "",
    Api::ERROR_INVALID_PROFILE => "",
    Api::ERROR_CONFIG_KEY_NOT_FOUND => "",
    Api::ERROR_EMAIL_IN_USE => "",
    Api::ERROR_COMMAND_NOT_SUPPORTED => "",
    Api::ERROR_FORMAT_NOT_SUPPORTED => "",
    Api::ERROR_NOT_FOUND_RESPONSE_FIELD => ""
];