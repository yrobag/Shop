<?php

namespace Shop\Exceptions;

use Exception;

/**
 * Custom Exception. Should be thrown when something try to remove more products
 * from stock that are avalible.
 */
class NotEnoughProductQtyException extends Exception
{
    
}
