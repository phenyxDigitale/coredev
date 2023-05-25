<?php

/**
 * Class PhenyxException
 *
 * @since 1.9.1.0
 */
class PhenyxException extends Exception {

    protected $trace;

    /**
     * PhenyxExceptionCore constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     * @param array|null     $customTrace
     * @param string|null    $file
     * @param int|null       $line
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, $customTrace = null, $file = null, $line = null) {

        parent::__construct($message, $code, $previous);

        if (!$customTrace) {
            $this->trace = $this->getTrace();
        } else {
            $this->trace = $customTrace;
        }

        if ($file) {
            $this->file = $file;
        }

        if ($line) {
            $this->line = $line;
        }

    }

    /**
     * This method acts like an error handler, if dev mode is on, display the error else use a better silent way
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function displayMessage() {

        header('HTTP/1.1 500 Internal Server Error');

        if (_EPH_MODE_DEV_ || getenv('CI')) {
            // Display error message

            echo '<style>
                #ephException{font-family: Verdana; font-size: 14px}
                #ephException h2{color: #F20000}
                #ephException p{padding-left: 20px}
                #ephException ul li{margin-bottom: 10px}
                #ephException a{font-size: 12px; color: #000000}
                #ephException .psTrace, #ephException .psArgs{display: none}
                #ephException pre{border: 1px solid #236B04; background-color: #EAFEE1; padding: 5px; font-family: Courier; width: 99%; overflow-x: auto; margin-bottom: 30px;}
                #ephException .psArgs pre{background-color: #F1FDFE;}
                #ephException pre .selected{color: #F20000; font-weight: bold;}
            </style>';
            echo '<div id="psException">';
            echo '<h2>[' . str_replace('EphenyxShop', 'Ephenyx', get_class($this)) . ']</h2>';
            echo $this->getExtendedMessage();

            echo $this->displayFileDebug($this->file, $this->line);

            // Display debug backtrace
            echo '<ul>';

            foreach ($this->trace as $id => $trace) {
                $relativeFile = (isset($trace['file'])) ? ltrim(str_replace([_EPH_ROOT_DIR_, '\\'], ['', '/'], $trace['file']), '/') : '';
                $currentLine = (isset($trace['line'])) ? $trace['line'] : '';

                if (defined('_EPH_ROOT_DIR_')) {
                    $relativeFile = str_replace(basename(_EPH_ROOT_DIR_) . DIRECTORY_SEPARATOR, 'admin' . DIRECTORY_SEPARATOR, $relativeFile);
                }

                echo '<li>';
                echo '<b>' . ((isset($trace['class'])) ? $trace['class'] : '') . ((isset($trace['type'])) ? $trace['type'] : '') . $trace['function'] . '</b>';
                echo ' - <a style="font-size: 12px; color: #000000; cursor:pointer; color: blue;" onclick="document.getElementById(\'psTrace_' . $id . '\').style.display = (document.getElementById(\'psTrace_' . $id . '\').style.display != \'block\') ? \'block\' : \'none\'; return false">[line ' . $currentLine . ' - ' . $relativeFile . ']</a>';

                if (isset($trace['args']) && count($trace['args'])) {
                    echo ' - <a style="font-size: 12px; color: #000000; cursor:pointer; color: blue;" onclick="document.getElementById(\'psArgs_' . $id . '\').style.display = (document.getElementById(\'psArgs_' . $id . '\').style.display != \'block\') ? \'block\' : \'none\'; return false">[' . count($trace['args']) . ' Arguments]</a>';
                }

                if ($relativeFile) {
                    $this->displayFileDebug($trace['file'], $trace['line'], $id);
                }

                if (isset($trace['args']) && count($trace['args'])) {
                    $this->displayArgsDebug($trace['args'], $id);
                }

                echo '</li>';
            }

            echo '</ul>';
            echo '</div>';
        } else {
            header('Content-Type: text/plain; charset=UTF-8');
            // Display error message
            $markdown = '';
            $markdown .= '## ' . str_replace('EphenyxShop', 'Ephenyx', get_class($this)) . '  ';
            $markdown .= $this->getExtendedMessageMarkdown();

            $markdown .= $this->displayFileDebug($this->file, $this->line, null, true);

            // Display debug backtrace

            foreach ($this->trace as $id => $trace) {
                $relativeFile = (isset($trace['file'])) ? ltrim(str_replace([_EPH_ROOT_DIR_, '\\'], ['', '/'], $trace['file']), '/') : '';
                $currentLine = (isset($trace['line'])) ? $trace['line'] : '';

                if (defined('_EPH_ROOT_DIR_')) {
                    $relativeFile = str_replace(basename(_EPH_ROOT_DIR_) . DIRECTORY_SEPARATOR, 'content' . DIRECTORY_SEPARATOR, $relativeFile);
                }

                $markdown .= '- ';
                $markdown .= '**' . ((isset($trace['class'])) ? $trace['class'] : '') . ((isset($trace['type'])) ? $trace['type'] : '') . $trace['function'] . '**';
                $markdown .= " - [line `" . $currentLine . '` - `' . $relativeFile . "`]  \n";

                if (isset($trace['args']) && count($trace['args'])) {
                    $markdown .= " - [" . count($trace['args']) . " Arguments]  \n";
                }

                if ($relativeFile) {
                    $markdown .= $this->displayFileDebug($trace['file'], $trace['line'], $id, true);
                }

                if (isset($trace['args']) && count($trace['args'])) {
                    $markdown .= $this->displayArgsDebug($trace['args'], $id, true);
                }

            }

            header('Content-Type: text/html');
            $markdown = Encryptor::getInstance()->encrypt($markdown);

            echo $this->displayErrorTemplate(_EPH_ROOT_DIR_ . '/error500.phtml', ['markdown' => $markdown]);
        }

        // Log the error to the disk
        $this->logError();
        exit;
    }

    /**
     * Display lines around current line
     *
     * Markdown is returned instead of being printed
     * (HTML is printed because old backwards stuff blabla)
     *
     * @param string $file
     * @param int    $line
     * @param string $id
     * @param bool   $markdown
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     * @version 1.0.1 Add markdown support - return string
     */
    protected function displayFileDebug($file, $line, $id = null, $markdown = false) {

        $lines = (array) file($file);
        $offset = $line - 6;
        $total = 11;

        if ($offset < 0) {
            $total += $offset;
            $offset = 0;
        }

        $lines = array_slice($lines, $offset, $total);
        ++$offset;

        $ret = '';

        if ($markdown) {
            $ret .= "```php  \n";

            foreach ($lines as $k => $l) {
                $ret .= ($offset + $k) . '. ' . (($offset + $k == $line) ? '=>' : '  ') . ' ' . $l;
            }

            $ret .= "```  \n";
        } else {
            echo '<div class="psTrace" id="psTrace_' . $id . '" ' . ((is_null($id) ? 'style="display: block"' : '')) . '><pre>';

            foreach ($lines as $k => $l) {
                $string = ($offset + $k) . '. ' . htmlspecialchars($l);

                if ($offset + $k == $line) {
                    echo '<span class="selected">' . $string . '</span>';
                } else {
                    echo $string;
                }

            }

            echo '</pre></div>';
        }

        return $ret;
    }

