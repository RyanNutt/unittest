<?php

/**
 * The question class for this question type.
 *
 * @package    qtype
 * @subpackage unittest
 * 
 * @author      Ryan Nutt
 * @link        http://github.com/RyanNutt/unittest
 * 
 * @author     Gergely Bertalan, bertalangeri@freemail.hu
 * @reference  sojunit 2008, Süreç Özcan, suerec@darkjade.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__).'/junit_parser.php');

defined('MOODLE_INTERNAL') || die();


/**
 * Represents an unittest question.
 *
 */
class qtype_unittest_question extends question_graded_automatically {
    public $responseformat;
    public $responsefieldlines;
    public $givencode;
    public $testclassname;
    public $automatic_feedback;


    /**
     * @param moodle_page the page we are outputting to.
     * @return qtype_unittest_format_renderer_base the response-format-specific renderer.
     */
    public function get_format_renderer(moodle_page $page) {

        return $page->get_renderer('qtype_unittest', 'format_' . $this->responseformat);
    }

     /**
     * return an array of expected parameters. The methode is called when the question attempt 
     * is actually stared and does necessary initialisation. In this case only the type of the
     * answer is defined.
     */
    public function get_expected_data() {

        return array('answer' => PARAM_RAW_TRIMMED);
    }


     /**
     * sumarize the response of the student
     */
    public function summarise_response(array $response) {

        if (isset($response['answer'])) {
            $formatoptions = new stdClass();
            $formatoptions->para = false;
            return html_to_text(format_text(
                    $response['answer'], FORMAT_HTML, $formatoptions), 0, false);
        } else {
            return null;
        }
    }

     /**
     * return the correct response of the student. Since we do not have any given correct
     * response to the question, we return null.
     */
    public function get_correct_response() {

        return null;
    }


     /**
     * check whether the student has already answered the question. 
     */
    public function is_complete_response(array $response) {

        return !empty($response['answer']);
    }


     /**
     * validate the student's response. Since we have a gradable response, we always
     * return an empty string here.
     */
    public function get_validation_error(array $response) {

        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseselectananswer', 'qtype_truefalse');
    }


     /**
     * every time a student changes his response in the texteditor this function is called
     * to check whether the student's newly entered response is the sane as the previous one.
     */
    public function is_same_response(array $prevresponse, array $newresponse) {

        return question_utils::arrays_same_at_key_missing_is_blank($prevresponse, $newresponse, 'answer');
    }
 

