<?php
abstract class Bugapp_Model
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_loader;

    /**
     * @var array Table instances
     */
    protected $_tables    = array();

    /**
     * Primary table for operations
     * @var string
     */
    protected $_primaryTable = 'user';

    /**
     * Columns that may not be specified in save operations
     * @var array
     */
    protected $_protectedColumns = array();

    /**
     * Get plugin loader
     * 
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader()
    {
        if (null === $this->_loader) {
            $this->_loader = new Zend_Loader_PluginLoader();
            $this->_loader->addPrefixPath('Bugapp_DbTable', dirname(__FILE__) . '/DbTable/');
        }
        return $this->_loader;
    }

    /**
     * Get table class
     * 
     * @param  string $name 
     * @return Zend_Db_Table_Abstract
     */
    public function getTable($name)
    {
        $name  = ucfirst($name);
        if (!array_key_exists($name, $this->_tables)) {
            $class = $this->getPluginLoader()->load($name);
            $this->_tables[$name] = new $class;
        }
        return $this->_tables[$name];
    }

    /**
     * Insert or update a row
     * 
     * @param  array $info New or updated row data
     * @param  string|null Table name to use (defaults to primaryTable)
     * @return int Row ID of saved row
     */
    public function save(array $info, $tableName = null)
    {
        $tableName = (null === $tableName) ? $this->_primaryTable : $tableName;
        $table = $this->getTable($tableName);
        $id    = null;
        $row   = null;
        if (array_key_exists('id', $info)) {
            $id = $info['id'];
            unset($info['id']);
            $matches = $table->find($id);
            if (0 < count($matches)) {
                $row = $matches->current();
            }
        }
        if (null === $row) {
            $row = $table->createRow();
            $row->date_created = date('Y-m-d');
        }

        $columns = $table->info('cols');
        foreach ($this->_protectedColumns as $column) {
            unset($columns[$column]);
        }
        foreach ($columns as $column) {
            if (array_key_exists($column, $info)) {
                $row->$column = $info[$column];
            }
        }

        return $row->save();
    }

}
