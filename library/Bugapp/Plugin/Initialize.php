<?php
class Bugapp_Plugin_Initialize extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Config
     */
    protected static $_config;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var string Path to application root
     */
    protected $_root;

    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     * 
     * @param  string $env 
     * @param  string|null $root 
     * @return void
     */
    public function __construct($env, $root = null)
    {
        $this->_setEnv($env);
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../../../');
        }
        $this->_root = $root;

        $this->initPhpConfig();

        $this->_front = Zend_Controller_Front::getInstance();
    }

    /**
     * Route startup
     * 
     * @return void
     */
    public function routeStartup()
    {
        $this->initDb();
        $this->initHelpers();
        $this->initControllers();
        $this->initRoutes();

        $this->_front->throwExceptions(true);
    }

    /**
     * Get config object (static)
     * 
     * @return Zend_Config
     */
    public static function getConfig()
    {
        return self::$_config;
    }

    public function initPhpConfig()
    {
        $config = $this->_getConfig();
        if (!isset($config->phpSettings)) {
            return;
        }
        foreach ($config->phpSettings as $key => $value) {
            ini_set($key, $value);
        }
    }

    /**
     * Initialize DB
     * 
     * @return void
     */
    public function initDb()
    {
        $config = $this->_getConfig();
        if (!isset($config->db)) {
            return;
        }

        $db = Zend_Db::factory($config->db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    }

    /**
     * Initialize action helpers
     * 
     * @return void
     */
    public function initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPrefix('Bugapp_Helper');
    }

    /**
     * Initialize controller directories
     * 
     * @return void
     */
    public function initControllers()
    {
        $this->_front->addControllerDirectory($this->_root . '/application/controllers/');
    }

    /**
     * Initialize routes
     * 
     * @return void
     */
    public function initRoutes()
    {
    }

    /**
     * Get configuration object
     * 
     * @return Zend_COnfig
     */
    protected function _getConfig()
    {
        if (null === self::$_config) {
            $configData = include $this->_root . '/application/config/site.php';
            if (!array_key_exists($this->_env, $configData)) {
                throw new Exception(sprintf('No configuration available for env %s', $this->_env));
            }
            self::$_config = new Zend_Config($configData[$this->_env], true);
            self::$_config->root = $this->_root;
        }
        return self::$_config;
    }

    /**
     * Set environment
     * 
     * @param  string $env 
     * @return void
     */
    protected function _setEnv($env)
    {
        if (!in_array($env, array('development', 'test', 'production'))) {
            $env = 'development';
        }
        $this->_env = $env;
    }
}
