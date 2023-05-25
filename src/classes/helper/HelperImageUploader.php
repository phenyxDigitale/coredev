<?php

/**
 * Class HelperImageUploader
 *
 * @since 1.8.1.0
 */
class HelperImageUploader extends HelperUploader {

    /**
     * @return int
     *
     * @since   1.8.1.0
     * @version 1.8.5.0
     */
    public function getMaxSize() {

        return (int) Tools::getMaxUploadSize();
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getSavePath() {

        return $this->_normalizeDirectory(_EPH_TMP_IMG_DIR_);
    }

    /**
     * @param null $fileName
     *
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getFilePath($fileName = null) {

        //Force file path
        return tempnam($this->getSavePath(), $this->getUniqueFileName());
    }

    /**
     * @param $file
     *
     * @return bool
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    protected function validate(&$file) {

        $file['error'] = $this->checkUploadError($file['error']);

        if ($file['error']) {
            return false;
        }

        $postMaxSize = Tools::convertBytes(ini_get('post_max_size'));

        $uploadMaxFilesize = Tools::convertBytes(ini_get('upload_max_filesize'));

        if ($postMaxSize && ($this->_getServerVars('CONTENT_LENGTH') > $postMaxSize)) {
            $file['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini');

            return false;
        }

        if ($uploadMaxFilesize && ($this->_getServerVars('CONTENT_LENGTH') > $uploadMaxFilesize)) {
            $file['error'] = Tools::displayError('The uploaded file exceeds the upload_max_filesize directive in php.ini');

            return false;
        }

        if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize($this->getMaxSize()), $this->getAcceptTypes())) {
            $file['error'] = $error;

            return false;
        }

        if ($file['size'] > $this->getMaxSize()) {
            $file['error'] = sprintf(Tools::displayError('File (size : %1s) is too big (max : %2s)'), $file['size'], $this->getMaxSize());

            return false;
        }

        return true;
    }

}
