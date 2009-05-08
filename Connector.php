 <?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Classes to build Jangle Connector API responses
 *
 * Long description for file (if any)...
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the GNU Public License v. 2.0
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Ltd. http://talis.com/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   SVN: <?php
 $
 ?> Id:$
 * @link      http://code.google.com/p/jangle
 */

require_once 'PEAR.php';


// {{{ Jangle_Connector
/**
 * A Jangle Connector object.
 *
 * This will provide the basic structure of a Jangle 'feed' response.
 *
 * Usage:  
 *  $feed = new Jangle_Connector_Feed('http://example.org/jangle/items/');
 *  $feed->toJSON();
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
  */
class Jangle_Connector
{
    // {{{ properties

    /**
     * The status of foo's universe
     *
     * Potential values are 'good', 'fair', 'poor' and 'unknown'.
     *
     * @var string
     */
    var $_connectorBase;

    /**
     * The status of life
     *
     * Note that names of private properties or methods must be
     * preceeded by an underscore.
     *
     * @var bool
     * @access private
     */
    var $_config = ;
    
    /**
     * Jangle Connector Constructor
     *
     * @return Jangle_Connector
     * @author Ross Singer
     **/
    function __construct($request, $config=null)
    {
    }


}

// }}}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>