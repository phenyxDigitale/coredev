<?php

/**
 * Class AbstractLogger
 */
abstract class AbstractLogger {

    // @codingStandardsIgnoreStart
    public $level;
    protected $level_value = [
        0 => 'DEBUG',
        1 => 'INFO',
        2 => 'WARNING',
        3 => 'ERROR',
    ];
    // @codingStandardsIgnoreEnd

    const DEBUG = 0;
    const INFO = 1;
    const WARNING = 2;
    const ERROR = 3;

    /**
     * AbstractLoggerCore constructor.
     *
     * @param int $level
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct($level = self::INFO) {

        if (array_key_exists((int) $level, $this->level_value)) {
            $this->level = $level;
        } else {
            $this->level = static::INFO;
        }

    }

    /**
     * Check the level and log the message if needed
     *
     * @param string $message
     * @param int    $level
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function log($message, $level = self::DEBUG) {

        if ($level >= $this->level) {
            $this->logMessage($message, $level);
        }

    }

    /**
     * Log a debug message
     *
     * @param string $message
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function logDebug($message) {

        $this->log($message, static::DEBUG);
    }

    /**
     * Log an info message
     *
     * @param string $message
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function logInfo($message) {

        $this->log($message, static::INFO);
    }

    /**
     * Log a warning message
     *
     * @param string $message
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function logWarning($message) {

        $this->log($message, static::WARNING);
    }

    /**
     * Log an error message
     *
     * @param string $message
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function logError($message) {

        $this->log($message, static::ERROR);
    }

    /**
     * Log the message
     *
     * @param string message
     * @param level
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    abstract protected function logMessage($message, $level);
}
