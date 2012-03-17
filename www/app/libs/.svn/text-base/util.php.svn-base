<?php 
class util
{

	/************************
		GET ARRAY LIST OF IDS WITH SOME FIELD AS VALUE
		$item_id -> id of object in database, if you whant a list this must be null
		$class -> class from model
		$variable -> this is the value that came in array
	*************************/
	function getItems($item_id = null, $class = null, $variable = null, $conditions = null, $array_key = null, $order = "id asc"){
		models($class);
		
		$items = new $class();
		if(null == $item_id){
			if(null == $conditions){
				$items = $items->findAll(array("order"=>$order));
			}
			else{
				$items = $items->findAll(array("order"=>$order, "conditions"=>$conditions));
			}
		}
		else{
			$items = $items->findAll(array("order"=>$order, "conditions"=>'id = ' . $item_id));
		}
		
		$items_array = array();
		if(null != $items){
			foreach($items as $key=>$value){ 
				
				if($array_key == null){
					$id = $value->id;
				}
				else{
					$id = $value->$array_key;
				}
				$info = $value->$variable;
				$items_array[$id] = $info;
			}
		}
		
		return $items_array;
	}
	
	
	function strtolower($str)
	{
		$patterns 		= array("/À/","/Á/","/Â/","/Ã/","/É/","/Ê/","/Ì/","/Í/","/Ó/","/Ô/","/Õ/","/Ú/","/Ü/","/Ç/","/Ñ/");
		$replacements 	= array("à","á","â","ã","é","ê","ì","í","ó","ô","õ","ú","ü","ç","ñ"); 
		
		$str = strtolower($str);
		
		return preg_replace($patterns, $replacements, $str);
	}
	
	function sanitize_filename($string,$remove_extension = true)
	{
		$string = util::strtolower($string);
		
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		
	
		$string = preg_replace('/&.+?;/', '', $string); // kill entities
		$string = preg_replace('/[^a-z0-9\s-._]/', '', $string);
		$string = preg_replace('/\s+/', '_', $string);
		$string = preg_replace('|-+|', '_', $string);
		$string = trim($string, '_');
				
		// Remove extension
		if($remove_extension === true) {
			$extension = strrchr($string, ".");
			$string = substr($string, 0, -strlen($extension));
		}
			
		return $string;

	}
	
	
	function array_keyvaluepair_exists($key = null, $value = null, $array = null)
    {
    	if(!is_array($array) || $key == null || $value == null) return null;   	
    	
    	foreach($array as $i=>$v) if($v[$key] == $value) return true;
    	
    	return false;
    }
    
 	function array_extractlist_bykey($array = null, $key = null)
    {
    	if($key === null && !is_array($array)) return null;
    	    	
    	$ret = array();
    	foreach($array as $idx=>$value) $ret[$idx] = $value[$key];
    	    	
    	return $ret;
    }
    
    function array_reindex_bykey($key = null, $array = null)
    {
    	if(!is_array($array) || $key == null) return null;
    	
    	$ret = array();
    	foreach($array as $v) $ret[$v[$key]][] = $v;
    	
    	return $ret; 
    }
    
    
    
    /*
     * 	Pega numa array de rows, e extrai um vector com index_key como indices e value_key como valor 
     *  	
     * 		@param string index_key - key do valor a utilizar como índices do vector
     * 		@param string value_key	- key do valor a utilizar como valor do vector
     * 		@param array  array		- array de rows (vinda por exemplo da base de dados)
     * 
     * 		Formato da array de rows
     *      -------------------------
     * 		array
	 *		(
	 *		    [0] => array(
	 *		           	"field_1" => 30,
	 *		            "field_2" => "field_2_value_1",
	 * 					"field_3" => "field_3_value_1"
	 *		    ),
	 *		    [1] => array(
	 *		           	"field_1" => 51,
	 *		            "field_2" => "field_2_value_2",
	 * 					"field_3" => "field_3_value_2"
	 *		    ), 			
	 *		)
	 * 
	 * 		Exemplo de utilização para array acima
	 * 		-------------------------------------------
	 * 		array_vectorize("field_1","field_2",$array)
	 * 
	 * 		Resultado devolvido
	 * 		-------------------
	 * 		array(
     * 		(
     * 			[30] => "field_2_value_1",
     * 			[51] => "field_2_value_2"
     * 		)
     * 
     */
    function array_vectorize($index_key = null, $value_key = null, $array = null, $sort_on_value = null)
    {
    	if(!is_array($array) || $value_key == null) return null;
    	
    	$ret = array();
    	$cnt = 0;
    	foreach($array as $i=>$v) {
    		if($index_key == null) $index = $i;
    		else $index = $v[$index_key];
    		
    		$ret[$index] = $v[$value_key];
    	}
    	
    	if($sort_on_value === true) asort($ret);
		else if($sort_on_value === false) ksort($ret);
		
    	
    	return $ret;
    }
    
    
    
