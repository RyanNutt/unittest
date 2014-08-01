<?php
/**
 * Class to parse JUnit 4 results 
 * 
 * @author      Ryan Nutt
 * @link        http://github.com/RyanNutt/unittest
 * @package     qtype
 * @subpackage  unittest
 */

/**
 * Data class containing information on a JUnit test result
 */
class junit_parser {
    
    /** Version of JUnit file */
    public $version = false;
    
    /** Time that the test took */
    public $time = false;
    
    /** Number of tests run */
    public $testCount = 0;
    
    /** Number of failed tests */
    public $failureCount = 0;
    
    /** Number of tests causing an error */
    public $errorCount = 0;
    
    public $status = false; 
    
    public $gradeString = false; 
    
    public $rawString = false; 
    
    public function __construct($text) {
        $this->rawString = $text;
        //$this->testCount = $this->preg_search('/Tests run:.*?(\d+?)/', $text);
        //$this->failureCount = $this->preg_search('/Failures:.*?(\d+?)/', $text);
        //$this->errorCount = $this->preg_search('/Errors:.*?(\d+?)/', $text);
        $this->version = $this->preg_search('/JUnit version.*?([\d|\.]+)/', $text);
        $this->time = $this->preg_search('/Time.*?([\d|\.]+)/', $text); 
        $this->status = $this->preg_search('/\[unittest:(.+?)\]/', $text); 
        
        $this->gradeString = $this->preg_search('/^([\.EF]+)\r?\n/m', $text);
        
        $this->testCount = substr_count($this->gradeString, '.'); 
        $this->errorCount = substr_count($this->gradeString, 'E');
        $this->failureCount = substr_count($this->gradeString, 'F');
        
    }
    
    /**
     * Shortcut to doing regex without needing to worry about $matches
     * @param type $pattern
     * @param type $search
     * @param type $group
     */
    private function preg_search($pattern, $search, $group=1) {
        if (preg_match($pattern, $search, $matches)) {
            if (isset($matches[$group])) {
                return $matches[$group];
            }
            return false; 
        }
        return false; 
    }
    
}