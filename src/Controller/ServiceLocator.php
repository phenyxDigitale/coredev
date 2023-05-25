<?php

namespace Ephenyxdigital\Core\DependencyInjection;

use Core_Foundation_IoC_Container;
use Db;
use Ephenyxdigital\Core\Error\ErrorHandler;
use Ephenyxdigital\Core\Error\Response\CliErrorResponse;
use Ephenyxdigital\Core\Error\Response\DebugErrorPage;
use Ephenyxdigital\Core\Error\Response\ErrorResponseInterface;
use Ephenyxdigital\Core\Error\Response\ProductionErrorPage;
use Exception;
use PhenyxException;

/**
 * Class ServiceLocatorCore
 *
 * @since 1.3.0
 */
class ServiceLocatorCore {

    // services
    const SERVICE_SERVICE_LOCATOR = 'Ephenyxdigital\Core\DependencyInjection\ServiceLocator';
    const SERVICE_SCHEDULER = 'Ephenyxdigital\Core\WorkQueue\Scheduler';
    const SERVICE_WORK_QUEUE_CLIENT = 'Ephenyxdigital\Core\WorkQueue\WorkQueueClient';
    const SERVICE_READ_WRITE_CONNECTION = 'Db';
    const SERVICE_ERROR_HANDLER = 'Ephenyxdigital\Core\Error\ErrorHandler';
    const SERVICE_ERROR_RESPONSE = 'Ephenyxdigital\Core\Error\Response\ErrorResponseInterface';

    // Legacy services
    const SERVICE_ADAPTER_CONFIGURATION = 'Core_Business_ConfigurationInterface';
    const SERVICE_ADAPTER_DATABASE = 'Core_Foundation_Database_DatabaseInterface';

    /**
     * @var ServiceLocator singleton instance
     */
    protected static $instance;

    /**
     * @var Core_Foundation_IoC_Container container
     */
    protected $container;

    /**
     * ServiceLocatorCore constructor
     * @param Core_Foundation_IoC_Container $container
     * @throws Exception
     */
    protected function __construct(Core_Foundation_IoC_Container $container = null) {

        $this->container = is_null($container)
        ? new Core_Foundation_IoC_Container()
        : $container;

        // initialize error page
        $this->container->bind(static::SERVICE_ERROR_RESPONSE, $this->getErrorResponse(), true);

        // initialize error handler

        if (!$this->container->knows(static::SERVICE_ERROR_HANDLER)) {
            $errorHandler = new ErrorHandler($this->getByServiceName(static::SERVICE_ERROR_RESPONSE));
            $this->container->bind(static::SERVICE_ERROR_HANDLER, $errorHandler, true);
        }

        // services
        $this->container->bind(static::SERVICE_SERVICE_LOCATOR, $this, true);
        $this->container->bind(static::SERVICE_WORK_QUEUE_CLIENT, static::SERVICE_WORK_QUEUE_CLIENT, true);
        $this->container->bind(static::SERVICE_SCHEDULER, static::SERVICE_SCHEDULER, true);
        $this->container->bind(static::SERVICE_READ_WRITE_CONNECTION, [Db::, 'getInstance'], true);

        // legacy services
        $this->container->bind(static::SERVICE_ADAPTER_CONFIGURATION, 'Adapter_Configuration', true);
        $this->container->bind(static::SERVICE_ADAPTER_DATABASE, 'Adapter_Database', true);
    }

    /**
     * @return ServiceLocatorCore
     */
    public function getServiceLocator() {

        return $this;
    }

    /**
     * Instantiates controller class
     *
     * @param $controllerClass
     * @return \Controller
     * @throws PhenyxException
     */
    public function getController($controllerClass) {

        $controller = $this->getByServiceName($controllerClass);

        if (!($controller instanceof \Controller)) {
            throw new PhenyxException("Failed to construct controller, class '$controllerClass' does not extend Controller");
        }

        return $controller;
    }

    /**
     * @return \Ephenyxdigital\Core\WorkQueue\Scheduler
     * @throws PhenyxException
     */
    public function getScheduler() {

        return $this->getByServiceName(static::SERVICE_SCHEDULER);
    }

    /**
     * @return \Ephenyxdigital\Core\WorkQueue\WorkQueueClient
     * @throws PhenyxException
     */
    public function getWorkQueueClient() {

        return $this->getByServiceName(static::SERVICE_WORK_QUEUE_CLIENT);
    }

    /**
     * Returns read/write connection
     *
     * @return Db
     * @throws PhenyxException
     */
    public function getConnection() {

        return $this->getByServiceName(static::SERVICE_READ_WRITE_CONNECTION);
    }

    /**
     * @return ErrorHandler
     */
    public function getErrorHandler() {

        try {
            return $this->getByServiceName(static::SERVICE_ERROR_HANDLER);
        } catch (PhenyxException $e) {
            die('Invariant: error handler must always be known to service locator');
        }

    }

    /**
     * @param string $serviceName
     * @return mixed
     * @throws PhenyxException
     */
    public function getByServiceName($serviceName) {

        try {
            return $this->container->make($serviceName);
        } catch (Exception $e) {
            throw new PhenyxException("Failed to construct service '$serviceName': " . $e->getMessage(), 0, $e);
        }

    }

    /**
     * @return ServiceLocator singleton instance
     */
    public static function getInstance() {

        if (is_null(static::$instance)) {
            die("Service locator has not been initialized yet");
        }

        return static::$instance;
    }

    /**
     * Method to initialize service locator
     * @param Core_Foundation_IoC_Container $container
     */
    public static function initialize(Core_Foundation_IoC_Container $container = null) {

        if (!is_null(static::$instance)) {
            die("Service locator is already initialized");
        }

        try {
            static::$instance = new static($container);
        } catch (\Throwable $e) {
            die("Failed to initialize service locator: " . $e);
        }

    }

    /**
     * @return ErrorResponseInterface
     */
    protected function getErrorResponse() {

        if (php_sapi_name() === 'cli') {
            return new CliErrorResponse();
        }

        if (_PS_MODE_DEV_) {
            return new DebugErrorPage();
        }

        return new ProductionErrorPage();
    }

}
