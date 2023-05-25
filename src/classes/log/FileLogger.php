<?php

/**
 * Class FileLogger
 *
 * @since 1.9.1.0
 */
class FileLogger extends AbstractLogger {

    protected $filename = '';

    /**
     * Check if the specified filename is writable and set the filename
     *
     * @param string $filename
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setFilename($filename) {

        if (is_writable(dirname($filename))) {
            $this->filename = $filename;
        } else {
            $this->filename = '';
        }

    }

    /**
     * Log the message
     *
     * @return string
     *
     * @since    1.0.0
     * @version  1.0.0 Initial version
     */
    public function getFilename() {

        return $this->filename;
    }

    /**
     * Write the message in the log file
     *
     * @param string $message
     * @param int    $level
     *
     * @return bool True on success, false on failure.
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    protected function logMessage($message, $level) {

        if (!is_string($message)) {
            $message = print_r($message, true);
        }

        $formattedMessage = '*' . $this->level_value[$level] . '* ' . "\t" . date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";

        $result = false;
        $path = $this->getFilename();

        if ($path) {
            $result = (bool) file_put_contents($path, $formattedMessage, FILE_APPEND);
        }

        return $result;
    }

}
