<?php
/**
 * Strings for component 'qtype_unittest', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @copyright &copy: 2013 Gergely Bertalan, Technical University Berlin
 * @author bertalangeri@freemail.hu
 * @reference: unittest 2008, Süreç Özcan, suerec@darkjade.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package so_questiontypes
 */


$string['attachments'] = 'Attachments'; 
$string['attachments_help'] = 'Any files attached to the question will be automatically copied to the working folder prior to the submitted code being tested.';
$string['datafiles'] = 'Attachments';
$string['datafiles_help'] = 'This is the help'; 
$string['graderinfo'] = 'Information for graders';
$string['nlines'] = '{$a} lines';
$string['pluginname'] = 'Unit Test';
$string['pluginname_help'] = 'JUnit question type';
$string['pluginname_link'] = 'question/type/unittest';
$string['pluginnameadding'] = 'Adding an Unit Test question';
$string['pluginnameediting'] = 'Editing an Unit Test question';
$string['pluginnamesummary'] = 'Allows a Java-code response which is evaluated by a defined JUnit test';
$string['responsefieldlines'] = 'Input box size';
$string['responseformat'] = 'Response format';
$string['testclassname'] = 'JUnit test class name';
$string['testclassname_help'] = 'The JUnit class name of the following JUnit test code. The JUnit class name must be the same as the class name in the following "JUnit test code" section.';
$string['uploadtestclass'] = 'JUnit test code';
$string['uploadtestclass_help'] = 'Upload the JUnit code. The code must be correct and must match to the question.';
$string['givencode'] = 'Given code';
$string['givencode_help'] = 'Code which is provided by the instructor';
$string['loadedtestclassheader'] = 'Load test file';

$string['tests'] = 'tests';
$string['correcttests'] = 'correct'; 
$string['outof'] = 'out of'; 
$string['notests'] = 'Zero tests run'; 

//STRINGS FOR FEEDBACK
$string['CA'] = 'Correct Answer';
$string['PCA'] = 'Partially Correct Answer';
$string['WA'] = 'Incorrect Answer';
$string['CE'] = 'Compile Error';
$string['JE'] = 'JUnit Error';

$string['pathtojava'] = 'Path to Java';
$string['configpathtojava'] = 'Server path to Java';
$string['pathtojavac'] = 'Path to JavaC';
$string['configpathtojavac'] = 'Server path to the Java compiler'; 
$string['pathtojunit'] = 'Path to JUnit.jar';
$string['configpathtojunit'] = 'Server path to the junit.jar file'; 
$string['useace'] = 'Use the Ace editor';
$string['configuseace'] = 'When available, use the Ace code editor instead of a plain textarea';
$string['highlightcode'] = 'Wrap displayed code';
$string['confighighlightcode'] = 'Wrap displayed code in &lbrack;code&lbrack; tags. Requires another plugin.'; 
$string['pathtohamcrest'] = 'Path to hamcrest.jar';
$string['configpathtohamcrest'] = 'Server path to the hamcrest.jar file. This file is only needed for JUnit 4.11 and up. If you\'re using an older version of JUnit 4 then you can leave this blank.'; 
$string['defaulttimeout'] = 'Default timeout'; 
$string['configdefaulttimeout'] = 'Number of seconds to run, by default, before a test is considered a failure. Can be overridden on individual tests. See the <a href="https://github.com/junit-team/junit/wiki/Timeout-for-tests" target="_blank">JUnit wiki</a> for more information.';

// Error messages
$string['err_nojava'] = 'Java does not appear to exist at the path specified';
$string['err_nojavac'] = 'JavaC does not appear to exist at the path specified';
$string['err_nojunit'] = 'The JUnit.jar file does not appear to exist at the path specified'; 