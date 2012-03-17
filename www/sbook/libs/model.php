<?php
/**
 * Spellbook: Web application framework
 *
 * Copyright Wiz Interactive (2006)
 *
 * Spellbook is yet another MVC web framework written in PHP. It is 
 * oriented on simplicity and speed. It has been used internally to build 
 * sites for several clients and as such it is perfectly usable in its 
 * current incarnation. Uses PEAR for library routines and Smarty templating.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, 
 * Boston, MA  02110-1301, USA.
 *
 * @author       Jorge Pena <jmgpena@gmail.com>, Nuno Ferreira <koriolis@gmail.com>
 * @version 3.0
 * @package sbook
 */

/**
 * model 
 * 
 * @package 
 * @version $id$
 * @copyright 1997-2005 Wiz Interactive
 * @author Jorge Pena <jmgpena@gmail.com>, Nuno Ferreira <koriolis@gmail.com>
 * @license 
 */
class model
{
	var $_dsn;
    var $_link;
    var $_table;
    var $_fields_info;
    var $_fields_values;
    var $_class_name;
    var $is_record = false; // True if it is a valid record;
     
	/*
     * Tabelas a que as especialidades est�o ligadas e que � preciso verificar em caso de delete
     * 
     * 		Formato:		Array (
     * 								"table_name" 	=> "field_name",
     * 								"other_table"	=>	"other_field"
     * 							  )
     */
    var $linked_to;
    

 
 	function delete($id=null)
    {
        if(!is_numeric($id))  $id = $this->_link->quote($this->id);
        $can_delete = $this->checkLinked($id);
        
        if($can_delete === true) {
        
	        $sql = 'delete from '.$this->_table.' where id='.$id;
	        $val = $this->_link->query($sql,$var);
	        $this->_check_error($val);
	        
	        return true;
	        
        } else {
        	
        	return false;
        	
        }
        
        
    }
	
    
    function checkLinked($id = null)
    {
		
    	if( isset($this->linked_to) && is_array($this->linked_to) && !empty($this->linked_to) ){
    			
    		 
    		foreach($this->linked_to as $linked_table => $foreign_key)
    		{
    			// Incluir se necess�rio o model espec�fico
    			models($linked_table);
    			// Instanciar model da tabela
    			$linked_table_obj = new $linked_table;
    			$rows = $linked_table_obj->count("$linked_table.$foreign_key = {$id}11");
    			// Basta existir pelo menos 1 liga��o para n�o ser poss�vel apagar 
    			if ($linked_table_obj->count("$linked_table.$foreign_key = {$id}")>0) return false;
    			
    		}
    		
    		// Caso o ciclo de verifica��o tenha decorrido at� ao fim sem interrup��es, ent�o n�o h� liga��es
    		return true;
    		
    	} else {
    		
    		
    		// $linked_to n�o foi definida, n�o � array ou est� vazia
    		return true;
    	}

    }
    
    
    function _check_error($res)
    {
        if (PEAR::isError($res))
        {
            trigger_error('DB Error: '.$res->getMessage()."\nQuery: ".$this->_link->last_query); 
            //debug($this->_link); 
            exit;
        }
    }

    /**
     * model 
     * 
     * @access public
     * @return void
     */
    function model($init=null)
    {
        static $_fields_info;

        // Class Name
        $this->_class_name = get_class($this);

        if(!isset($this->_table))
        {
            $this->_table = $this->_class_name;
        }

        // Database Link
		$this->_link = &sBook::DBLink($this->_dsn);


        // Check if table exists
        $this->_link->loadModule('Manager');
        $tables = $this->_link->manager->listTables();
        if(array_search($this->_table,$tables)===false)
        {
            // Create table if query exists
            if(isset($this->_sql_create))
            {
                $val = $this->_link->query($this->_sql_create);
                $this->_check_error($val);
            }
            else
            {
                trigger_error('DB Error: Table '.$this->_table.' does not exist!'); 
            }
        }

        // Fields Info (static)
        if(!isset($_fields_info[$this->_table]))
        {
            $this->_link->loadModule('Reverse');
            $info = $this->_link->reverse->tableInfo($this->_table);
            $this->_check_error($info);
            $_fields_info[$this->_table] = $info;
        }
        $this->_fields_info = $_fields_info[$this->_table];

        // Initialize Class
        if(isset($init))
        {
            if(is_array($init))
            {
                foreach($init as $key=>$value)
                {
                    if($key[0] == '_')
                    {
                        $pos = strpos($key,'_',1);
                        $name = array_search(substr($key,1,$pos-1),$this->_has_one);
                        $field = substr($key,$pos+1);
                        $key = $name.'_'.$field;
                    }
                    $this->$key = $value;
                }
            }
            else
            { 
                $this->findById($init);
            }
        }
    }

