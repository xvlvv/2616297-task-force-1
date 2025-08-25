<?php

namespace Xvlvv\Exception;

use Throwable;
use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;

class InvalidActionForTaskException extends \DomainException
{
    public function __construct(Action $action, Status $status, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf(
            'Invalid action for task "%s": %s',
            $action->getName(),
            $status->getName(),
        );
        parent::__construct($message, $code, $previous);
    }
}