<?php

namespace lib;

class Rest
{
    /**
     * Send rest query to Bitrix24.
     *
     * @param       $method - Rest method, ex: methods
     * @param array $params - Method params, ex: array()
     * @param array $auth - Authorize data, ex: array('domain' => 'https://test.bitrix24.com', 'access_token' => '7inpwszbuu8vnwr5jmabqa467rqur7u6')
     *
     * @return mixed
     */
    public static function restCommand($method, array $params = array(), array $auth = array())
    {
        $queryUrl = $auth['client_endpoint'] . $method;
        $queryData = http_build_query(array_merge($params, array('auth' => $auth['access_token'])));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);
        return $result;
    }
}