    function _insert()
    {
        //var_dump($this->_fields_info);
        foreach($this->_fields_info as $field)
        {
            switch($field['name'])
            {
            case 'id':
                // Auto sequence id
                $field_names[]='id';
                $this->id = $this->_link->nextId($this->_table);
                $field_values[]=$this->id;
                break;
            case 'created_on':
                $field_names[]='created_on';
                $field_values[]=$this->_link->quote(date('Y-m-d H:i:s'));
                break;
            case 'updated_on':
                break;
            default:
                if(isset($this->$field['name']))
                {
                    $field_names[]=$field['name'];
                    $field_values[]=$this->_link->quote($this->$field['name']);
                }
            }
        }
        
        $sql  = 'insert into '.$this->_table.' ('.implode(',',$field_names).') ';
        $sql .= 'values('.implode(',',$field_values).')';
        //var_dump($sql);
        //$var  = array($this->_table);
        $val  = $this->_link->query($sql);
        $this->_check_error($val);
    }

    function _update()
    {
        foreach($this->_fields_info as $field)
        {
            switch($field['name'])
            {
            case 'id':
                break;
            case 'created_on':
                break;
            case 'updated_on':
                $field_names[]='updated_on';
                $field_values[]=$this->_link->quote(date('Y-m-d H:i:s'));
                break;
            default:
                $field_expr[]=' '.$field['name'].'='.$this->_link->quote($this->$field['name']);
            }
        }
        
        $sql  = 'update '.$this->_table.' set'.implode(',',$field_expr).' where id='.$this->id;
        //var_dump($sql);
        //$var  = array($this->_table);
        $val  = $this->_link->query($sql);
        $this->_check_error($val);
    }

    function save()
    {
        if(isset($this->id))
            $this->_update();
        else
            $this->_insert();
        $this->is_record = true;
    }

    function count($condition=null)
    {
        if(isset($condition))
        {
            $where_sql = ' WHERE '.$condition;
        }
        $table = $this->_link->quoteIdentifier($this->_table);
        $sql = 'select count(id) as count from '.$table.$where_sql;
        $val = $this->_link->queryOne($sql);
        $this->_check_error($val);
        return (int)$val;
    }

    /**
     * find 
     * 
     * @param mixed $cmd 
     * @param mixed $options 
     * @access public
     * @return void
     */
    function find($cmd,$options=null,$class=null)
    {
        // FindById
        if(is_numeric($cmd))
        {
            return $this->findById($cmd);
        }
        if(is_array($cmd))
        {
            return $this->findByIds($cmd,$options);
        }
        if($cmd == 'first')
        {
            return $this->findFirst($options);
        }
        if($cmd == 'all')
        {
            return $this->findAll($options);
        }
        trigger_error('model::find() : Args error!');
        return null;
    }

    /**
     * table 
     * 
     * @param mixed $class 
     * @access public
     * @return void
     */
    function &table($class)
    {
        $object = new $class();
        if(is_a($object,'model'))
            return $object;
        else
            return null;
    }

    /**
     * findById 
     *
     * This function gets one row from the table into the current object
     * and returns the row as an associative array.
     * 
     * @param int $id 
     * @access public
     * @return array
     */
    function findById($id)
    {
        $id = $this->_link->quote($id,'integer');

        $sql = 'select * from '.$this->_table.' where id='.$id;
        $val = $this->_link->queryRow($sql);
        $this->_check_error($val);
        $this->is_record = false;
        if(count($val) > 0)
        {
            foreach($val as $key=>$value)
            {
                $this->$key = $value;
            }
            $this->is_record = true;
        }
        return $this;
    }

