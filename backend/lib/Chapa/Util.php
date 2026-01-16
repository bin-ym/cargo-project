<?php

namespace Chapa;

use Chapa\Model\PostData;
use Exception;

/**
 * The Util class serves as a helper class for the main {@link Chapa} class.
 */
class Util
{
    /**
     *  @param PostData  $postData Instance of PostData class that contains post
     *                             fields to be validated.
     * @return void
     */
    public static function validate($postData)
    {
        if(!preg_match("/^([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(\.[0-9][0-9])?$/", $postData->getAmount())){
            throw new Exception('Invalid amount value. Amount must be in numerical format.');
        }

        if(!preg_match('/^([A-Za-z]{3})$/', $postData->getCurrency())){
            throw new Exception('Invalid currency value. Currency should match the following regex: ^([A-Z]{3})$');
        }

        // Relaxed name validation
        // if(!preg_match("/^[A-ZÀ-ÿa-z-,']*$/",  $postData->getFirstName()) || !preg_match("/^[A-ZÀ-ÿa-z-,']*$/",  $postData->getLastName())){
        //     throw new Exception('Invalid name format.');
        // }
        
        if(!filter_var($postData->getEmail(), FILTER_VALIDATE_EMAIL)){
            throw new Exception("Invalid email address.");
        }

        $callBackUrl = $postData->getCallbackUrl();
        if(!is_null($callBackUrl)) {
            if (!filter_var($callBackUrl, FILTER_VALIDATE_URL)) {
                throw new Exception("Invalid callback url.");
            }
        }
    }

    public static function generateToken($prefix = 'cp')
    {
        return $prefix . '-' . bin2hex(random_bytes(4)) . '-' . time();
    }
}
