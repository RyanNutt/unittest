<?php
/**
 * The renderer type class for this question type.
 *
 * @package    qtype
 * @author      Ryan Nutt
 * @subpackage unittest
 * @reference     Gergely Bertalan, bertalangeri@freemail.hu
 * @reference  sojunit 2008, Süreç Özcan, suerec@darkjade.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for unittest questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_unittest_renderer extends qtype_renderer {

    /**
     * Used to keep the JavaScript from including more than once, even when
     * multiple unittest questions are on the same page. 
     * @var type 
     */
    private static $jsInitCalled = false; 
    
    /**
     * Generates the web-side when te student is attempting the question. This is the
     * side which is showed with the question text and the response field
     */
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

	global $DB, $PAGE;

        $question = $qa->get_question();
        $responseoutput = $question->get_format_renderer($this->page);

        // Answer field.
        $step = $qa->get_last_step_with_qt_var('answer');

	// Get the question options to show the question text
	$question->options = $DB->get_record('qtype_unittest_options' , array('questionid' =>$question->id));
	$studentscode = $question->options->givencode;
        if (empty($options->readonly)) {
            $answer = $responseoutput->response_area_input('answer', $qa, $step, $question->responsefieldlines, $options->context, $studentscode);

        } else {
            $answer = $responseoutput->response_area_read_only('answer', $qa, $step, $question->responsefieldlines, $options->context, $studentscode);
        }

        $inputname = $qa->get_qt_field_name('answer');
        
	// Generate the html code which will be showed
        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));
        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', $answer, array('class' => 'answer'));
        $result .= html_writer::end_tag('div');

        
        $attemptid = optional_param('attempt', '', PARAM_INT);
	$question = $qa->get_question();
	$step = $qa->get_last_step_with_qt_var('answer');
	$studentid = $step->get_user_id();
	$questionid = $question->id;

	//compute the unique id of the feedback
	$unique_answerid = ($studentid + $questionid * $attemptid) + ($studentid * $questionid + $attemptid);

	//get the feedback from the database
	$answer = $DB->get_records('question_answers', array('question' => $unique_answerid));
	$answer = array_shift($answer);
        
        if (!empty($answer->feedback)) {
            
            $junit = new junit_parser($answer->feedback);
            
            if ($junit->testCount == 0) {
                $percentage = 0; 
            }
            else {
                $percentage = round(($junit->testCount - ($junit->errorCount + $junit->failureCount)) / $junit->testCount * 100);
            }
            $correct = $junit->testCount - $junit->errorCount - $junit->failureCount;
            $result .= '<div class="unittest_results clear">';
            $result .= '<div class="progress">';
            $result .= '<div class="correct" style="width:' . $percentage . '%">&nbsp;</div>'; 
            $result .= '</div>'; // .progress
            $result .= '<div class="left">' . get_string($junit->status, 'qtype_unittest') .'</div>';
            $result .= '<div class="right">';
            if ($junit->testCount == 0) {
                $result .= get_string('notests', 'qtype_unittest');
            }
            else {
                $result .= $correct . ' ' . get_string('outof', 'qtype_unittest') . ' ' . $junit->testCount . ' ' . get_string('tests', 'qtype_unittest') . ' ' . get_string('correcttests', 'qtype_unittest'); 
            }
            $result .= '</div>'; //.right 
            $result .= '<br style="clear:both;">'; 
            $result .= '</div>';
        }
        
        $conf = get_config('qtype_unittest');        
        // Need to load JS for Ace
        if ($conf->useace) { // && !self::$jsInitCalled) {
            $PAGE->requires->js('/question/type/unittest/ext/ace/src-min-noconflict/ace.js'); 
            $PAGE->requires->yui_module('moodle-qtype_unittest-loader', 'M.unittest_loader.question_page', array(array('element' => $inputname)));
            self::$jsInitCalled = true; 
        }
        //echo '<pre>'.print_r($question, true).'</pre>'; 
        return $result;
    }


    /**
     * Generates the specific feedback from the database when the attempt is finished
     * and the question is answered.
     */   
    public function specific_feedback(question_attempt $qa) {
	
	global $DB, $CFG;

	//in question.grade_response() function these data were used to store the feedback
	//in the database.
	$attemptid = optional_param('attempt', '', PARAM_INT);
	$question = $qa->get_question();
	$step = $qa->get_last_step_with_qt_var('answer');
	$studentid = $step->get_user_id();
	$questionid = $question->id;

	//compute the unique id of the feedback
	$unique_answerid = ($studentid + $questionid * $attemptid) + ($studentid * $questionid + $attemptid);

	//get the feedback from the database
	$answer = $DB->get_records('question_answers', array('question' => $unique_answerid));
	$answer = array_shift($answer);

	return $question->format_text('<div style="white-space:pre-wrap;">'.$answer->feedback.'</div>', 0, $qa, 'question', 'answerfeedback', 1);

    }

}


/**
 * A base class to abstract out the differences between different type of
 * response format.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_unittest_format_renderer_base extends plugin_renderer_base {
    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param int $lines approximate size of input box to display.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response.
     */
    public abstract function response_area_read_only($name, question_attempt $qa,
            question_attempt_step $step, $lines, $context);

    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param int $lines approximate size of input box to display.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response for editing.
     */
    public abstract function response_area_input($name, question_attempt $qa,
            question_attempt_step $step, $lines, $context);

    /**
     * @return string specific class name to add to the input element.
     */
    protected abstract function class_name();
}



/**
 * An unittest format renderer for unittests where the student should use a plain
 * input box, but with a normal, proportional font.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_unittest_format_plain_renderer extends plugin_renderer_base {
    /**
     * @return string the HTML for the textarea.
     */
    protected function textarea($response, $studentscode, $lines, $attributes) {
        $attributes['class'] = $this->class_name() . ' qtype_unittest_response';
        $attributes['rows'] = $lines;
        $attributes['cols'] = 60;
	
	if(empty($response)) {
		return html_writer::tag('textarea', s($studentscode), $attributes);
	}
	return html_writer::tag('textarea', s($response), $attributes);
    }

    protected function class_name() {
        return 'qtype_unittest_plain';
    }

    public function response_area_read_only($name, $qa, $step, $lines, $context, $studentscode) {
        $inputname = $qa->get_qt_field_name($name); 
        return $this->textarea($step->get_qt_var($name), "", $lines, array('readonly' => 'readonly', 'id' => $inputname));
    }

    public function response_area_input($name, $qa, $step, $lines, $context, $studentscode) {
        $inputname = $qa->get_qt_field_name($name);
        return $this->textarea($step->get_qt_var($name), $studentscode, $lines, array('name' => $inputname, 'id' => $inputname)) .
                html_writer::empty_tag('input', array('type' => 'hidden',
                    'name' => $inputname . 'format', 'value' => FORMAT_PLAIN));
    }
}
