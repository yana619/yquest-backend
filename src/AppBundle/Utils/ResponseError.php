<?php
namespace AppBundle\Utils;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseError
{
    // Bad request (HTTP code 400):
    const MISSED_INPUT_PARAMETER = 400001;

    // Unauthorized (HTTP code 401):
    const WRONG_AUTH_TOKEN = 401001;
    const WRONG_GOOGLE_TOKEN = 401002;

    // Not Found (HTTP code 404):
    const UNKNOWN_EVENT = 404001;
    const UNKNOWN_ANSWER = 404002;

    // Internal Server Error (HTTP code 500):
    const UNKNOWN_ERROR = 500000;

    protected static $dictionary = [
        // Bad request (HTTP code 400):
        self::MISSED_INPUT_PARAMETER => 'Missed input parameter',

        // Unauthorized (HTTP code 401):
        self::WRONG_AUTH_TOKEN => 'Wrong Authorization token',
        self::WRONG_GOOGLE_TOKEN => 'Wrong /inactive Google token',


        // Not Found (HTTP code 404):
        self::UNKNOWN_EVENT => 'Unknown Event',
        self::UNKNOWN_ANSWER => 'Unknown Answer',

        // Internal Server Error (HTTP code 500):
        self::UNKNOWN_ERROR => 'Unknown error',
    ];

    /**
     * @param int $code
     * @return string
     */
    public static function description($code)
    {
        return self::$dictionary[$code];
    }

    /**
     * @param $code
     * @param $description
     */
    public static function force($code, $description = null)
    {
        throw new HttpException(
            self::makeHttpStatusCode($code),
            ($description) ? $description : self::description($code),
            null,
            [],
            $code
        );
    }

    /**
     * @param $code
     * @return int
     */
    public static function makeHttpStatusCode($code)
    {
        return (int)($code / 1000);
    }
}
