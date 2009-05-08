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

// {{{ Jangle_Connector_Explain
/**
 * A Jangle Connector Explain (Search Description) object.
 *
 * This will provide the basic structure of a Jangle 'explain' response.
 * It (currently) has no capability of being extended beyond the Jangle 
 * specification.  If you need to do that, it would probably make sense 
 * to subclass it.
 *
 * Usage:  
 *  $svc = new Jangle_Connector_Explain('http://example.org/resources/search');
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
 */
class Jangle_Connector_Explain
{
    
    /**
     * The request URI
     *
     * @var string
     **/
    var $uri;
        
    /**
     * Type of API response.  Should always be 'explain'.
     *
     * @var string
     * @access private
     **/
    var $_type = 'explain';    
    
    /**
     * An array to store extensions.  There is no real expectation of how these
     * would work.
     *
     * @var array
     **/
    var $extensions = array();
    
    /**
     * The short name for this search, max length of 16 chars.
     *
     * @var string
     **/
    var $_shortName;
    
    /**
     * A longer, more descriptive name for this search, max length of 48 chars.
     *
     * @var string
     **/
    var $_shortName;    
    
    /**
     * A human readable description of the search.  Max length 1024 chars.
     *
     * @var string
     **/
    var $_description;
    
    /**
     * An OpenSearch query template to describe how to make requests.
     *
     * @var string
     **/
    var $template;        
    
    /**
     * A contact email address.
     *
     * @var string
     **/
    var $contact;    
    
    /**
     * An array of terms to describe the search.
     *
     * @var array
     **/
    var $tags;    
    
    /**
     * An associative array to define an icon for this search.
     *
     * @var array
     **/
    var $_image;    
    
    /**
     * An example query string.
     *
     * @var string
     **/
    var $exampleQuery;    
    
    /**
     * The name of the person who created this service.
     *
     * @var string
     **/
    var $_developer;    
    
    /**
     * The sources that need to be credited for the data.
     *
     * @var string
     **/
    var $_attribution;
    
    /**
     * The rights surrounding use of the results feeds.
     *
     * @var string
     **/
    var $_syndicationRight;   
    
    /**
     * Indicates whether or not this feed contains adult content
     *
     * @var bool
     **/
    var $adultContent;         
    
    /**
     * The languages of the search results.
     *
     * @var array
     **/
    var $languages;
    
    /**
     * The valid input encodings accepted in the search terms.
     *
     * @var array
     **/
    var $inputEncodings;
    
    /**
     * The character encoding of the feed.
     *
     * @var array
     **/
    var $outputEncodings;
    
    /**
     * The SRU Context sets supported by the search.
     *
     * @var array
     **/
    var $_contextSets;         
}
// }}}