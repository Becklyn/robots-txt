<?php declare(strict_types=1);

namespace Becklyn\RobotsTxt\Exception;



class RobotsTxtException extends \InvalidArgumentException
{
    /**
     * @inheritdoc
     */
    public function __construct (string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
