<?php

class Core_Business_Payment_PaymentOption {

    // @codingStandardsIgnoreEnd

    protected $callToActionText;
    protected $logo;
    protected $action;
    protected $method;
    protected $inputs;
    protected $form;
    protected $moduleName;

    /**
     * Return Call to Action Text
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getCallToActionText() {

        return $this->callToActionText;
    }

    /**
     * Set Call To Action Text
     *
     * @param string $callToActionText
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setCallToActionText($callToActionText) {

        $this->callToActionText = $callToActionText;

        return $this;
    }

    /**
     * Return logo path
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getLogo() {

        return $this->logo;
    }

    /**
     * Set logo path
     *
     * @param string $logo
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setLogo($logo) {

        $this->logo = $logo;

        return $this;
    }

    /**
     * Return action to perform (POST/GET)
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getAction() {

        return $this->action;
    }

    /**
     * Set action to be performed by this option
     *
     * @param $action
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setAction($action) {

        $this->action = $action;

        return $this;
    }

    /**
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getMethod() {

        return $this->method;
    }

    /**
     * @param $method
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setMethod($method) {

        $this->method = $method;

        return $this;
    }

    /**
     * Return inputs contained in this payment option
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getInputs() {

        return $this->inputs;
    }

    /**
     * Set inputs for this payment option
     *
     * @param $inputs
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setInputs($inputs) {

        $this->inputs = $inputs;

        return $this;
    }

    /**
     * Get payment option form
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getForm() {

        return $this->form;
    }

    /**
     * Set payment option form
     *
     * @param $form
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setForm($form) {

        $this->form = $form;

        return $this;
    }

    /**
     * Get related module name to this payment option
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getModuleName() {

        return $this->moduleName;
    }

    /**
     * Set related module name to this payment option
     *
     * @param string $moduleName
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setModuleName($moduleName) {

        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Legacy options were specified this way:
     * - either an array with a top level property 'cta_text'
     *    and then the other properties
     * - or a numerically indexed array or arrays as described above
     * Since this was a mess, this method is provided to convert them.
     * It takes as input a legacy option (in either form) and always
     * returns an array of instances of Core_Business_Payment_PaymentOption
     *
     * @param array $legacyOption
     *
     * @return array|null
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function convertLegacyOption(array $legacyOption) {

        if (!$legacyOption) {
            return null;
        }

        if (array_key_exists('cta_text', $legacyOption)) {
            $legacyOption = [$legacyOption];
        }

        $newOptions = [];

        $defaults = [
            'action' => null,
            'form'   => null,
            'method' => null,
            'inputs' => [],
            'logo'   => null,
        ];

        foreach ($legacyOption as $option) {
            $option = array_merge($defaults, $option);

            $newOption = new Core_Business_Payment_PaymentOption();
            $newOption->setCallToActionText($option['cta_text'])
                ->setAction($option['action'])
                ->setForm($option['form'])
                ->setInputs($option['inputs'])
                ->setLogo($option['logo'])
                ->setMethod($option['method']);

            $newOptions[] = $newOption;
        }

        return $newOptions;
    }

}
