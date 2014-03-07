==========================================================
Todo in order to get this question type running properly:
==========================================================

You may try out this question type with some given example files in the folder EXAMPLE_FILES.

1. javaunittest was tested on Ubuntu 10.4 with Moodle2.3 and Moodle2.5, with Firefox and Google Chrome

2. Module Path:
    Put this module into this sub directory: moodle/question/type/

3. Compilation and execution:
    In config.php set the proper 'PATH_TO_JAVAC', 'PATH_TO_JAVA' and 'PATH_TO_JUNIT' !!!
    Note: There is also a security manager which checks for non-proper student-responses. 
	  This way you should not worry about the student messing up the system (polfile-file).
