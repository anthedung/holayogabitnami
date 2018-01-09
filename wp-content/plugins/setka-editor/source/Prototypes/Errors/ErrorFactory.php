<?php
namespace Setka\Editor\Prototypes\Errors;

class ErrorFactory {

    public static function createFromException(\Exception $exception) {
        $error = new Error(get_class($exception));
        $error->setData($exception);
        return $error;
    }
}