    function array_groupbykeys($key_groups = null, $array = null, $index_key = "id")
    {
    	
    	// Verificar se os parâmetros são passados
    	if( !is_array($key_groups) || !is_array($array) || !(count($key_groups)>0) || !(count($array)>0) ) return null;
    	
    	
    	
    	// Verificar que todas as keys pedidas existem na array
    	
    	$keys = array_keys($array[0]);
    	
    	
    	
    	$groups = array();
    	$grouped_fields = array();
    	
    	foreach($key_groups as $label=>$fields){
			
    		$field_names = explode(",",$fields);
			if($field_names !== array_intersect($field_names,$keys)) {
				trigger_error("Key name not found in array_groupbykeys",E_USER_WARNING);
			} else {
				$groups[$label] = $field_names;
				foreach($field_names as $field) $grouped_fields[] = $field;
			}
    	}
    	
    	
    	
    	// Get flat fields
    	$flat_fields = array_diff($keys,$grouped_fields);

    	    	
    	$ret = array();
    	
    	foreach($array as $idx=>$row){
    		
    		foreach($flat_fields as $flat_field) $ret[$row[$index_key]][$flat_field] = $row[$flat_field];
			
    		foreach($groups as $label=>$fields) foreach($fields as $field) if($row[$field]) $ret[$row[$index_key]][$label][$row[$label."_id"]][$field] = $row[$field];	
    		

    	}
    	    	
    	return $ret;
    	
    	
    	
    	
    }
    

		
	

	
	/*****************
		FUNCTION TO RESIZE IMAGES
	*****************/
	function resize($Dir,$Image,$NewDir,$NewImage,$MaxWidth,$MaxHeight,$Quality) {
		list($ImageWidth,$ImageHeight,$TypeCode)=getimagesize($Dir.$Image);
		
		if(null == $MaxWidth and null == $MaxHeight){
			$MaxWidth = $ImageWidth;
			$MaxHeight = $ImageHeight;
		}
		$ImageType=($TypeCode==1?"gif":($TypeCode==2?"jpeg":($TypeCode==3?"png":FALSE)));
		$OutputFunction="image".$ImageType;
		
		$xscale=$ImageWidth/$MaxWidth;
		$yscale=$ImageHeight/$MaxHeight;
    
	    // Recalculate new size with default ratio
	    if ($yscale>$xscale){
	        $new_width = round($ImageWidth * (1/$yscale));
	        $new_height = round($ImageHeight * (1/$yscale));
	    }
	    else {
	        $new_width = round($ImageWidth * (1/$xscale));
	        $new_height = round($ImageHeight * (1/$xscale));
	    }
  
		if ($ImageType){
			 // Resize the original image
			$imageResized = imagecreatetruecolor($new_width, $new_height);
			$imageTmp     = imagecreatefromjpeg ($Dir.$Image);
			imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $ImageWidth, $ImageHeight);
			$OutputFunction($imageResized,$NewDir.$NewImage,$Quality);
			return true;
		}   
		else{
			return false;
		}
	}
	
	
	
	

	/*****************
		FUNCTION TO RESIZE IMAGES WITH CROP
	*****************/
	function resizeWithCrop($Dir,$Image,$NewDir,$NewImage,$MaxWidth,$MaxHeight,$Quality) {
		list($ImageWidth,$ImageHeight,$TypeCode)=getimagesize($Dir.$Image);
		if(null == $MaxWidth and null == $MaxHeight){
			$MaxWidth = $ImageWidth;
			$MaxHeight = $ImageHeight;
		}
		$ImageType=($TypeCode==1?"gif":($TypeCode==2?"jpeg":($TypeCode==3?"png":FALSE)));
		$CreateFunction="imagecreatefrom".$ImageType;
		$OutputFunction="image".$ImageType;
		if ($ImageType){
			$ImageSource=$CreateFunction($Dir.$Image);

			/*	Calcula proporções em relação à altura	*/
			$relatedheight = $ImageWidth*$MaxHeight/$MaxWidth;
			$proporcaoheight = 100000;
			if($relatedheight >= $ImageHeight){
				//se eh maior a diferença fica sempre positiva
				$proporcaoheight = $relatedheight - $ImageHeight;
			}
			
			/*	Calcula proporções em relação à largura	*/
			$relatedwidth = $ImageHeight*$MaxWidth/$MaxHeight;
			$proporcaowidth = 100000;
			if($relatedwidth >= $ImageWidth){
				//se eh maior a diferença fica sempre positiva
				$proporcaowidth = $relatedwidth - $ImageWidth;
			}
			//faz a proporção pelo menor. Quanto menor menos crop fará
			if($proporcaoheight < $proporcaowidth){
				//faz resize pela altura da imagem
				$Ratio = $ImageWidth/$ImageHeight;
				$ResizedHeight = $MaxHeight;
				$ResizedWidth = $ResizedHeight * $Ratio;
			}
			else{
				//faz resize pela largura da imagem
				$Ratio = ($ImageHeight/$ImageWidth);
				$ResizedWidth = $MaxWidth;
				$ResizedHeight = $ResizedWidth * $Ratio;
			}

			$ResizedImage=imagecreatetruecolor($MaxWidth,$MaxHeight);
			$trans_colour = imagecolorallocatealpha($ResizedImage, 255, 255, 255, 127);
			imagefill($ResizedImage, 0, 0, $trans_colour);
			
			imagecopyresampled($ResizedImage,$ImageSource,0,0,0,0,$ResizedWidth, $ResizedHeight,$ImageWidth,$ImageHeight);
			$OutputFunction($ResizedImage,$NewDir.$NewImage,$Quality);
			return true;
		}   
		else{
			return false;
		}
	}


	
	/*****************
		UPLOAD IMAGE FUNCTIONS
	*****************/
	function uploadImage($path = null, $file = null){
		uses('inputfilter','debug');
		pear('HTTP/Upload');
		
		if($path == null){
			return null;
		}
		else{
			//$upload = new HTTP_Upload('pt');
			//$upload->setChmod(0777);
			//$file = $upload->getFiles('image_path');
			
			$temp_filename = '';
			$erro = '';
			
			$return_value = array();
		
			if ($file->isValid()) {
					
				if( strtoupper($file->getProp('ext'))!='JPG'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato JPEG';
				} else if( strtolower($file->getProp('type'))!='image/jpeg' && strtolower($file->getProp('type'))!='image/pjpeg'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato JPEG';
				} else if($file->getProp('size') >= 2100000){
					$temp_filename = '';
					$erro = 'O ficheiro de imagem deve ter no máximo 2Mb de tamanho.';
				} else {
					$file->setName('uniq');
					
					$file->setName(strtolower($file->upload["name"]));
					$temp_filename = $file->moveTo($path);
					/*$temp_filename = $file->moveTo("media/news_uploads/");*/
				}
				
			} else if($file->isMissing()){
				$temp_filename = '';
				$erro = 'Não foi feito upload do ficheiro de imagem.';
			} else if($file->isError()){
				$temp_filename = '';
				$erro = $file->errorMsg();
			}
		
			if($erro == ''){
				
				$return_value[0] = $erro;
				$return_value[1] = inputfilter::sanitize($temp_filename);
				return $return_value;
					
			} else {
				$return_value[0] = $erro;
				$return_value[1] = "nodata.jpg";
				return $return_value;
			}
		}
	}

	/*****************
		UPLOAD PDF FUNCTIONS
	*****************/	
	
	function uploadPDF($path = null, $file = null){
		uses('inputfilter','debug');
		pear('HTTP/Upload');
		
		if($path == null){
			return null;
		}
		else{
			
			$temp_filename = '';
			$erro = '';
			
			$return_value = array();
			
			if ($file->isValid()) {
							
				if( strtoupper($file->getProp('ext'))!='PDF'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato PDF';
				} else if( strtolower($file->getProp('type'))!='application/pdf'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato PDF';
				} else if($file->getProp('size') >= 2100000){
					$temp_filename = '';
					$erro = 'O ficheiro pdf deve ter no máximo 2Mb de tamanho.';
				} else {
					$file->setName('uniq');
					
					$file->setName(strtolower($file->upload["name"]));
					$temp_filename = $file->moveTo($path);
					/*$temp_filename = $file->moveTo("media/news_uploads/");*/
				}
				
			} else if($file->isMissing()){
				$temp_filename = '';
				$erro = 'Não foi feito upload do ficheiro.';
			} else if($file->isError()){
				$temp_filename = '';
				$erro = $file->errorMsg();
			}
		
			if($erro == ''){
				
				$return_value[0] = $erro;
				$return_value[1] = inputfilter::sanitize($temp_filename);
				return $return_value;
					
			} else {
				$return_value[0] = $erro;
				$return_value[1] = "nodata.pdf";
				return $return_value;
			}
		}
	}
	
	function uploadSWF($path = null, $file = null){
		uses('inputfilter','debug');
		pear('HTTP/Upload');
		
		if($path == null){
			return null;
		}
		else{
			
			$temp_filename = '';
			$erro = '';
			
			$return_value = array();
			
			if ($file->isValid()) {
							
				if( strtoupper($file->getProp('ext'))!='SWF'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato SWF';
				} else if( strtolower($file->getProp('type'))!='application/x-shockwave-flash'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato SWF';
				} else if($file->getProp('size') >= 2100000){
					$temp_filename = '';
					$erro = 'O ficheiro swf deve ter no máximo 2Mb de tamanho.';
				} else {
					$file->setName('uniq');
					
					$file->setName(strtolower($file->upload["name"]));
					$temp_filename = $file->moveTo($path);
					/*$temp_filename = $file->moveTo("media/news_uploads/");*/
				}
				
			} else if($file->isMissing()){
				$temp_filename = '';
				$erro = 'Não foi feito upload do ficheiro.';
			} else if($file->isError()){
				$temp_filename = '';
				$erro = $file->errorMsg();
			}
		
			if($erro == ''){
				
				$return_value[0] = $erro;
				$return_value[1] = inputfilter::sanitize($temp_filename);
				return $return_value;
					
			} else {
				$return_value[0] = $erro;
				$return_value[1] = "nodata.pdf";
				return $return_value;
			}
		}
	}

	/*****************
		UPLOAD IMAGE FUNCTIONS
	*****************/
	function upload($path = null, $file = null){
		uses('inputfilter','debug');
		pear('HTTP/Upload');
		
		if($path == null){
			return null;
		}
		else{
			//$upload = new HTTP_Upload('pt');
			//$upload->setChmod(0777);
			//$file = $upload->getFiles('image_path');
			
			$temp_filename = '';
			$erro = '';
			
			$return_value = array();
		
			if ($file->isValid()) {
							
				/*if( strtoupper($file->getProp('ext'))!='JPG'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato swf';
				} else if( strtolower($file->getProp('type'))!='image/jpeg' && strtolower($file->getProp('type'))!='image/pjpeg'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato JPEG';
					*/
				if($file->getProp('size') >= 3100000){
					$temp_filename = '';
					$erro = 'O ficheiro deve ter no máximo 3Mb de tamanho.';
				} else {
					$file->setName('uniq');
					
					$file->setName(strtolower($file->upload["name"]));
					$temp_filename = $file->moveTo($path);
				
					/*$temp_filename = $file->moveTo("media/news_uploads/");*/
				}
				
			} else if($file->isMissing()){
				$temp_filename = '';
				$erro = 'Não foi feito upload do ficheiro.';
			} else if($file->isError()){
				$temp_filename = '';
				$erro = $file->errorMsg();
			}
		
			if($erro == ''){
				
				$return_value[0] = $erro;
				$return_value[1] = strip_tags($temp_filename);
				return $return_value;
					
			} else {
				$return_value[0] = $erro;
				$return_value[1] = "nodata.txt";
				return $return_value;
			}
		}
	}
	
	
	function uploadBigFile($path = null, $file = null){
		uses('inputfilter','debug');
		pear('HTTP/Upload');
		
		if($path == null){
			return null;
		}
		else{
			//$upload = new HTTP_Upload('pt');
			//$upload->setChmod(0777);
			//$file = $upload->getFiles('image_path');
			
			$temp_filename = '';
			$erro = '';
			
			$return_value = array();
		
			if ($file->isValid()) {
							
				/*if( strtoupper($file->getProp('ext'))!='JPG'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato swf';
				} else if( strtolower($file->getProp('type'))!='image/jpeg' && strtolower($file->getProp('type'))!='image/pjpeg'){
					$temp_filename = '';
					$erro = 'Só são permitidos ficheiros no formato JPEG';
					*/
				if($file->getProp('size') >= 51000000){
					$temp_filename = '';
					$erro = 'O ficheiro deve ter no máximo 50Mb de tamanho.';
				} else {
					$file->setName('uniq');
					
					$file->setName(strtolower($file->upload["name"]));
					$temp_filename = $file->moveTo($path);
					/*$temp_filename = $file->moveTo("media/news_uploads/");*/
				}
				
			} else if($file->isMissing()){
				$temp_filename = '';
				$erro = 'Não foi feito upload do ficheiro.';
			} else if($file->isError()){
				$temp_filename = '';
				$erro = $file->errorMsg();
			}
		
			if($erro == ''){
				
				$return_value[0] = $erro;
				$return_value[1] = inputfilter::sanitize($temp_filename);
				return $return_value;
					
			} else {
				$return_value[0] = $erro;
				$return_value[1] = "nodata.swf";
				return $return_value;
			}
		}
	}
	
	
	//DICTIONARY
	function getDictionary($word = null, $lang = null){
		models('dictionary');
		uses('debug');
		
		$dictionary = new dictionary();
		if(null == $word){
			if(null == $lang){
				$dictionary = $dictionary->findAll(array("order"=>'id asc'));
			}
			else{
				$dictionary = $dictionary->findAll(array("order"=>'id asc', "conditions"=>'language_id = ' . $lang->id));
			}
		}
		else{
			if(null == $lang){
				$dictionary = $dictionary->findAll(array("order"=>'id asc', "conditions"=>'word = ' . $word));
			}
			else{
				$dictionary = $dictionary->findAll(array("order"=>'id asc', "conditions"=>'word = ' . $word . ' and language_id = ' . $lang->id));
			}
		}
		
		$dictionary_array = array();
		if(null != $dictionary){
			foreach($dictionary as $key=>$value){ 
				
				if($value->parent_id != 0){
					
					$translation = $value->word;
					$pt = new dictionary($value->parent_id);
					
					$dictionary_array[$pt->shortcut] = $translation;
				}
				else{
					$dictionary_array[$value->shortcut] = $value->word;
				}
			}
		}
		return $dictionary_array;
	}
	
	
	function changeLanguage($language_id = null, $control = false, $pageurl = 'homepage/'){//'showLink/'){
		uses('debug');
		models('languages');
		
		session_start();
		
		$last = strlen($pageurl);
		if($pageurl[$last-1] != "/"){
			$pageurl .= "/";
		}
		
		$language = new languages($language_id);
		
		if($language->is_record){
			$language->cod = strtolower($language->cod);
			$_SESSION["language"] = $language->cod;
		}
		else{
			$language = new languages();
			$language = $language->findAll(array("conditions"=>'lower(cod) like "en"'));
			
			if(null != $language and sizeof($language) > 0){
				$language = $language[0];
				$language->cod = strtolower($language->cod);
				$_SESSION["language"] = $language->cod;
			}
		}
		
		if($control){
			$this->_redirect($pageurl . $language->cod);
		}
		
		return $language;		
	}
	
	
	/*
		HEADER FUNCTION 
	*/
	
	function header_function(&$languages, $root, $lang_cod, $lang = null, $params = null, $traducoes = null){
		uses('debug');
		models('languages');
		models('highlights');
		
		session_start();
		
		/////////////////////////////////////////
		//array with keys as ids and valuas as codes
		if(null == $_SESSION["languages"]){
			$languages = util::getItems(null, "languages", "id", null, "cod");
			$_SESSION["languages"] = $languages;
		}
		else{
			$languages = $_SESSION["languages"];
		}
		
		/////////////////////////////////////////
		//verify if session exists, if not get defualt language and city
		if(null == $_SESSION["language"] and $lang_cod == null){
			
			//language code by default
			$language_session_code = util::changeLanguage();
			if(null == $params){
				$this->_redirect($root . '/' . $language_session_code->cod);
			}
			else{
				$this->_redirect($root . '/' . $language_session_code->cod . '/' . $params);
			}
		}
		
		/////////////////////////////////////////
		//in case of session exists but in url the language_code and city id were deleted
		if(null == $lang_cod and null != $_SESSION["language"]){
			if(null == $params){
				$this->_redirect($root . '/' . $_SESSION["language"]);
			}
			else{
				$this->_redirect($root . '/' . $_SESSION["language"] . '/' . $params);
			}			
		}
		
		/////////////////////////////////////////
		//change session language_code if in url  varibles were diferent values
		if(null != $_SESSION["language"]){
			if(null != $lang_cod and $_SESSION["language"] != $lang_cod){
				if(null == $languages[$lang_cod]){
					$lang_cod = $_SESSION["language"];
				}
				util::changeLanguage($languages[$lang_cod]);
			}
		}

		/////////////////////////////////////////
		// build links for language
		$language_links = array();
		
		if(null != $languages){
			foreach ($languages as $key => $code){
				if(null == $params){
					$language_links[$code] = $root . '/' . strtolower($key);			
				}
				else{
					$language_links[$code] = $root . '/' . strtolower($key) . '/' . $params;	
				}
			}
		}
		//$this->tpl->assign("language_links",$language_links);
		////////////////////////
		
		$lang = new languages($languages[$lang_cod]);
		$lang->cod = strtolower($lang->cod);
		$this->tpl->assign("lang",$lang);		
		
		$link_lang = "";
		if($lang->cod == "pt"){
			$link_lang = $language_links[2];
		}
		else{
			$link_lang = $language_links[1];
		}
		$this->tpl->assign("language_links",$link_lang);
		
		////////////////////////
		//all words in dictionary for language
		$traducoes = null;
		if(null == $_SESSION['traducoes'] or $_SESSION['lang'] != $lang_cod){
			$traducoes = util::getDictionary(null, $lang);
			$_SESSION['traducoes'] = $traducoes;
		}
		else{
			$traducoes = $_SESSION['traducoes'];
		}
		$this->tpl->assign("traducoes",$traducoes);
		
		if(null == $_SESSION['lang'] or $_SESSION['lang'] != $lang_cod){
			$_SESSION['lang'] = $lang_cod;
		}
		////////////////////////
		
		
		$highlights = new highlights();
		$highlights = $highlights->findAll(array("order"=>"title", "conditions"=>"active = 1 and language_id = " . $lang->id, "limit"=>"3"));
		$this->tpl->assign("highlights",$highlights);
		
		
	}
	
	
	
	/*
		$from -> email format
		$fromName -> string
		$subject -> string
		$html-> text
		$sendBcc -> boolean
		$address -> array ("User Name"=>"email")
	*/
	function sendEmail($from = null, $fromName = null, $subject = null, $html = null, $sendBcc = true, $address = null, $bccaddress = null){
	
		modules('class.phpmailer');
		uses('configs');
		
		$mail = new PHPMailer();
		$mail->From = $from;
		$mail->FromName = $fromName;
		$mail->Subject  = $subject;
		$mail->Mailer = configs::getMailer();//"sendmail";
		$mail->Host = configs::getHost();
		
		$mail->isHTML(true);
		
		//body email
		$mail->Body = $html;
		
		/*foreach($address as $name => $email){
			$mail->AddAddress($email, $name);
		}   */
		$mail->AddAddress($address);
		//$mail->AddBCC('ldias@wiz.pt', "luis dias");
		if($sendBcc == true){
			if(null != $bccaddress){
				foreach($bccaddress as $name => $vemail){
					$mail->AddBCC($email, $name);
				}
			}
			else{
				$mail->AddBCC(configs::bccEMail());
			}
		}
		
		//debug::trace($mail);
		$control = true;
		if(!$mail->Send()){
			$control = false;
		}
		
		$mail->ClearAddresses();
		
		return $control;
	}
	
	/*CROP IMAGE*/
	
	function resizeCropImage($imageName = null, $MaxWidth = null, $MaxHeight = null, $path = null){
		
		$path_thumbs = "media/thumbs/";
		$path = 'media/'.$path . '/';
		$newImageName = $imageName . "_" . $MaxWidth . "x" . $MaxHeight . ".jpg";
		$imageName .= ".jpg";
		if(!file_exists($path_thumbs . $newImageName)){
			util::resizeWithCrop($path,$imageName,$path_thumbs,$newImageName,$MaxWidth,$MaxHeight,$Quality = 80);
		}
		$image = imagecreatefromjpeg($path_thumbs.$newImageName);
		
		header("Content-type: image/jpeg");
		imagejpeg($image,"",80);
	}
	
	/*HOROSCOPO*/
	
	function getHoroscopo(){
		
		pear("XML/Unserializer");
		
		$xml_file 			= ROOT."media/horoscopos/horoscopos.xml";
		if(is_file($xml_file)) $last_modification 	= filemtime($xml_file);
    	else $last_modification = false;
    	
    	$last_modification_difference = mktime()-$last_modification;
    	$refresh_file = ($last_modification_difference > HOUR);
		if($refresh_file === true) {
			$service_url = "http://www.horoscopofree.com/pt/misc/partnership/Horoscopo.xml";
			$service_content 	= file_get_contents($service_url);
			file_put_contents($xml_file,$service_content);
    	} else {
    		$service_content 	= file_get_contents($xml_file);    		 
    	}
    	
		$xml = new XML_Unserializer();
		$xml->setOption("targetEncoding","ISO-8859-1");
		$xml->unserialize($service_content);
		
		$raw_horoscopos = $xml->getUnserializedData();

		$signos_lookup = array(
			"Áries"			=> "Carneiro",
			"Touro"			=> "Touro",
			"Gêmeos"			=> "Gémeos",
			"Câncer"		=> "Caranguejo",
			"Leão"			=> "Leão",
			"Virgem"		=> "Virgem",
			"Libra"			=> "Balança",
			"Escorpião"		=> "Escorpião",
			"Sagitário"		=> "Sagitário",
			"Capricórnio"	=> "Capricórnio",
			"Aquário"		=> "Aquário",
			"Peixes"		=> "Peixes"
		);
		
		$signos_datas = array(
			array("start"=>mktime(0,0,0,3,21),"end"=>mktime(0,0,0,4,20),"signo"=>"Carneiro"),
			array("start"=>mktime(0,0,0,4,21),"end"=>mktime(0,0,0,5,20),"signo"=>"Touro"),
			array("start"=>mktime(0,0,0,5,21),"end"=>mktime(0,0,0,6,21),"signo"=>"Gémeos"),
			array("start"=>mktime(0,0,0,6,22),"end"=>mktime(0,0,0,7,22),"signo"=>"Caranguejo"),
			array("start"=>mktime(0,0,0,7,23),"end"=>mktime(0,0,0,8,23),"signo"=>"Leão"),
			array("start"=>mktime(0,0,0,8,24),"end"=>mktime(0,0,0,9,22),"signo"=>"Virgem"),
			array("start"=>mktime(0,0,0,9,23),"end"=>mktime(0,0,0,10,22),"signo"=>"Balança"),
			array("start"=>mktime(0,0,0,10,23),"end"=>mktime(0,0,0,11,22),"signo"=>"Escorpião"),
			array("start"=>mktime(0,0,0,11,23),"end"=>mktime(0,0,0,12,21),"signo"=>"Sagitário"),
			array("start"=>mktime(0,0,0,12,22),"end"=>mktime(0,0,0,1,20),"signo"=>"Capricórnio"),
			array("start"=>mktime(0,0,0,1,21),"end"=>mktime(0,0,0,2,19),"signo"=>"Aquário"),
			array("start"=>mktime(0,0,0,2,20),"end"=>mktime(0,0,0,3,20),"signo"=>"Peixes")
		);
		
		
		$today = mktime();
		//get today's sign
		foreach($signos_datas as $datas) if($today >= $datas['start'] && $today <= $datas['end']) break;
		$signo_do_dia = $datas;
		
					
		$horoscopos = array();
		foreach($raw_horoscopos['channel']['item'] as $idx=>$horoscopo){
			
			$description 	= $horoscopo['description'];
			$description 	= substr($description,0,strpos($description,"<a")-strlen($description));
			
			$title			= $signos_lookup[$horoscopo['title']]; 
			
			//Colocar o signo do mês no topo da array
			if($signo_do_dia['signo'] == $signos_lookup[$horoscopo['title']]) {
				
				array_unshift($horoscopos,array(
					"id"			=> $idx,
					"title" 		=> $title,
					"description" 	=> $description
				));
				
			} else {
				
				array_push($horoscopos,array(
					"id"			=> $idx,
					"title"			=> $title,
					"description"	=> $description
				));
				
			}
		}
		
		return $horoscopos;
	}
	
    
    function file_get_contents_proxy($szURL, $szProxy, $iProxyPort = 8080)
    {
        
        $pCurl = curl_init();
        curl_setopt($pCurl, CURLOPT_URL, $szURL);
        curl_setopt($pCurl, CURLOPT_HEADER, 0);
        curl_setopt($pCurl,CURLOPT_PROXY, $szProxy);
        curl_setopt($pCurl,CURLOPT_PROXYPORT, $iProxyPort);
        curl_setopt($pCurl,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($pCurl,CURLOPT_RETURNTRANSFER, true);
        return curl_exec($pCurl);
    }
    
    
	/* Weather */
	function getWeather($citycode = "POXX0039")
    {
    	pear("XML/Unserializer");
		
		$xml_file 			= ROOT."media/meteo/".$citycode.".xml";
        
    	if(is_file($xml_file)) $last_modification 	= filemtime($xml_file);
    	else $last_modification = false;
    	
    	$last_modification_difference = mktime()-$last_modification;
    	$refresh_file = ($last_modification_difference > HOUR);
    	    	
        $service_content = "";
    	if($refresh_file === true) {
			$service_url = "http://xoap.weather.com/weather/local/".$citycode."?ut=C&cc=*&dayf=5&link=xoap&prod=xoap&par=1026225670&key=951e3cd77d482854";
            
            if ($_SERVER['SERVER_NAME']=="refertelecom2008"){
                $service_content = file_get_contents($service_url);
            }
            else
            {
                $service_content = util::file_get_contents_proxy($service_url,'proxy10.refertelecom.pt',8080);
            }    
            //debug::trace($service_content);
            
			file_put_contents($xml_file,$service_content);
    	} else {
    		$service_content 	= file_get_contents($xml_file);    		 
    	}   
        
    	        
		$xml = new XML_Unserializer(); 
		$xml->unserialize($service_content);
		$weather = $xml->getUnserializedData();

		return $weather;
		
    }
	 
	 function homepageWeather(){
		$weather = util::getWeather();
		$weather_array = array();
		if(null != $weather and sizeof($weather)>0){
			$weather_array[] = $weather["cc"]["icon"];
			$weather_array[] = $weather["dayf"]["day"][1]["part"][0]["icon"];
		}
		return $weather_array;
	 }
	 
	
}	
?>