    /**
     * Here happens everything, starting from compiling and executing the proper files
     * till calculating the grade.
     * @param $response the response of the student
     * @return $fraction of the grade. If the max grade is 10 then fraction can be for 
     *         example 2 (10/5 = 2 indicating that from 10 points the student achieved 5).
     */
    public function grade_response(array $response) {

	global $CFG, $DB;
        
        $conf = get_config('qtype_unittest');
        
        // Check to make sure that javac, java, and unit exist in the
        // path specified. Otherwise, just error out
        if (!is_executable($conf->pathtojava)) {
            die(get_string('err_nojava', 'qtype_unittest')); 
        }
        if (!is_executable($conf->pathtojavac)){
            die(get_string('err_nojavac', 'qtype_unittest'));
        }
        if (!is_readable($conf->pathtojunit)) {
            die(get_string('err_nojunit', 'qtype_unittest')); 
        }
        
	/* preparation:
         * create a new sub-folder in the course-files-path for each question.
         * Put the related source codes (Test.java and student_response.java) of a user into this sub-folder
         */


	//these data are used to create an unique folder name for the temporary folder
        //in which the JUnit test will be executed
	$step = new question_attempt_step();
	$studentid = $step->get_user_id();
	$questionid = $this->id;
         
	$attemptid = optional_param('attempt', '', PARAM_INT);

	//if we are in edit question mode we have no attemptid => use any attemptid
	if(!isset($attemptid)) {
		$attemptid = $studentid + $questionid;;
	}

	//create a unique temp folder to keep the data together in one place 
        $cfg_dataroot_backslashes = $CFG->dataroot;
        $cfg_dataroot = str_replace("\\", "/", $cfg_dataroot_backslashes);	
        $temp_folder = $cfg_dataroot . '/unittest_temp_' . $studentid . '_' . $questionid . '_' . $attemptid;
	
	if (file_exists($temp_folder)) {
		$this->delTree($temp_folder);
	}
        $this->mkdir_recursive($temp_folder);


	//create the test class from the database and save it in the temporary folder
	$options = $DB->get_record('qtype_unittest_options', array('questionid' => $questionid));
         
	
        
	$junitcode = $options->junitcode;
        $junitcode = $this->add_junit_timeout($junitcode);
	

        $testclassname = $options->testclassname;

        preg_match('/^(?:\s*public)?\s*class\s+(\w[a-zA-Z0-9_]+)/m', $junitcode, $matches);
	if (empty( $matches[1])){
		$testclassname = 'Xy';
	} else {
		$testclassname = $matches[1];
	}
        $testFile = $temp_folder . '/' . $testclassname . '.java';
	touch($testFile);
	$fh = fopen($testFile, 'w') or die("can't open file");
	fwrite($fh, $junitcode);
	fclose($fh);


	//create the student's response class from the responsefield
	$studentscode = $response['answer'];
        $matches = array();
        preg_match('/^(?:\s*public)?\s*class\s+(\w[a-zA-Z0-9_]+)/m', $studentscode, $matches);
	if (empty( $matches[1])){
		$studentsclassname = 'Xy';
	} else {
		$studentsclassname = $matches[1];
	}

	$studentclass_path = $temp_folder . '/';
	$studentclass =  $studentclass_path . $studentsclassname . '.java';
        
	touch($studentclass);
	$fh = fopen($studentclass, 'w') or die("can't open file");
	fwrite($fh, $studentscode);
	fclose($fh);

        // Copy any attachments to the working folder
        $files = $this->getDataFiles();
        if (!empty($files)) {
            foreach ($files as $filename => $data) {
                file_put_contents($studentclass_path.$filename, $data); 
            } 
        }
        
        
	//compile the student's response
        $compileroutput = $this->compile($studentclass, $temp_folder, $studentsclassname);
	$compileroutput = substr_replace($compileroutput, '', 0, strlen($temp_folder)+1);
	$compileroutput = addslashes($compileroutput);
	$compileroutput = str_replace($temp_folder, "\n", $compileroutput);


	//create grader's feedback file. This file is used to return a feedback to the student
	$feedbackFile = $temp_folder . '/' . 'feedback.log';
	touch($feedbackFile);

	//execute junit test if no compilation error. If we have compiler error we jump to the else 
        //part, save the compiler error in the feedback file and grade the question with 0 pionts
	if(strlen($compileroutput) == 0){  

		//execute JUnit test
		$executionoutput = $this->execute($temp_folder, $testFile, $testclassname, $studentclass, $studentsclassname);
                
                //filter the $executionoutput by 'Time 0.000' part in order to get same
		//execution-outputs in the Results->Item analysis grouped together for a better analysis
		//$cleaned_executionoutput = preg_replace("/Time:\s([0-9]+[.][0-9]+)\s/","",$executionoutput);
		$cleaned_executionoutput = $executionoutput; 
                $cleaned_executionoutput = addslashes($cleaned_executionoutput);

		//initialize the feedback, later will be modified according to the junit test
		$automatic_feedback = $cleaned_executionoutput;
	
		//the JUnit-execution-output returns always a String in the first line e.g. "...F",
            	//which means that 1 out of 4 test cases didn't pass the JUnit test
            	//In the second line it says "Time ..."

                $junit = new junit_parser($cleaned_executionoutput);
                              
                $totalErrors = $junit->errorCount + $junit->failureCount;
                
            	// Discard the summary.
            	//$pos = strpos($executionoutput, 'Time') + 1; //get the pos in the string where 'Time' starts
            	//$executionoutputresult = substr($executionoutput, 0, $pos);
                $executionoutputresult = $executionoutput; 
            	// Count the failures and errors.
		//$numtest = substr_count($executionoutputresult, '.');
		//$numfailures = substr_count($executionoutputresult, 'F');
            	//$numerrors = substr_count($executionoutputresult, 'E');
		//$totalerror = $numfailures + $numerrors;

		//after we counted the failures and errors we can grade the response

		//CASE 1
		/* No tests were run. This could be due to either an issue with 
                 * the JUnit test file, in which case it's something that needs
                 * to be fixed. Or, the student could have edited their code 
                 * causing the tests to fail. For example, changing the method
                 * signature would cause an error in the JUnit build, which would
                 * then cause no tests to be run. 
                 */
		if($junit->testCount == 0){
			$fraction = 0; 
			$this->automatic_feedback = get_string('JE', 'qtype_unittest') . "\n\n" . $automatic_feedback . "\n\n" . $this->resultString('JE');
			
		}
		//CASE 2
		//100% correct answer
		else if($totalErrors == 0){
			$fraction = 1; 
			$this->automatic_feedback = get_string('CA', 'qtype_unittest') . "\n\n" . $automatic_feedback . "\n\n" . $this->resultString('CA');
		}
		//CASE 3
		//partially correct answer
		else if($junit->testCount > $totalErrors){
			$fraction = 1 - round(( 1.0 * $totalErrors / $junit->testCount),2);
			$this->automatic_feedback = get_string('PCA', 'qtype_unittest') . "\n\n" . $automatic_feedback . "\n\n" . $this->resultString('PCA');
		}
		//CASE 4
		// wrong answer
		else{
			$fraction = 0;
			$this->automatic_feedback = get_string('WA', 'qtype_unittest') . "\n\n" . $automatic_feedback . "\n\n" . $this->resultString('WA');
		}
	}
	// Doesn't compile => wrong answer. We grade the response with 0 point
	else{
		$fraction = 0;
		$this->automatic_feedback = get_string('CE', 'qtype_unittest') . "\n\n" . $compileroutput . "\n\n" . $this->resultString('CE');	
	}

    //after the grade is computed, a feedback is created. The feedback is the compiler output (in the case of compilation error)
    //or the JUnit test output (in the case when the response can be tested) plus some additional information.
    
    //compute the unique id for the feedback. We need it to store the feedback in the database.
    $unique_answerid = ($studentid + $questionid * $attemptid) + ($studentid * $questionid + $attemptid);

    $oldanswers = $DB->get_records('question_answers', array('question' => $unique_answerid));
    $answer = array_shift($oldanswers);
    //if this is the first attempt, we store the fedback in the database
    if (!$answer) {
        $answer = new stdClass();
        $answer->question = $unique_answerid;
        $answer->feedback = $this->automatic_feedback;
	$answer->answer = '';
	$DB->insert_record('question_answers', $answer);
    }
    //update an existing answer if possible. If the student has already answered the question, we have already created
    //and stored the feedback in the database and we simply update it.
    else {
	$answer->answer = '';
	$answer->feedback = $this->automatic_feedback;
	$DB->update_record('question_answers', $answer);
    }
	
    //at this pont the feedback has already stored in the database and the grade is created. We delete the temporary 
    //and return with the computed fraction of the response.
    //$this->delTree($temp_folder);
    return array($fraction, question_state::graded_state_for_fraction($fraction));
    }


