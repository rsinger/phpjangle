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
    var $_longName;    
    
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
    var $tags = array();    
    
    /**
     * An associative array to define an icon for this search.
     *
     * @var array
     **/
    var $_image = array();    
    
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
    var $languages = array();
    
    /**
     * The valid input encodings accepted in the search terms.
     *
     * @var array
     **/
    var $inputEncodings = array();
    
    /**
     * The character encoding of the feed.
     *
     * @var array
     **/
    var $outputEncodings = array();
    
    /**
     * The SRU Context sets supported by the search.
     *
     * @var array
     **/
    var $_contextSets = array();
    
    /**
     * Constructor for a Jangle Explain response object.
     *
     * @param string $uri The request URI
     *
     * @return Jangle_Connector_Explain
     * @author Ross Singer
     **/
    function __construct($uri)
    {
        $this->uri = $uri;
    }         
    
    /**
     * Sets the _shortName variable.  Value must be a string, 16 characters or less.
     *
     * @param string $shortName The short name of the search.
     *
     * @return void
     * @author Ross Singer
     **/
    function setShortName($shortName)
    {
        if (strlen($shortName) > 16) {
            throw new InvalidArgumentException("Input must be 16 characters or less
             in length!");
        }
        $this->_shortName = $shortName;
    }
    
    /**
     * Getter for the search's short name.
     *
     * @return string
     * @author Ross Singer
     **/
    function getShortName() 
    {
        return $this->_shortName;
    }

    /**
     * Sets the _longName variable.  Value must be a string, 48 characters or less.
     *
     * @param string $longName The long name of the search.
     *
     * @return void
     * @author Ross Singer
     **/
    function setLongName($longName)
    {
        if (strlen($longName) > 48) {
            throw new InvalidArgumentException("Input must be 48 characters or less
             in length!");
        }
        $this->_longName = $longName;
    }
    
    /**
     * Getter for the search's long name.
     *
     * @return string
     * @author Ross Singer
     **/
    function getLongName() 
    {
        return $this->_longName;
    }  

    /**
     * Sets the _description variable.  Value must be a string, 1024 characters
     * or less.
     *
     * @param string $desc The search description.
     *
     * @return void
     * @author Ross Singer
     **/
    function setDescription($desc)
    {
        if (strlen($desc) > 1024) {
            throw new InvalidArgumentException('Input must be 1024 characters or less
             in length!');
        }
        $this->_description = $desc;
    }
    
    /**
     * Getter for the search's description
     *
     * @return string
     * @author Ross Singer
     **/
    function getDescription() 
    {
        return $this->_description;
    } 
    
    /**
     * Sets an image to represent the search service.
     *
     * @param string $url    The location of the image.
     * @param string $type   The content-type of the image file (optional)
     * @param int    $width  The width, in pixels, of the image (optional)
     * @param int    $height The height, in pixels, of the image (optional)
     *
     * @return void
     * @author Ross Singer
     **/
    function setImage($url, $type=null, $width=null, $height=null)     
    {
        $this->_image = array_filter(array('location' => $url, 'type' => $type,
         'width' => $width, 'height' => $height));
    }
    
    /**
     * Gets the image attributes as an associative array.
     *
     * @return array
     * @author Ross Singer
     **/
    function getImage()
    {
        return $this->_image;
    }
    
    /**
     * Sets the _developer variable.  Value must be a string, 64 characters or less.
     *
     * @param string $dev The name of the developer or maintainer
     *
     * @return void
     * @author Ross Singer
     **/
    function setDeveloper($dev)
    {
        if (strlen($dev) > 64) {
            throw new InvalidArgumentException("Input must be 64 characters or less
             in length!");
        }
        $this->_developer = $dev;
    }
    
    /**
     * Getter for the search's developer.
     *
     * @return string
     * @author Ross Singer
     **/
    function getDeveloper() 
    {
        return $this->_developer;
    }  
    
    /**
     * Sets the _attribution variable.  Value must be a string,
     * 256 characters or less.
     *
     * @param string $text The attribution text.
     *
     * @return void
     * @author Ross Singer
     **/
    function setAttribution($text)
    {
        if (strlen($text) > 256) {
            throw new InvalidArgumentException("Input must be 256 characters or less
             in length!");
        }
        $this->_attribution = $text;
    }
    
    /**
     * Getter for the attribution string.
     *
     * @return string
     * @author Ross Singer
     **/
    function getAttribution() 
    {
        return $this->_attribution;
    }  
    
    /**
     * Sets the search results syndication rights.  Value must be a string,
     * of the value: "open", "limited", "private" or "closed".
     *
     * @param string $syndRight The syndication rights of the feed.
     *
     * @return void
     * @author Ross Singer
     **/
    function setSyndicationRight($syndRight)
    {
        $valid = array('open', 'limited', 'private', 'closed');
        if (!in_array(strtolower($syndRight), $valid)) {
            throw new InvalidArgumentException("Input must be one of: 'open', 
             'limited', 'private' or 'closed'!");                              
        }

        $this->_syndicationRight = strtolower($syndRight);
    }
    
    /**
     * Getter for syndication right.
     *
     * @return string
     * @author Ross Singer
     **/
    function getSyndicationRight() 
    {
        return $this->_syndicationRight;
    }
    
    /**
     * Adds a Context Set to the explain response.
     *
     * @param Jangle_Connector_Context_Set $contextSet A context set object
     *
     * @return void
     * @author Ross Singer
     **/
    function addContextSet($contextSet)
    {
        if (get_class($contextSet) != 'Jangle_Connector_Context_Set') {
            throw new InvalidArgumentException('Argument must be a
             Jangle_Connector_Context_Set!');
        }
        $this->_contextSets[$contextSet->getName()] = $contextSet;
    }    
    
    /**
     * Returns the associative array of context set objects associated with the
     * search service.  The array keys are the short name attribute of the context
     * set object.
     *
     * @return array
     * @author Ross Singer
     **/
    function getContextSets()
    {
        return $this->_contextSets;
    }   
    
    /**
     * Returns a context set object by short name.
     *
     * @param string $name Context set short name
     *
     * @return Jangle_Connector_Context_Set
     * @author Ross Singer
     **/
    function getContextSetByName($name)
    {
        return $this->_contextSets[$name];
    } 
    
    /**
     * Returns a context set object by identifier URI
     *
     * @param string $identifier Context set identifier URI
     *
     * @return Jangle_Connector_Context_Set
     * @author Ross Singer
     **/
    function getContextSetByIdentifier($identifier)
    {
        foreach ($this->_contextSets as $c) {
            if ($c->getIdentifier() == $identifier) {
                return $c;
            }
        }
        
    }
    
    /**
     * Serializes the explain object as an associative array.
     *
     * @return array
     * @author Ross Singer
     **/
    function toArray()
    {
        $ary = array('request' => $this->uri, 'type' => $this->_type);
        if ($this->_shortName) {
            $ary['shortname'] = $this->_shortName;
        }
        if (!isset($this->_description)) {
            throw new RuntimeException('The description has not been set!');
        }
        $ary['description'] = $this->_description;
        if (!isset($this->template)) {
            throw new RuntimeException('The search template has not been set!');
        }
        $ary['template'] = $this->template;     
        if ($this->_developer) {
            $ary['developer'] = $this->_developer;
        }
        if ($this->contact) {
            $ary['contact'] = $this->contact;
        }
        
        if (count($this->tags) > 0) {
            $ary['tags'] = $this->tags;
        }
        if ($this->_longName) {
            $ary['longname'] = $this->_longName;
        }
        
        if (count($this->_image) > 0) {
            $ary['image'] = $this->_image;
        }
        
        if ($this->_attribution) {
            $ary['attribution'] = $this->_attribution;
        }
        
        if ($this->_syndicationRight) {
            $ary['syndicationright'] = $this->_syndicationRight;
        }
        if (isset($this->adultContent)) {
            $ary['adultcontent'] = $this->adultContent;
        }
        
        if (count($this->languages) > 0) {
            $ary['language'] = $this->languages;
        }
        if (count($this->inputEncodings) > 0) {
            $ary['inputencodings'] = $this->inputEncodings;
        }
        if (count($this->outputEncodings) > 0) {
            $ary['outputencodings'] = $this->outputEncodings;
        }
        
        if ($this->exampleQuery || count($this->_contextSets) > 0) {
            $ary['query'] = array();
            if ($this->exampleQuery) {
                $ary['query']['example'] = $this->exampleQuery;
            }
            foreach ($this->_contextSets as $c) {
                if (!$ary['query']['context-sets']) {
                    $ary['query']['context-sets'] = array();
                }
                array_push($ary['query']['context-sets'], $c->toArray());
            }
        }
        return $ary;
    }
    
    /**
     * Serializes the object as a Jangle API JSON explain response
     *
     * @return string
     * @author Ross Singer
     **/
    function toJSON()
    {
        return json_encode($this->toArray());
    }
}
// }}}


// {{{ Jangle_Connector_Context_Set
/**
 * A Jangle Connector Context Set (CQL Search Description) object.
 *
 * This will provide the basic structure of a Jangle 'explain' response.
 * It (currently) has no capability of being extended beyond the Jangle 
 * specification.  If you need to do that, it would probably make sense 
 * to subclass it.
 *
 * Usage:  
 *  $contextSet = new Jangle_Connector_Context_Set('dc',
 *   'info:srw/cql-context-set/1/dc-v1.1');
 *  array_push($contextSet->indexes, 'title');
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
 */
class Jangle_Connector_Context_Set
{
    /**
     * The short name of the context set
     *
     * @var string
     **/
    var $_name;
    
    /**
     * The identifier URI for the context set
     *
     * @var string
     **/
    var $_identifier;
    
    /**
     * The indexes available to search from this context set.
     *
     * @var array
     **/
    var $indexes = array();
    
    /**
     * Creates a new Context Set object
     *
     * @param string $name       The short name of the context set
     * @param string $identifier The context set's identifier URI
     *
     * @return Jangle_Connector_Context_Set
     * @author Ross Singer
     **/
    function __construct($name, $identifier)
    {
        $this->_name       = $name;
        $this->_identifier = $identifier;
    }
    
    /**
     * Getter for Context Set name
     *
     * @return string
     * @author Ross Singer
     **/
    function getName()
    {
        return $this->_name;
    }
    
    /**
     * Getter for Context Set identifier
     *
     * @return string
     * @author Ross Singer
     **/
    function getIdentifier()
    {
        return $this->_identifier;
    }
        
    /**
     * Serializes the context set object into an associative array.
     *
     * @return array
     * @author Ross Singer
     **/
    function toArray()
    {
        $ary = array('name' => $this->_name, 'identifier' => $this->_identifier);
        if (count($this->indexes) > 0) {
            $ary['indexes'] = $this->indexes;
        }
        return $ary;
    }
}
// }}}