<?php

namespace Xvlvv\Entity;

final class Customer
{
    public function __construct(
        private int $id,
        private int $failedTasksCount = 0,
    ) {

    }
}