     /**
     * compile the student's response
     * @param $studentclass the response of the student
     * @param $temp_folder the temporary folder defined in grade_response() we use to store the data
     * @param $studentsclassname the name of the class which has to be compiled
     * @return $compileroutput the output of the compiler
     */  
    function compile($studentclass, $temp_folder, $studentsclassname) {
        $conf = get_config('qtype_unittest');
        
        //work out the compile command line
        $compileroutputfile = $temp_folder . '/' . $studentsclassname . '_compileroutput.log';
	touch($compileroutputfile);
			
        $command = '"' . $conf->pathtojavac . '" -cp "' . $conf->pathtojunit . '" "' . $studentclass .'"';// . ' -Xstdout ' . $compileroutputfile;
        
        $stdout = '';
        $stderr = '';
        $proc = $this->execute_process($command, $stdout, $stderr, $temp_folder);
        
        return $stderr; 
        
        var_dump($stdout);
        var_dump($stderr);
        die(); 
        
	//get the content of the copiler output
        $compileroutput = file_get_contents($compileroutputfile);

	return $compileroutput;
    }

     /**
     * execute the JUnit test
     * @param $temp_folder the temporary folder defined in grade_response() we use to store the data
     * @param $testFile the JUnit test file
     * @param $testFileName the name of the JUnit test file
     * @param $studentclass the response of the student
     * @param $studentsclassname the name of the class which has to be tested
     * @return $executionoutput the output of the JUnit test
     */
    function execute($temp_folder, $testFile, $testFileName, $studentclass, $studentsclassname){
        $conf = get_config('qtype_unittest');
        
	//create the log file to store the output of the JUnit test
        $executionoutputfile = $temp_folder. '/' . $studentsclassname . '_executionoutput.log';
	
        touch($studentclass);

        /* Windows uses a semicolon to separate classpaths, Linux uses a colon */
        $classPath = $conf->pathtojunit . ($this->isWindows() ? ';' : ':') . $temp_folder;
        if (!empty($conf->pathtohamcrest)) {
            $classPath .= ($this->isWindows() ? ';' : ':') . $conf->pathtohamcrest;
        }
        $classPath = '"' . $classPath . '"';
        
        
	//work out the compile command line to compile the JUnit test
	$command = '"' . $conf->pathtojavac . '" -cp "' . $classPath . '" -sourcepath ' . $temp_folder . ' ' . $testFile . ' > ' . $executionoutputfile . ' 2>&1';
        
        $output = shell_exec($command);	

        $policyFile = dirname(__FILE__).'/polfile';
        
	//work out the compile command line to execute the JUnit test
	$commandWithSecurity = '"' . $conf->pathtojava . '"' . " -Djava.security.manager=default" . " -Djava.security.policy=". $policyFile . " -Duser.dir=" . $temp_folder ." ";	
	
        $command = $commandWithSecurity . ' -cp ' . $classPath . ' org.junit.runner.JUnitCore ' . $testFileName;
        //die($command);
        $stdout = '';
        $stderr = ''; 
        $ret = $this->execute_process($command, $stdout, $stderr, $temp_folder);
        
        return $stdout;
    }



