<?php
// vim: set et ts=4 sw=4 fdm=marker:
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2006 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith                                         |
// | All rights reserved.                                                 |
// +----------------------------------------------------------------------+
// | MDB2 is a merge of PEAR DB and Metabases that provides a unified DB  |
// | API as well as database abstraction for PHP applications.            |
// | This LICENSE is in the BSD license style.                            |
// |                                                                      |
// | Redistribution and use in source and binary forms, with or without   |
// | modification, are permitted provided that the following conditions   |
// | are met:                                                             |
// |                                                                      |
// | Redistributions of source code must retain the above copyright       |
// | notice, this list of conditions and the following disclaimer.        |
// |                                                                      |
// | Redistributions in binary form must reproduce the above copyright    |
// | notice, this list of conditions and the following disclaimer in the  |
// | documentation and/or other materials provided with the distribution. |
// |                                                                      |
// | Neither the name of Manuel Lemos, Tomas V.V.Cox, Stig. S. Bakken,    |
// | Lukas Smith nor the names of his contributors may be used to endorse |
// | or promote products derived from this software without specific prior|
// | written permission.                                                  |
// |                                                                      |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
// | FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE      |
// | REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
// | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS|
// |  OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED  |
// | AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT          |
// | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY|
// | WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE          |
// | POSSIBILITY OF SUCH DAMAGE.                                          |
// +----------------------------------------------------------------------+
// | Author: Lukas Smith <smith@pooteeweet.org>                           |
// +----------------------------------------------------------------------+
//
// $Id: mysql.php,v 1.41 2006/05/14 11:08:43 lsmith Exp $
//

require_once 'MDB2/Driver/Datatype/Common.php';

