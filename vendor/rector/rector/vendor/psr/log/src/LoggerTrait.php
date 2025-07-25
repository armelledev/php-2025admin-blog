<?php

namespace RectorPrefix202507\Psr\Log;

/**
 * This is a simple Logger trait that classes unable to extend AbstractLogger
 * (because they extend another class, etc) can include.
 *
 * It simply delegates all log-level-specific methods to the `log` method to
 * reduce boilerplate code that a simple Logger that does the same thing with
 * messages regardless of the error level has to implement.
 */
trait LoggerTrait
{
    /**
     * System is unusable.
     * @param string|\Stringable $message
     */
    public function emergency($message, array $context = []) : void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     * @param string|\Stringable $message
     */
    public function alert($message, array $context = []) : void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     * @param string|\Stringable $message
     */
    public function critical($message, array $context = []) : void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     * @param string|\Stringable $message
     */
    public function error($message, array $context = []) : void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     * @param string|\Stringable $message
     */
    public function warning($message, array $context = []) : void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }
    /**
     * Normal but significant events.
     * @param string|\Stringable $message
     */
    public function notice($message, array $context = []) : void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     * @param string|\Stringable $message
     */
    public function info($message, array $context = []) : void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }
    /**
     * Detailed debug information.
     * @param string|\Stringable $message
     */
    public function debug($message, array $context = []) : void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     *
     * @throws \Psr\Log\InvalidArgumentException
     * @param string|\Stringable $message
     */
    public abstract function log($level, $message, array $context = []) : void;
}
