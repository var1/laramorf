<?php
namespace Morphy;

define('PHPMORPHY_SOURCE_FSA', 'fsa');
define('PHPMORPHY_SOURCE_DBA', 'dba');
define('PHPMORPHY_SOURCE_SQL', 'sql');

interface phpMorphy_Source_Interface {
    function getValue($key);
}

class phpMorphy_Source_Fsa implements phpMorphy_Source_Interface {
    protected
        $fsa,
        $root;
    
    function __construct(phpMorphy_Fsa_Interface $fsa) {
        $this->fsa = $fsa;
        $this->root = $fsa->getRootTrans();
    }
    
    function getFsa() {
    	return $this->fsa;
    }
    
    function getValue($key) {
        if(false === ($result = $this->fsa->walk($this->root, $key, true)) || !$result['annot']) {
            return false;
        }
        
        return $result['annot'];
    }
}

class phpMorphy_Source_Dba implements phpMorphy_Source_Interface {
    const DEFAULT_HANDLER = 'db3';
    
    protected $handle;
    
    function __construct($fileName, $options = null) {
        $this->handle = $this->openFile($fileName, $this->repairOptions($options));
    }
    
    function close() {
        if(isset($this->handle)) {
            dba_close($this->handle);
            $this->handle = null;
        }
    }
    
    static function getDefaultHandler() {
        return self::DEFAULT_HANDLER;
    }
    
    protected function openFile($fileName, $options) {
        if(false === ($new_filename = realpath($fileName))) {
            throw new phpMorphy_Exception("Can`t get realpath for '$fileName' file");
        }
        
        $lock_mode = $options['lock_mode'];
        $handler = $options['handler'];
        $func = $options['persistent'] ? 'dba_popen' : 'dba_open';
        
        if(false === ($result = $func($new_filename, "r$lock_mode", $handler))) {
            throw new phpMorphy_Exception("Can`t open '$fileFile' file");
        }
        
        return $result;
    }
    
    protected function repairOptions($options) {
        $defaults = array(
            'lock_mode' => 'd',
            'handler' => self::getDefaultHandler(),
            'persistent' => false
        );
        
        return (array)$options + $defaults;
    }
    
    function getValue($key) {
        return dba_fetch($key, $this->handle);
    }
}
