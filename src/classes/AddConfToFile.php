<?php

/**
 * Class AddConfToFile
 *
 * @package EphenyxShop
 */
class AddConfToFile {

    public $fd;
    public $file;
    public $mode;
    public $error = false;

    /**
     * AddConfToFile constructor.
     *
     * @param string $file
     * @param string $mode
     */
    public function __construct($file, $mode = 'r+') {

        $this->file = $file;
        $this->mode = $mode;
        $this->checkFile($file);

        if ($mode == 'w' and !$this->error) {

            if (!$res = @fwrite($this->fd, '<?php' . "\n")) {
                $this->error = 6;
            }

        }

    }

    /**
     *
     */
    public function __destruct() {

        if (!$this->error) {
            @fclose($this->fd);
        }

    }

    /**
     * @param $file
     */
    protected function checkFile($file) {

        if (!$fd = @fopen($this->file, $this->mode)) {
            $this->error = 5;
        } else if (!is_writable($this->file)) {
            $this->error = 6;
        }

        $this->fd = $fd;
    }

    /**
     * @param string $name
     * @param string $data
     *
     * @return bool
     */
    public function writeInFile($name, $data) {

        if (!$res = @fwrite(
            $this->fd,
            'define(\'' . $name . '\', \'' . $this->checkString($data) . '\');' . "\n"
        )) {
            $this->error = 6;

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function writeEndTagPhp() {

        if (!$res = @fwrite($this->fd, '?>' . "\n")) {
            $this->error = 6;

            return false;
        }

        return true;
    }

    /**
     * @param string $string
     *
     * @return mixed|string
     */
    public function checkString($string) {

        
        if (!is_numeric($string)) {
            $string = addslashes($string);
            $string = str_replace(["\n", "\r"], '', $string);
        }
        

        return $string;
    }

}