    /**
     * Display arguments list of traced function
     * Markdown is returned instead of being printed
     * (HTML is printed because old backwards stuff blabla)
     *
     * @param array  $args List of arguments
     * @param string $id ID of argument
     * @param bool   $markdown
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     * @version 1.0.1 Add markdown support - return string
     */
    protected function displayArgsDebug($args, $id, $markdown = false) {

        $ret = '';

        if ($markdown) {
            $ret .= '```';

            foreach ($args as $arg => $value) {
                $ret .= 'Argument [' . Tools::safeOutput($arg) . "]  \n";
                $ret .= Tools::safeOutput(print_r($value, true));
                $ret .= "\n";
            }

            $ret .= "```  \n";
        } else {
            echo '<div class="psArgs" id="psArgs_' . $id . '"><pre>';

            foreach ($args as $arg => $value) {
                echo '<b>Argument [' . Tools::safeOutput($arg) . "]</b>\n";
                echo Tools::safeOutput(print_r($value, true));
                echo "\n";
            }

            echo '</pre>';
        }

        return $ret;
    }

    /**
     * Log the error on the disk
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    protected function logError() {

        $logger = new FileLogger();
        $logger->setFilename(_EPH_ROOT_DIR_ . '/log/' . date('Ymd') . '_exception.log');
        $logger->logError($this->getExtendedMessage(false));
    }

    /**
     * @deprecated 2.0.0
     */
    protected function getExentedMessage($html = true) {

        Tools::displayAsDeprecated();

        return $this->getExtendedMessage($html);
    }

    /**
     * Return the content of the Exception
     * @return string content of the exception.
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    protected function getExtendedMessage($html = true) {

        $format = '<p><b>%s</b><br /><i>at line </i><b>%d</b><i> in file </i><b>%s</b></p>';

        if (!$html) {
            $format = strip_tags(str_replace('<br />', ' ', $format));
        }

        return sprintf(
            $format,
            $this->getMessage(),
            $this->getLine(),
            ltrim(str_replace([_EPH_ROOT_DIR_, '\\'], ['', '/'], $this->getFile()), '/')
        );
    }

    /**
     * Return the content of the Exception
     * @return string content of the exception.
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    protected function getExtendedMessageMarkdown() {

        $format = "\n**%s**  \n *at line* **%d** *in file* `%s`  \n";

        return sprintf(
            $format,
            $this->getMessage(),
            $this->getLine(),
            ltrim(str_replace([_EPH_ROOT_DIR_, '\\'], ['', '/'], $this->getFile()), '/')
        );
    }

    /**
     * Display a phtml template file
     *
     * @param string $file
     * @param array  $params
     *
     * @return string Content
     *
     * @since 1.9.1.0
     */
    protected function displayErrorTemplate($file, $params = []) {

        foreach ($params as $name => $param) {
            $$name = $param;
        }

        ob_start();

        include $file;

        $content = ob_get_contents();

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        return $content;
    }

}
