<?php

class template_functions
{
	
	
	static function html_image($filename=null, $folder = null, $width = 100, $height=100, $img_attrs = null)
	{
		if($filename !== null && $folder !== null) {
		
			
			$thumb_destination_folder 	= UPLOADS.'thumbs'.DS.$folder.DS;
			$cache_hash_id 				= md5(serialize(func_get_args())); 
			$thumb_filename 			= $thumb_destination_folder.$cache_hash_id.".jpg";
			$source_filename 			= UPLOADS.$folder.DS.$filename; 
			
			// Criar a pasta da área no thumbs caso não exista
			if(is_dir($thumb_destination_folder) === false) mkdir($thumb_destination_folder);
			//echo "<small>".$source_filename."</small>";
			
			if(is_file($source_filename)){ // Se não existir o ficheiro original então não fazemos nada
				
			
				if(!is_file($thumb_filename)){ // Se não existir nenhum ficheiro na pasta então temos que o criar
					
					// Include Image Classes
					include (APP."libs/lib/WideImage.inc.php");	
					
					/*	
					if ($scale == 'down')
						$result = $img->resizeDown($width, $height, $fit);
					elseif ($scale == 'up')
						$result = $img->resizeUp($width, $height, $fit);
					else
						$result = $img->resize($width, $height, $fit);
					$result = $img->resize($width, $height, 'inside';
					$format = substr(Request::get('img'), -3);
					img_header($format);
					echo $result->asString($format);
					*/
					
					wiImage::load($source_filename)->resize($width, $height,'outside')->crop(0,0,$width,$height)->saveToFile($thumb_filename,'jpeg',65);
				
				}
					
				$src = UPLOADS_URI.'thumbs/'.$folder.'/'.$cache_hash_id.'.jpg';
				$img_html = '<img src="'.$src.'" width="'.$width.'" height="'.$height.'" '.(($img_attrs !== null)?$img_attrs:'').' />';
					
				echo $img_html;
					
			}
			
		} 
	}
	
	
	/**
	 * Truncate
	 *
	 * Type:     modifier<br>
	 * Name:     truncate<br>
	 * Purpose:  Truncate a string to a certain length if necessary,
	 *           optionally splitting in the middle of a word, and
	 *           appending the $etc string or inserting $etc into the middle.
	 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
	 *          truncate (Smarty online manual)
	 * @author   Nuno Ferreira <nuno.ferreira at wiz dot pt>
	 * @param string
	 * @param integer
	 * @param string
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
	{
	    if ($length == 0)
	        echo '';
	
	    if (strlen($string) > $length) {
	        $length -= strlen($etc);
	        if (!$break_words && !$middle) {
	            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
	        }
	        if(!$middle) {
	            echo substr($string, 0, $length).$etc;
	        } else {
	            echo substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
	        }
	    } else {
	        echo $string;
	    }
	}
	
	/*
	 * Extract file extension in lowercase
	 */
	function file_ext($filename)
	{
		return strtolower(str_replace(".", "", strrchr(basename($filename), ".")));
	}
	
		
	function html_options($array,$selected = null,$css_class=null)
	{
		if(is_array($array)){
			if($css_class !== null) $class="$css_class";
			if($selected !== null) $selected_value = $selected; 
			
			foreach($array as $value=>$text) echo "<option ".(($selected_value == $value)?"selected":"")." value=\"$value\">$text</option>\r\n";
			
		}
	}
	
	function load_css()
	{
		$files = array_values(func_get_args());
		
		// Se existirem argumentos
		if(is_array($files) && count($files)>0) foreach($files as $file) $this->_template_vars['css'][] = $file;

	}
	
	function load_js()
	{
		
		$files = array_values(func_get_args());

		// Se existirem argumentos
		if(is_array($files) && count($files)>0) foreach($files as $file) $this->_template_vars['js'][] = $file;
	}
	
