<?php
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2004 Manuel Lemos, Tomas V.V.Cox,                 |
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
// | Author: Frank M. Kromann <frank@kromann.info>                        |
// +----------------------------------------------------------------------+
//
// $Id: mssql.php,v 1.7 2006/02/05 15:57:32 lsmith Exp $
//

require_once 'MDB2/Driver/Function/Common.php';
// {{{ class MDB2_Driver_Function_mssql
/**
 * MDB2 MSSQL driver for the function modules
 *
 * @package MDB2
 * @category Database
 * @author Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Driver_Function_mssql extends MDB2_Driver_Function_Common
{
     // }}}
    // {{{ executeStoredProc()

    /**
     * Execute a stored procedure and return any results
     *
     * @param string $name string that identifies the function to execute
     * @param mixed  $params  array that contains the paramaters to pass the stored proc
     * @param mixed   $types  array that contains the types of the columns in
     *                        the result set
     * @param mixed $result_class string which specifies which result class to use
     * @param mixed $result_wrap_class string which specifies which class to wrap results in
     * @return mixed a result handle or MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function &executeStoredProc($name, $params = null, $types = null, $result_class = true, $result_wrap_class = false)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $query = 'EXECUTE '.$name;
        $query .= $params ? '('.implode(', ', $params).')' : '';
        return $db->query($query, $types, $result_class, $result_wrap_class);
    }

    // }}}
    // {{{ concat()

    /**
     * returns string to concatenate two or more string parameters
     *
     * @param string $value1
     * @param string $value2
     * @param string $values...
     * @return string to caoncatenate two strings
     * @access public
     **/
    function concat($value1, $value2)
    {
        $args = func_get_args();
        return "(".implode(' + ', $args).")";
    }

    // }}}
}
// }}}
?>
