<?php
/**
 * Strings for component 'qtype_unittest', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @copyright &copy: 2013 Gergely Bertalan, Technical University Berlin
 * @author bertalangeri@freemail.hu
 * @reference: javaunittest 2008, Süreç Özcan, suerec@darkjade.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package so_questiontypes
 */



$string['graderinfo'] = 'Information for graders';
$string['nlines'] = '{$a} lines';
$string['pluginname'] = 'Unit Test';
$string['pluginname_help'] = 'JUnit question type';
$string['pluginname_link'] = 'question/type/javaunittest';
$string['pluginnameadding'] = 'Adding an javaunittest question';
$string['pluginnameediting'] = 'Editing an javaunittest question';
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


//STRINGS FOR FEEDBACK
$string['CA'] = 'CORRECT ANSWER';
$string['PCA'] = 'PARTIALLY CORRECT ANSWER';
$string['WA'] = 'WRONG ANSWER';
$string['CE'] = 'COMPILER ERROR';
$string['JE'] = 'JUNIT TEST FILE ERROR: Test cannot be executed. The test class is either missing or contains compilation error(s).';

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