	function display_js()
	{
		$cache_folder 		= UPLOADS."_cache/";
		$cache_folder_uri 	= UPLOADS_URI."_cache/";
		$files = array_values($this->_template_vars['js']);
		
		
		if(is_array($files) && count($files)>0){
			
			$files = array_values($this->_template_vars['js']);
						
			// Get last-modified times from files
			foreach($files as $i=>$file) if(is_file(TEMPLATES.$file)) $modtimes[]=filemtime(TEMPLATES.$file);
						
			$cached_filepath 	= $cache_folder.md5(implode("",$modtimes).implode("",$files)).".js";
			$cached_file_uri	= $cache_folder_uri.basename($cached_filepath);
			
			
			if(!is_file($cached_filepath))
			{
				foreach($files as $i=>$file) {
					
					$already_minified = (substr($file, strlen($file) - strlen("min.js"))=="min.js");
					
					if($already_minified === true) $source[] = file_get_contents(TEMPLATES.$file);
					else $source[] = template_functions::compress_js(file_get_contents(TEMPLATES.$file));
					
				}
				$source = array_unique($source);				
				$source = implode("",$source);
				file_put_contents($cached_filepath,$source);
				
				unset($this->_template_vars['js']);
			}
			
		}
		
		echo "<script type='text/javascript' src='".$cached_file_uri."'></script>";	
	}
	
	function display_css()
	{
		
		$cache_folder 		= UPLOADS."_cache/";
		$cache_folder_uri 	= UPLOADS_URI."_cache/"; 
		
		
		
		$files = array_values($this->_template_vars['css']);
			
		
		// Se existirem ficheiros
		if(is_array($files) && count($files)>0){
			
			
			
			// Get last-modified times from files
			foreach($files as $i=>$file) if(is_file(TEMPLATES.$file)) $modtimes[]=filemtime(TEMPLATES.$file);
			
			
			
			$cached_filepath 	= $cache_folder.md5($this->_template_vars['skin'].implode("",$modtimes).implode("",$files)).".css";
			$cached_file_uri	= $cache_folder_uri.basename($cached_filepath);
			
						
			if(!is_file($cached_filepath))
			{
				//debug::dump(TEMPLATES.$file);
								
				foreach($files as $i=>$file) $source[] = file_get_contents(TEMPLATES_URI.$file);
				
				$source = array_unique($source);
				
				/*if(COMPRESS_CSS === false) $source = implode("",$source);
				else $source = template_functions::compress_css(implode("",$source));
				*/
				
				
				$source = template_functions::compress_css(implode("",$source));
				file_put_contents($cached_filepath,$source);
				
				unset($this->_template_vars['css']);
			}
			
		}
		
		echo '<link rel="stylesheet" href="'.$cached_file_uri.'" type="text/css" media="screen">';
		
	}
	
	
/*
 * Funções para comprimir JS;
 * 
 */
	function compress_js($js)
	{
		
		uses("jsmin");
		
		
		
		if(COMPRESS_JS !== false) return JSMin::minify($js);
		else return $js;
	}
	
	
/*
 * Funções para comprimir CSS;
 * 
 */
	
