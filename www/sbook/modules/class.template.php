<?php
////////////////////////////////////////////////////
// Template - PHP template class
//
// PHP Templating class for sBook 
// Uses a fairly simple method to require_once a php file
// Code is isolated to the class so you only have access to class public data and globals
// 
//
////////////////////////////////////////////////////

/**
 * Template - PHP Template class
 * @package sbook
 * @author Nuno Ferreira <koriols@gmail.com>
 * @copyright 2004-2008 Wiz Interactive
 */
class Template
{
	var $_template_vars;
	var $template_dir;
	
	function assign($tpl_var, $value = null)
    {
        if (is_array($tpl_var)){
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_template_vars[$key] = $val;
                }
            }
        } else {
            if ($tpl_var != '')
                $this->_template_vars[$tpl_var] = $value;
        }
    }
	
	function display($_template_file = false) {
		if($_template_file !== false){
			if(is_array($this->_template_vars)) extract($this->_template_vars, EXTR_SKIP);
			include ($this->template_dir.$_template_file);
			return;
		}
	}
    
    function fetch($_template_file = false) {
        if($_template_file !== false){
            ob_start();
            if(is_array($this->_template_vars)) extract($this->_template_vars, EXTR_SKIP);
            include ($this->template_dir.$_template_file);
            $html = ob_get_clean();
            return $html;
        }
    }
}
?>