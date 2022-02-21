<?php

namespace CVLB\Svc\Logging\Contracts;

interface LogService
{
    /**
     * @param string $message
     * @param int $level
     * @return string
     */
    function log(string $message, int $level): string;
}