     /**
     * read the content of a file
     * @param $fileinfo the file which content will be readed
     * @return $contents the content of the file
     */     
    function get_file_content($fileinfo) {
    	
	$fs = get_file_storage(); 
	 
	// Get file
	$file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
		              $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
	 
	// Read content
	if ($file) {
	    $contents = $file->get_content();
	} else {
	} 
	return $contents;
    } 


     /**
     * create a file
     */ 
    function mkdir_recursive($folder, $mode='') {
        if(is_dir($folder)) {
            return true;
        }
        if ($mode == '') {
            global $CFG;
            $mode = $CFG->directorypermissions;
        }
        if(!$this->mkdir_recursive(dirname($folder),$mode)) {
            return false;
        }
        return mkdir($folder,$mode);
    }

    /**
     * delete a directory tree
     */ 
    public static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }


     /**
     * checks the file acces. We do not use this function. 
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {

        if ($component == 'question' && $filearea == 'response_attachments') {
            // Response attachments visible if the question has them.
            return $this->attachments != 0;

        } else if ($component == 'question' && $filearea == 'response_answer') {
            // Response attachments visible if the question has them.
            return $this->responseformat === 'editorfilepicker';

        } else if ($component == 'qtype_unittest' && $filearea == 'graderinfo') {
            return $options->manualcomment;

        } else {
            return parent::check_file_access($qa, $options, $component,
                    $filearea, $args, $forcedownload);
        }
    }

    /**
     * Check whether the server is running Windows.
     * 
     * @return boolean  True if running on Windows, False if something else
     * @link http://stackoverflow.com/a/5879078
     */
    private function isWindows() {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ;
    }

    
    /**
     * Replacement for the shell_exec that captures stdout and stderr so 
     * that they don't have to be saved to a file if not needed. 
     * 
     * @link http://stackoverflow.com/a/2320835
     * 
     * @return Exit code from the process
     */
    private function execute_process($cmd, &$stdout, &$stderr, $workingFolder=null) {
        //die($cmd); 
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w"),  // stderr
         );
        
        /**
         * Extra double quotes are due to a php bug with proc_open
         * @link https://bugs.php.net/bug.php?id=49139
         */
        if ($this->isWindows()) {
            $cmd = '"' . $cmd . '"'; 
        }
        
        //var_dump($workingFolder.'/'); die(); 
        chdir($workingFolder.'/'); 
        var_dump(getcwd()); 
        $proc_id = proc_open($cmd, $descriptorspec, $pipes, $workingFolder);
        
        if ($proc_id) {
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
        }
