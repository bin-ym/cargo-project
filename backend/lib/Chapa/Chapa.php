<?php

namespace Chapa;

use Chapa\Model\PostData;
use Chapa\Model\ResponseData;
use Exception;

/**
 * The Chapa class is responsible for making GET and POST request to Chapa API
 * to initialize payment and verify transactions.
 */
class Chapa
{
    const baseUrl = 'https://api.chapa.co/';
    const apiVersion = 'v1';

    private $secreteKey;

    /**
     * @param string  $secreteKey A secrete key provided from Chapa.
     */
    function __construct($secreteKey)
    {
        $this->secreteKey = $secreteKey;
    }

    /**
     * Initialize payment
     * @param  PostData $postData
     * @return ResponseData
     */
    public function initialize($postData)
    {
        Util::validate($postData);

        $url = self::baseUrl . self::apiVersion . '/transaction/initialize';
        $data = $postData->getAsKeyValue();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send as JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix for localhost
        
        $headers = [
            'Authorization: Bearer ' . $this->secreteKey,
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        
        curl_close($ch);

        return new ResponseData($response, $statusCode);
    }

    /**
     * Verify transaction
     * @param string $transactionRef
     * @return ResponseData
     */
    public function verify($transactionRef)
    {
        if(empty($transactionRef)){
            throw new Exception("Transaction reference can't be null or empty");
        }

        $url = self::baseUrl . self::apiVersion . '/transaction/verify/' . $transactionRef;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix for localhost
        
        $headers = [
            'Authorization: Bearer ' . $this->secreteKey
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return new ResponseData($response, $statusCode);
    }
}
