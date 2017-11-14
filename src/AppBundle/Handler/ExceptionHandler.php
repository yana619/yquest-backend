<?php
namespace AppBundle\Handler;

use FOS\RestBundle\Serializer\Normalizer\ExceptionHandler as FosExceptionHandler;
use JMS\Serializer\Context;

class ExceptionHandler extends FosExceptionHandler
{
    /**
     * @param \Exception $exception
     * @param Context $context
     * @return array
     */
    protected function convertToArray(\Exception $exception, Context $context)
    {
        $data = [];

        $templateData = $context->attributes->get('template_data');

        if ($templateData->isDefined()) {
            $data['status'] = $code = $exception->getCode();
        }

        $data['response'] = $exception->getMessage();

        return $data;
    }
}
