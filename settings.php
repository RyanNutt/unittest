<?php

$settings->add(new admin_setting_configexecutable('qtype_unittest/pathtojava',
        get_string('pathtojava', 'qtype_unittest'), get_string('configpathtojava', 'qtype_unittest'),
        '/usr/bin/java'));
 
$settings->add(new admin_setting_configexecutable('qtype_unittest/pathtojavac',
        get_string('pathtojavac', 'qtype_unittest'), get_string('configpathtojavac', 'qtype_unittest'),
        '/usr/bin/javac'));
 
$settings->add(new admin_setting_configfile('qtype_unittest/pathtojunit',
        get_string('pathtojunit', 'qtype_unittest'), get_string('configpathtojunit', 'qtype_unittest'),
        '/usr/share/java/junit.jar'));

$settings->add(new admin_setting_configfile('qtype_unittest/pathtohamcrest',
        get_string('pathtohamcrest', 'qtype_unittest'),
        get_string('configpathtohamcrest', 'qtype_unittest'),
        '/usr/share/java/hamcrest.jar'));

$settings->add(new admin_setting_configtext('qtype_unittest/defaulttimeout',
        get_string('defaulttimeout', 'qtype_unittest'),
        get_string('configdefaulttimeout', 'qtype_unittest'),
        2,
        PARAM_INT));

$settings->add(new admin_setting_configcheckbox('qtype_unittest/useace',
        get_string('useace', 'qtype_unittest'),
        get_string('configuseace', 'qtype_unittest'),
        1)); 