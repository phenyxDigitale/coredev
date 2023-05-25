<?php

class ProfilingCore {

    public static function redirect($url, $base_uri = __EPH_BASE_URI__, Link $link = null, $headers = null) {

        if (!$link) {
            $link = Context::getContext()->link;
        }

        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && $link) {

            if (strpos($url, $base_uri) === 0) {
                $url = substr($url, strlen($base_uri));
            }

            if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0) {
                $url = substr($url, strlen('index.php?controller='));

                if (Configuration::get(Configuration::REWRITING_SETTINGS)) {
                    $url = Tools::strReplaceFirst('&', '?', $url);
                }

            }

            $explode = explode('?', $url);
            // don't use ssl if url is home page
            // used when logout for example
            $use_ssl = !empty($url);
            $url = $link->getPageLink($explode[0], $use_ssl);

            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }

        }

        // Send additional headers

        if ($headers) {

            if (!is_array($headers)) {
                $headers = [$headers];
            }

            foreach ($headers as $header) {
                header($header);
            }

        }

        Context::getContext()->controller->setRedirectAfter($url);
    }

    public static function getDefaultControllerClass() {

        if (isset(Context::getContext()->employee) && Validate::isLoadedObject(Context::getContext()->employee) && isset(Context::getContext()->employee->default_tab)) {
            $default_controller = EmployeeMenu::getClassNameById((int) Context::getContext()->employee->default_tab);
        }

        if (empty($default_controller)) {
            $default_controller = 'AdminDashboard';
        }

        $controllers = Performer::getControllers([_EPH_ADMIN_DIR_ . '/tabs/', _EPH_ADMIN_CONTROLLER_DIR_, _EPH_OVERRIDE_DIR_ . 'controllers/admin/']);

        if (!isset($controllers[strtolower($default_controller)])) {
            $default_controller = 'adminnotfound';
        }

        $controller_class = $controllers[strtolower($default_controller)];
        return $controller_class;
    }

    public static function redirectLink($url) {

        if (!preg_match('@^https?://@i', $url)) {

            if (strpos($url, __EPH_BASE_URI__) !== false && strpos($url, __EPH_BASE_URI__) == 0) {
                $url = substr($url, strlen(__EPH_BASE_URI__));
            }

            $explode = explode('?', $url);
            $url = Context::getContext()->link->getPageLink($explode[0]);

            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }

        }

    }

    public static function redirectAdmin($url) {

        if (!is_object(Context::getContext()->controller)) {
            try {
                $controller = Controller::getController(Profiling::getDefaultControllerClass());
                $controller->setRedirectAfter($url);
                $controller->run();
                Context::getContext()->controller = $controller;
                die();
            } catch (PhenyxException $e) {
                $e->displayMessage();
            }

        } else {
            Context::getContext()->controller->setRedirectAfter($url);
        }

    }

}
