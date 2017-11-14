<?php
namespace AppBundle\Utils;

use Symfony\Component\HttpFoundation\Request;

class ApiUtils
{
    /**
     * @param array $params
     * @param Request $request
     */
    public static function checkRequired(array $params, Request $request)
    {
        foreach ($params as $key) {
            if (!$request->get($key)) {
                ResponseError::force(ResponseError::MISSED_INPUT_PARAMETER, 'You missed parameter "' . $key . '"');
            }
        }
    }
}
