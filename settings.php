<?php
$settings->add(new admin_setting_configtext('qtype_javaunittest/pathtojava',
        get_string('pathtojava', 'qtype_javaunittest'), get_string('configpathtojava', 'qtype_javaunittest'),
        '/usr/bin/java'));
 
$settings->add(new admin_setting_configtext('qtype_javaunittest/pathtojavac',
        get_string('pathtojavac', 'qtype_javaunittest'), get_string('configpathtojavac', 'qtype_javaunittest'),
        '/usr/bin/javac'));
 
$settings->add(new admin_setting_configtext('qtype_javaunittest/pathtojunit',
        get_string('pathtojunit', 'qtype_javaunittest'), get_string('configpathtojunit', 'qtype_javaunittest'),
        '/usr/share/java/junit.jar'));

$settings->add(new admin_setting_configtext('qtype_javaunittest/pathtohamcrest',
        get_string('pathtohamcrest', 'qtype_javaunittest'),
        get_string('configpathtohamcrest', 'qtype_javaunittest'),
        '/usr/share/java/hamcrest.jar'));

//$settings->add(new admin_setting_configcheckbox('qtype_javaunittest/highlightcode',
//        get_string('highlightcode', 'qtype_javaunittest'),
//        get_string('confighighlightcode', 'qtype_javaunittest'),
//        1)); 

$settings->add(new admin_setting_configcheckbox('qtype_javaunittest/useace',
        get_string('useace', 'qtype_javaunittest'),
        get_string('configuseace', 'qtype_javaunittest'),
        1)); 