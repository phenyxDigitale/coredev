<?php

class Core_Business_Email_EmailLister {

    // @codingStandardsIgnoreEnd

    protected $filesystem;

    /**
     * Core_Business_Email_EmailLister constructor.
     *
     * @param Core_Foundation_FileSystem_FileSystem $fs
     */
    public function __construct(Core_Foundation_FileSystem_FileSystem $fs) {

        // Register dependencies
        $this->filesystem = $fs;
    }

    /**
     * Return the list of available mails
     *
     * @param string $dir
     *
     * @return array|null
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getAvailableMails($dir) {

        if (!is_dir($dir)) {
            return null;
        }

        $mailDirectory = $this->filesystem->listEntriesRecursively($dir);
        $mailList = [];

        // Remove unwanted .html / .txt / .tpl / .php / . / ..

        foreach ($mailDirectory as $mail) {

            if (strpos($mail->getFilename(), '.') !== false) {
                $tmp = explode('.', $mail->getFilename());

                // Check for filename existence (left part) and if extension is html (right part)

                if (($tmp === false || !isset($tmp[0])) || (isset($tmp[1]) && $tmp[1] !== 'html')) {
                    continue;
                }

                $mailNameNoExt = $tmp[0];

                if (!in_array($mailNameNoExt, $mailList)) {
                    $mailList[] = $mailNameNoExt;
                }

            }

        }

        return $mailList;
    }

    /**
     * Give in input getAvailableMails(), will output a human readable and proper string name
     *
     * @param string $mailName
     *
     * @return string
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    public function getCleanedMailName($mailName) {

        if (strpos($mailName, '.') !== false) {
            $tmp = explode('.', $mailName);

            if ($tmp === false || !isset($tmp[0])) {
                return $mailName;
            }

            $mailName = $tmp[0];
        }

        return ucfirst(str_replace(['_', '-'], ' ', $mailName));
    }

}