//        var_dump($stdout);
//        var_dump($stderr); 
//        echo '<pre>'.print_r($pipes, true).'</pre>'; die(); 
        return proc_close($proc_id); 
    }
    
    /**
     * Formats the result code for inclusion in the saved output. 
     * 
     * @param type $tag
     * @return String
     */
    private function resultString($tag) {
        return '[unittest:' . $tag . ']';
    }
    
    /**
     * Parse the results from JUnit
     * 
     * @param type $result
     * @return stdClass
     */
    private function parseResult($result) {
        $out = new stdClass(); 
        
        $out->numTests = 0;
        $out->numFailures = 0;
        $out->numErrors = 0;

        if (preg_match('/Tests run:.*?(\d+)/is', $result, $matches)) {
            if (!empty($matches[1])) {
                $out->numTests = $matches[1]; 
            }
        }
        if (preg_match('/Failures:.*?(\d+?)/i', $result, $matches)) {
            if (!empty($matches[1])) {
                $out->numFailures = $matches[1];
            }
        }
        if (preg_match('/Errors:.*?(\d+?)/is', $result, $matches)) {
            if (!empty($matches[1])) {
                $out->numErrors = $matches[1];
            }
        } 
        
        return $out; 
    }
    
    /**
     * Adds a default timeout to the JUnit test file if it doesn't already
     * exist.
     * 
     * @link https://github.com/junit-team/junit/wiki/Timeout-for-tests
     * @param String $junitCode
     */
    private function add_junit_timeout($junitCode) {
        
        // If it's already there, don't repeat
        if (!preg_match('/@Rule\s+?public.*?new\s+Timeout\(\d+?\)\s*?;/', $junitCode)) {
            // Replace the first { with a { and the timeout line
            
            $conf = get_config('qtype_unittest');
            $timeout = $conf->defaulttimeout * 1000; // need milliseconds
            
            $newLine = "\t@Rule\n\tpublic Timeout globalTimeout = new Timeout(" . $timeout . ");\n"; 
            
            $junitCode = "import org.junit.rules.Timeout;\nimport org.junit.Rule; \n" . preg_replace('/{/', "{\n" . $newLine, $junitCode, 1);
        }
        //die($junitCode); 
        return $junitCode; 
    }
    
    /**
     *  Return an associative array mapping filename to datafile contents
     *  for all the datafiles associated with this question
     */
    private function getDataFiles() {
        global $DB;
        if (isset($this->contextid)) {  // Is this possible? No harm in trying
            $contextid = $this->contextid;
        } else if (isset($this->context)) {
            $contextid = $this->context->id;
        } else {
            $record = $DB->get_record('question_categories',
                array('id' => $this->category), 'contextid');
            $contextid = $record->contextid;
        }
        $fs = get_file_storage();
        $fileMap = array();
        $files = $fs->get_area_files($contextid, 'qtype_unittest', 'datafile', $this->id);
        foreach ($files as $f) {
            $name = $f->get_filename();
            if ($name !== '.') {
                $fileMap[$f->get_filename()] = $f->get_content();
            }
        }
        return $fileMap;
    }
}
