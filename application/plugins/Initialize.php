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
    protected $_appPath;

    /**
     * Constructor
     *
     * Initialize environment, application path, and configuration.
     * 
     * @param  string $env 
     * @param  string|null $appPath
     * @return void
     */
    public function __construct($env, $appPath = null)
    {
        $this->_setEnv($env);
        if (null === $appPath) {
            $appPath = realpath(dirname(__FILE__) . '/../');
        }
        $this->_appPath = $appPath;

        $this->_front = Zend_Controller_Front::getInstance();

        Zend_Locale::$compatibilityMode = false;
    }

    /**
     * Route startup
     * 
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // $this->initPathCache()
        $this->initDb()
             ->initHelpers()
             ->initView()
             ->initPlugins()
             ->initRoutes()
             ->initControllers();
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

    /**
     * Initialize the file map cache for Zend_Loader
     * 
     * @return Bugapp_Plugin_Initialize
     */
    public function initPathCache()
    {
        $pluginIncFile = $this->_appPath . '/../data/cache/plugins.inc.php';
        if (file_exists($pluginIncFile)) {
            include_once $pluginIncFile;
        }
        Zend_Loader::setIncludeFileCache($pluginIncFile);
        Zend_Loader_PluginLoader::setIncludeFileCache($pluginIncFile);
        return $this;
    }

    /**
     * Initialize DB
     * 
     * @return Bugapp_Plugin_Initialize
     */
    public function initDb()
    {
        $config = $this->_getConfig();
        if (!isset($config->db)) {
            return $this;
        }

        $db = Zend_Db::factory($config->db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
        return $this;
    }

    /**
     * Initialize action helpers
     * 
     * @return Bugapp_Plugin_Initialize
     */
    public function initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPath($this->_appPath . '/controllers/helpers', 'Bugapp_Helper');
        return $this;
    }

    /**
     * Initialize view and layout
     * 
     * @return Bugapp_Plugin_Initialize
     */
    public function initView()
    {
        // Setup View
        $view = new Zend_View();
        $view->doctype('XHTML1_TRANSITIONAL');
        $view->placeholder('nav')->setPrefix('<div id="nav">')
                                 ->setPostfix('</div>');

        // Set view in ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        // Initialize layouts
        Zend_Layout::startMvc($this->_appPath . '/layouts/scripts');

        return $this;
    }

    /**
     * Initialize plugins
     * 
     * @return Bugapp_Plugin_Initialize
     */
    public function initPlugins()
    {
        $loader = new Zend_Loader_PluginLoader(array(
            'Bugapp_Plugin' => $this->_appPath . '/plugins/',
        ));
        $class = $loader->load('Auth');
        $this->_front->registerPlugin(new $class());
        return $this;
    }

    /**
     * Initialize routes
     * 
     * @return Bugapp_Plugin_Initialize
     */
    public function initRoutes()
    {
        return $this;
    }

    /**
     * Initialize controller directories
     * 
     * @return Bugapp_Plugin_Initialize
     */
    public function initControllers()
    {
        $this->_front->addControllerDirectory($this->_appPath . '/controllers/');
        return $this;
    }

    /**
     * Get configuration object
     * 
     * @return Zend_Config
     */
    protected function _getConfig()
    {
        if (null === self::$_config) {
            self::$_config = new Zend_Config_Ini($this->_appPath . '/config/site.ini', $this->_env, true);
            self::$_config->root = $this->_appPath;
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