/**
 * MDB2 MySQL driver
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Driver_Datatype_mysql extends MDB2_Driver_Datatype_Common
{
    // }}}
    // {{{ getTypeDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare an text type
     * field to be used in statements like CREATE TABLE.
     *
     * @param array $field  associative array with the name of the properties
     *      of the field being declared as array indexes. Currently, the types
     *      of supported field properties are as follows:
     *
     *      length
     *          Integer value that determines the maximum length of the text
     *          field. If this argument is missing the field should be
     *          declared to have the longest length allowed by the DBMS.
     *
     *      default
     *          Text value to be used as default for this field.
     *
     *      notnull
     *          Boolean flag that indicates whether this field is constrained
     *          to not be set to null.
     * @return string  DBMS specific SQL code portion that should be used to
     *      declare the specified field.
     * @access public
     */
    function getTypeDeclaration($field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        switch ($field['type']) {
        case 'text':
            $length = array_key_exists('length', $field)
                ? $field['length'] : false;
            $fixed = array_key_exists('fixed', $field) ? $field['fixed'] : false;
            return $fixed ? ($length ? 'CHAR('.$length.')' : 'CHAR(255)')
                : ($length ? 'VARCHAR('.$length.')' : 'TEXT');
        case 'clob':
            if (array_key_exists('length', $field)) {
                $length = $field['length'];
                if ($length <= 255) {
                    return 'TINYTEXT';
                } elseif ($length <= 65535) {
                    return 'TEXT';
                } elseif ($length <= 16777215) {
                    return 'MEDIUMTEXT';
                }
            }
            return 'LONGTEXT';
        case 'blob':
            if (array_key_exists('length', $field)) {
                $length = $field['length'];
                if ($length <= 255) {
                    return 'TINYBLOB';
                } elseif ($length <= 65535) {
                    return 'BLOB';
                } elseif ($length <= 16777215) {
                    return 'MEDIUMBLOB';
                }
            }
            return 'LONGBLOB';
        case 'integer':
            if (array_key_exists('length', $field)) {
                $length = $field['length'];
                if ($length <= 1) {
                    return 'TINYINT';
                } elseif ($length == 2) {
                    return 'SMALLINT';
                } elseif ($length == 3) {
                    return 'MEDIUMINT';
                } elseif ($length == 4) {
                    return 'INT';
                } elseif ($length > 4) {
                    return 'BIGINT';
                }
            }
            return 'INT';
        case 'boolean':
            return 'TINYINT(1)';
        case 'date':
            return 'DATE';
        case 'time':
            return 'TIME';
        case 'timestamp':
            return 'DATETIME';
        case 'float':
            return 'DOUBLE';
        case 'decimal':
            $length = array_key_exists('length', $field) ? $field['length'] : 18;
            return 'DECIMAL('.$length.','.$db->options['decimal_places'].')';
        }
        return '';
    }

    // }}}
    // {{{ _getIntegerDeclaration()

    /**
     * Obtain DBMS specific SQL code portion needed to declare an integer type
     * field to be used in statements like CREATE TABLE.
     *
     * @param string  $name   name the field to be declared.
     * @param string  $field  associative array with the name of the properties
     *                        of the field being declared as array indexes.
     *                        Currently, the types of supported field
     *                        properties are as follows:
     *
     *                       unsigned
     *                        Boolean flag that indicates whether the field
     *                        should be declared as unsigned integer if
     *                        possible.
     *
     *                       default
     *                        Integer value to be used as default for this
     *                        field.
     *
     *                       notnull
     *                        Boolean flag that indicates whether this field is
     *                        constrained to not be set to null.
     * @return string  DBMS specific SQL code portion that should be used to
     *                 declare the specified field.
     * @access protected
     */
    function _getIntegerDeclaration($name, $field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $default = $autoinc = '';;
        if (array_key_exists('autoincrement', $field) && $field['autoincrement']) {
            $autoinc = ' AUTO_INCREMENT PRIMARY KEY';
        } else {
            if (array_key_exists('default', $field)) {
                if ($field['default'] === '') {
                    $field['default'] = (array_key_exists('notnull', $field) && $field['notnull'])
                        ? 0 : null;
                }
                $default = ' DEFAULT '.$this->quote($field['default'], 'integer');
            }
        }

        $unsigned = (array_key_exists('unsigned', $field) && $field['unsigned']) ? ' UNSIGNED' : '';
        $notnull = (array_key_exists('notnull', $field) && $field['notnull']) ? ' NOT NULL' : '';
        $name = $db->quoteIdentifier($name, true);
        return $name.' '.$this->getTypeDeclaration($field).$unsigned.$default.$notnull.$autoinc;
    }

    // }}}
    // {{{ _quoteBLOB()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param bool $quote determines if the value should be quoted and escaped
     * @return string  text string that represents the given argument value in
     *                 a DBMS specific format.
     * @access protected
     */
    function _quoteBLOB($value, $quote)
    {
        $value = $this->_readFile($value);
        if (!$quote) {
            return $value;
        }
        return "'".addslashes($value)."'";
    }

    // }}}
    // {{{ mapNativeDatatype()

    /**
     * Maps a native array description of a field to a MDB2 datatype and length
     *
     * @param array  $field native field description
     * @return array containing the various possible types, length, sign, fixed
     * @access public
     */
    function mapNativeDatatype($field)
    {
        $db_type = strtolower($field['type']);
        $db_type = strtok($db_type, '(), ');
        if ($db_type == 'national') {
            $db_type = strtok('(), ');
        }
        if (array_key_exists('length', $field)) {
            $length = $field['length'];
            $decimal = '';
        } else {
            $length = strtok('(), ');
            $decimal = strtok('(), ');
        }
        $type = array();
        $unsigned = $fixed = null;
        switch ($db_type) {
        case 'tinyint':
            $type[] = 'integer';
            $type[] = 'boolean';
            if (preg_match('/^[is|has]/', $field['name'])) {
                $type = array_reverse($type);
            }
            $unsigned = preg_match('/ unsigned/i', $field['type']);
            $length = 1;
            break;
        case 'smallint':
            $type[] = 'integer';
            $unsigned = preg_match('/ unsigned/i', $field['type']);
            $length = 2;
            break;
        case 'mediumint':
            $type[] = 'integer';
            $unsigned = preg_match('/ unsigned/i', $field['type']);
            $length = 3;
            break;
        case 'int':
        case 'integer':
            $type[] = 'integer';
            $unsigned = preg_match('/ unsigned/i', $field['type']);
            $length = 4;
            break;
        case 'bigint':
            $type[] = 'integer';
            $unsigned = preg_match('/ unsigned/i', $field['type']);
            $length = 8;
            break;
        case 'tinytext':
        case 'mediumtext':
        case 'longtext':
        case 'text':
        case 'text':
        case 'varchar':
            $fixed = false;
        case 'string':
        case 'char':
            $type[] = 'text';
            if ($length == '1') {
                $type[] = 'boolean';
                if (preg_match('/^[is|has]/', $field['name'])) {
                    $type = array_reverse($type);
                }
            } elseif (strstr($db_type, 'text')) {
                $type[] = 'clob';
                if ($decimal == 'binary') {
                    $type[] = 'blob';
                }
                $type = array_reverse($type);
            }
            if ($fixed !== false) {
                $fixed = true;
            }
            break;
        case 'enum':
            $type[] = 'text';
            preg_match_all('/\'.+\'/U', $field['type'], $matches);
            $length = 0;
            $fixed = false;
            if (is_array($matches)) {
                foreach ($matches[0] as $value) {
                    $length = max($length, strlen($value)-2);
                }
                if ($length == '1' && count($matches[0]) == 2) {
                    $type[] = 'boolean';
                    if (preg_match('/^[is|has]/', $field['name'])) {
                        $type = array_reverse($type);
                    }
                }
            }
            $type[] = 'integer';
        case 'set':
            $fixed = false;
            $type[] = 'text';
            $type[] = 'integer';
            break;
        case 'date':
            $type[] = 'date';
            $length = null;
            break;
        case 'datetime':
        case 'timestamp':
            $type[] = 'timestamp';
            $length = null;
            break;
        case 'time':
            $type[] = 'time';
            $length = null;
            break;
        case 'float':
        case 'double':
        case 'real':
            $type[] = 'float';
            $unsigned = preg_match('/ unsigned/i', $field['type']);
            break;
        case 'unknown':
        case 'decimal':
        case 'numeric':
            $type[] = 'decimal';
            $unsigned = preg_match('/ unsigned/i', $field['type']);
            break;
        case 'tinyblob':
        case 'mediumblob':
        case 'longblob':
        case 'blob':
            $type[] = 'blob';
            $length = null;
            break;
        case 'year':
            $type[] = 'integer';
            $type[] = 'date';
            $length = null;
            break;
        default:
            $db =& $this->getDBInstance();
            if (PEAR::isError($db)) {
                return $db;
            }

            return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'mapNativeDatatype: unknown database attribute type: '.$db_type);
        }

        return array($type, $length, $unsigned, $fixed);
    }
}

?>
