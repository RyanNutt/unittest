<?php

/**
 * The edit form class for this question type. Each time a question is created moodle uses the
 * edit form to collect data from the teacher. With this form the following attributes of a question 
 * need to be defined: name, question text, geven code and the JUnit test class. Affter editing this 
 * form a question is created in the database with the form's attributes.
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
defined('MOODLE_INTERNAL') || die();

/**
 * unittest question type editing form.
 *
 */
class qtype_unittest_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        //var_dump($mform); 
        global $DB, $question;
        global $PAGE;

        $conf = get_config('qtype_unittest');

        // Need to load JS for Ace
        if ($conf->useace) {
            $PAGE->requires->js('/question/type/unittest/ext/ace/src-min-noconflict/ace.js');
            $PAGE->requires->yui_module('moodle-qtype_unittest-loader', 'M.unittest_loader.edit_page', array(array()));
        }

        $loaded_initialy = optional_param('reloaded_initialy', 1, PARAM_INT);

        $qtype = question_bank::get_qtype('unittest');

        $definitionoptions = $this->_customdata['definitionoptions'];
        $attachmentoptions = $this->_customdata['attachmentoptions'];

        $mform->removeElement('defaultmark');
        $mform->addElement('hidden', 'defaultmark', 1);

        //-------------------------- size of the response field                                             
        $mform->addElement('select', 'responsefieldlines', get_string('responsefieldlines', 'qtype_unittest'), $qtype->response_sizes());
        $mform->setDefault('responsefieldlines', 15);

        //-------------------------- "Given Code" Text-Area                                             
        $mform->addElement('textarea', 'givencode', get_string('givencode', 'qtype_unittest'), array('cols' => 80, 'rows' => 20));
        $mform->setType('givencode', PARAM_RAW);
        $mform->addHelpButton('givencode', 'givencode', 'qtype_unittest');

        //-------------------------- "Test class" Text-Area
        //$mform->addElement('textarea', 'testclassname', get_string('testclassname', 'qtype_unittest'), array('cols' => 80, 'rows' => 1));
        //$mform->setType('testclassname', PARAM_RAW);
        //$mform->addRule('testclassname', null, 'required');
        //$mform->addHelpButton('testclassname', 'testclassname', 'qtype_unittest');
        $mform->addElement('textarea', 'junitcode', get_string('uploadtestclass', 'qtype_unittest'), array('cols' => 80, 'rows' => 20));
        $mform->setType('junitcode', PARAM_RAW);
        $mform->addRule('junitcode', null, 'required');
        $mform->addHelpButton('junitcode', 'uploadtestclass', 'qtype_unittest');

        // Attached file
        $mform->addElement('header', 'attachmentheader',
                get_string('attachments','qtype_unittest'));
        $opts = $this->fileoptions; 
        $opts['subdirs'] = false; 
        $mform->addElement('filemanager', 
                'datafiles', 
                get_string('attachments', 'qtype_unittest'),
                null,
                $opts
                );
        //$mform->addHelpButton('datafiles', 'datafiles', 'qtype_unittest'); 
        //$mform->addHelpButton('datafiles', 'datafiles', 'qtype_unittest'); 
        $this->add_interactive_settings(); 
        
        
    }

    //this methode is called to preprocess the data for the edit form in the case of reediting the question
    protected function data_preprocessing($question) {
        global $CFG, $mform;

        //$question = parent::data_preprocessing($question);

        //$question = $this->data_preprocessing_hints($question);
        //echo '<pre>'.print_r($this->context, true).'</pre>'; 
        if (empty($question->options)) {
            return $question;
        }

        $question->responseformat = 'plain';
        $question->responsefieldlines = $question->options->responsefieldlines;
        $question->givencode = $question->options->givencode;
        $question->testclassname = $question->options->testclassname;
        $question->junitcode = $question->options->junitcode;
        
        $draftid = file_get_submitted_draft_itemid('datafiles');
        $opts = $this->fileoptions;
        $opts['subdirs'] = false;
        $context = context_system::instance(); 
        file_prepare_draft_area($draftid, $this->context->id,
                'qtype_unittest',
                'datafile',
                empty($question->id) ? null : (int)$question->id,
                $opts);
        $question->datafiles = $draftid;
        

        return $question;
    }

    public function qtype() {
        return 'unittest';
    }

}
