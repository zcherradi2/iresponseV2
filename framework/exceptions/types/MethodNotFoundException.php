<?php declare(strict_types=1); namespace IR\Exceptions\Types; if (!defined('IR_START')) exit('<pre>No direct script access allowed</pre>');
/**
 * @framework       iResponse Framework 
 * @version         1.0
 * @author          Amine Idrissi <contact@iresponse.tech>
 * @date            2019
 * @name            MethodNotFoundException.php	
 */

# exceptions
use IR\Exceptions\IRException as IRException;

/**
 * @name MethodNotFoundException
 * @description an exception type class
 */
class MethodNotFoundException extends IRException
{}


