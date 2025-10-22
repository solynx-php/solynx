<?php
namespace app\core\exceptions;
use Exception;

class NotFoundException extends Exception
{
    protected $code = 404;
    protected $message = '404 Page Not Found';
}