	function compress_css($css)
	{
		
		// converter line breaks em line feeds
		$css = str_replace("\r\n", "\n", $css);
		
		// preserve empty comment after '>'
        // http://www.webdevout.net/css-hacks#in_css-selectors
        $css = preg_replace('@>/\\*\\s*\\*/@', '>/*keep*/', $css);
        
        // preserve empty comment between property and value
        // http://css-discuss.incutio.com/?page=BoxModelHack
        $css = preg_replace('@/\\*\\s*\\*/\\s*:@', '/*keep*/:', $css);
        $css = preg_replace('@:\\s*/\\*\\s*\\*/@', ':/*keep*/', $css);
        
         // apply callback to all valid comments (and strip out surrounding ws
        $css = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@',array('template_functions', '_commentCB'), $css);
        
         // remove ws around { } and last semicolon in declaration block
        $css = preg_replace('/\\s*{\\s*/', '{', $css);
        $css = preg_replace('/;?\\s*}\\s*/', '}', $css);
        
        // remove ws surrounding semicolons
		$css = preg_replace('/\\s*;\\s*/', ';', $css);
		
		// remove ws around urls
		
        $css = preg_replace('/
                url\\(      # url(
                \\s*
                ([^\\)]+?)  # 1 = the URL (really just a bunch of non right parenthesis)
                \\s*
                \\)         # )
            /x', 'url($1)', $css);
        
        // remove ws between rules and colons
        $css = preg_replace('/
                \\s*
                ([{;])              # 1 = beginning of block or rule separator 
                \\s*
                ([\\*_]?[\\w\\-]+)  # 2 = property (and maybe IE filter)
                \\s*
                :
                \\s*
                (\\b|[#\'"])        # 3 = first character of a value
            /x', '$1$2:$3', $css);
        // remove ws in selectors
        $css = preg_replace_callback('/
                (?:              # non-capture
                    \\s*
                    [^~>+,\\s]+  # selector part
                    \\s*
                    [,>+~]       # combinators
                )+
                \\s*
                [^~>+,\\s]+      # selector part
                {                # open declaration block
            /x',array('template_functions', '_selectorsCB'), $css);
        // minimize hex colors
        $css = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i','$1#$2$3$4$5', $css);
		
		 // remove spaces between font families
        $css = preg_replace_callback('/font-family:([^;}]+)([;}])/',array('template_functions', '_fontFamilyCB'), $css);            
		$css = preg_replace('/@import\\s+url/', '@import url', $css);
		
		// replace any ws involving newlines with a single newline
        $css = preg_replace('/[ \\t]*\\n+\\s*/', "\n", $css);
        
        // separate common descendent selectors w/ newlines (to limit line lengths)
        $css = preg_replace('/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $css);
        
         // Use newline after 1st numeric value (to limit line lengths).
        $css = preg_replace('/
            ((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value
            \\s+
            /x'
            ,"$1\n", $css);
		
		$css = preg_replace_callback('/@import\\s+([\'"])(.*?)[\'"]/',array('template_functions', '_urlCB'), $css);
		$css = preg_replace_callback('/url\\(\\s*([^\\)\\s]+)\\s*\\)/',array('template_functions', '_urlCB'), $css);
        
        return $css;
	}
	
	function _selectorsCB($m)
    {
        // remove ws around the combinators
        return preg_replace('/\\s*([,>+~])\\s*/', '$1', $m[0]);
    }
    
	function _commentCB($m)
    {
    	
        $m = $m[1]; 
        // $m is the comment content w/o the surrounding tokens, 
        // but the return value will replace the entire comment.
        if ($m === 'keep') {
            return '/**/';
        }
        if ($m === '" "') {
            // component of http://tantek.com/CSS/Examples/midpass.html
            return '/*" "*/';
        }
        if (preg_match('@";\\}\\s*\\}/\\*\\s+@', $m)) {
            // component of http://tantek.com/CSS/Examples/midpass.html
            return '/*";}}/* */';
        }

        if (substr($m, -1) === '\\') { // comment ends like \*/
            // begin hack mode and preserve hack
            return '/*\\*/';
        }
        if ($m !== '' && $m[0] === '/') { // comment looks like /*/ foo */
            // begin hack mode and preserve hack
            return '/*/*/';
        }
        
        return ''; // remove all other comments
    }
    
	function _fontFamilyCB($m)
    {
        $m[1] = preg_replace('/
                \\s*
                (
                    "[^"]+"      # 1 = family in double qutoes
                    |\'[^\']+\'  # or 1 = family in single quotes
                    |[\\w\\-]+   # or 1 = unquoted family
                )
                \\s*
            /x', '$1', $m[1]);
        return 'font-family:' . $m[1] . $m[2];
    }
    
	function _urlCB($m)
    {
    	
        $isImport = (0 === strpos($m[0], '@import'));
        if ($isImport) {
            $quote = $m[1];
            $url = $m[2];
        } else {
            // is url()
            // $m[1] is either quoted or not
            $quote = ($m[1][0] === "'" || $m[1][0] === '"')
                ? $m[1][0]
                : '';
            $url = ($quote === '')
                ? $m[1]
                : substr($m[1], 1, strlen($m[1]) - 2);
        }
        if ('/' !== $url[0]) {
            if (strpos($url, '//') > 0) {
                // probably starts with protocol, do not alter
            } else {
                // relative URI, rewrite!
				// rewrite absolute url from scratch!
				// prepend path with current dir separator (OS-independent)
				
				
				$path = TEMPLATES.strtr($url, '/', DS);
				
				// strip doc root
                $path = substr($path, strlen(realpath(ROOT)));
				
                // fix to absolute URL
				$url = strtr($path, DS, '/');
				// remove /./ and /../ where possible
				$url = str_replace('/./', '/', $url);
				$url = str_replace('/../', '/', $url);
                //$url = substr(BASEURI,0,-1).$url;
            }
        }
        return $isImport 
            ? "@import {$quote}{$url}{$quote}"
            : "url({$quote}{$url}{$quote})";
    }
	
}




?>