    /**
     * findByIds 
     * 
     * @param array $ids 
     * @access public
     * @return object array
     */
    function findByIds($ids,$options=null,$to_array = false)
    {
        $id_list = implode(',',$ids);
        $condition = 'id in ('.$id_list.')';

        if(isset($options['conditions']))
        {
            if(is_array($options['conditions']))
            {
                $options['conditions'][] = $condition;
            }
            else
            {
                $temp = $options['conditions'];
                $options['conditions'] = array();
                $options['conditions'][] = $temp;
                $options['conditions'][] = $condition;
            }
        }
        else
        {
            $options['conditions'] = $condition;
        }

        // Does it work???
        //return $this->findAll($options);
        return model::findall($options,$class,$to_array);
    }

    function findFirst($options=null)
    {
        $options['limit'] = 1;
        // Does it work???
        //$res = $this->findAll($options);
        $res = model::findall($options,$class);
        //$this = $res[0];
        return $res[0];
    }

     function findAll($options=null,$toarray=false)
    {
        pear('MDB/QueryTool');

        $query = new MDB_QueryTool();
        $query->setDbInstance($this->_link);
        $query->setTable($this->_table);
        
    	// Add SELECT conditions to the query
		if(isset($options['select']))
        {
            if(is_array($options['select']))
            {
                foreach($options['select'] as $what)
                {
                    $query->addSelect($what,",");
                }
            }
            else
            {
                $query->setSelect($options['select']);
            }
        }
        
		// Add WHERE conditions to the query		
        if(isset($options['conditions']))
        {
            if(is_array($options['conditions']))
            {
                foreach($options['conditions'] as $cond)
                {
                    $query->addWhere($cond);
                }
            }
            else
            {
                $query->setWhere($options['conditions']);
            }
        }
        
        // Add ORDER parameters to the query
        if(isset($options['order']))
        {
            $query->setOrder($options['order']);
        }
        
        // Add LIMIT params to the query
        if(isset($options['limit']))
        {
            if(isset($options['offset']))
                $offset = $options['offset'];
            else
                $offset = 0;
            $query->setLimit($offset,$options['limit']);
        }
        
        // Linkage options (join)
	    if(isset($options['linkone']))
        {
			if(is_array($options['linkone'])){
				foreach($options['linkone'] as $idx=>$value){
					if(array_key_exists($value,$this->_has_one))
		            {
		                $table1 = $this->_table;
		                $table2 = $this->_has_one[$value];
		                $query->setLeftJoin($table2,$table1.'.'.$value.'_id = '.$table2.'.id');
		            }
				}
			} else {
				if(array_key_exists($options['linkone'],$this->_has_one))
	            {
					
	                $table1 = $this->_table;
	                $table2 = $this->_has_one[$options['linkone']];
	                $query->setLeftJoin($table2,$table1.'.'.$options['linkone'].'_id = '.$table2.'.id');
	            }
			}
        }
		
        // parse sql
        $sql = $query->getQueryString();
		
        // do query
        $val = $this->_link->queryAll($sql);
        $this->_check_error($val);
        
        // if $toarray param is true then return array of associative row arrays
        if($toarray === true){
			return $val;
        } else {
	        //debug($val,true);
	        switch (count($val))
	        {
	            case 0:
	                return null;
	                break;
	            /*case 1:
	                $this->model($val[0]);
	                return new $this->_class_name($val[0]); 
	                break;*/
	            default:
	                foreach($val as $row)
	                {
	                    $obj = new $this->_class_name($row);
	                    $obj->is_record = true;
	                    $list[] = $obj;
	                }
	                return $list;
	        }
        }
    }

	function linkOne($rel_name)
    {
        if(array_key_exists($rel_name,$this->_has_one))
        {
            $rel_field = $rel_name.'_id';
            $parent = new $this->_has_one[$rel_name];
            $parent->find($this->$rel_field);
            return $parent;
        }
        return null;
    }

    function linkMany($rel_name)
    {
        if(array_key_exists($rel_name,$this->_has_many))
        {
            $rel_field = $this->_has_many[$rel_name].'_id';
            $children = new $rel_name();
            return $children->findAll(array("conditions"=>$rel_field.'='.$this->id));
        }
        return null;
    }

    function to_string()
    {
        $out = $this->_table." object:\n";
        $vars = get_object_vars($this);
        foreach($vars as $key=>$var)
        {
            if($key[0] != '_')
                $out .= "\t".$key." -> ".$var."\n";
        }
        return $out;
    }

    function quote($str,$type = null)
    {
        return $this->_link->quote($str,$type);
    }
}
?>
