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

// {{{ Jangle_Connector_Services
/**
 * A Jangle Connector Services object.
 *
 * This will provide the basic structure of a Jangle 'services' response.
 * It (currently) has no capability of being extended beyond the Jangle 
 * specification.  If you need to do that, it would probably make sense 
 * to subclass it.
 *
 * Usage:  
 *  $svc = new Jangle_Connector_Services('http://example.org/jangle/services/');
 *  $entity = new Jangle_connector_Services_Entity('Actor');
 *  $entity->setPath('/actors/');
 *  $svc->addEntity($entity);
 *  $svc->toJSON();
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
 */
class Jangle_Connector_Services
{
    // {{{ properties
    /**
     * The URI of the Services resource
     *
     * Can be absolute or relative
     *
     * @var string
     * @access public
     */
    var $uri;

    
    /**
     * The name of the Jangle service
     *
     * This should be something descriptive but short.
     *
     * @var string
     * @access public
     */
    var $title;    

    /**
     * The version of the Jangle spec the response conforms to.
     *
     * This should always be '1.0'.  Perhaps it should be a constant.
     *
     * @var string
     * @access private
     */
    var $_version = '1.0';
    
    /**
     * The API response type
     *
     * This should always be 'services'.  Perhaps it should be a constant.
     *
     * @var string
     * @access private
     */
    var $_type = 'services';    
    
    /**
     * The entities defined in this service.
     *
     * An array of Jangle_Services_Entity objects.
     *
     * @var array
     * @access public
     */
    var $entities = array();
    
    /**
     * The categories defined in this service.
     *
     * An array of Jangle_Services_Category objects.
     *
     * @var array
     * @access public
     */
    var $categories = array();    
    // }}}
    // {{{ __construct()
    /**
     * Creates a new Jangle Connector 'Services' object
     *
     * Provides the data structure and the serialization of a standard Jangle
     * Services JSON response.
     *
     * @param string $uri The location of the connector response
     *
     * @access public
     */
    function __construct($uri)
    {
         $this->uri = $uri;
    }        
    // }}}    
    // {{{ getType()
    /**
     * Gets the response type string
     *
     * This should always return the string 'services'.
     *
     * @return string The API response type string
     *
     * @access public
     */
    function getType()
    {
         return $this->_type;
    }        
    // }}}   
    // {{{ getVersion()
    /**
     * Gets the Jangle API version supported.
     *
     * This should always return the string '1.0'.
     *
     * @return string The supported API version string
     *
     * @access public
     */
    function getVersion()
    {
         return $this->_version;
    }        
    // }}}     
    // {{{ setTitle()
    /**
     * Sets the name of the Jangle Connector.
     *
     * Should be descriptive and short.
     *
     * @param string $title Service name
     *
     * @return void
     *
     * @access public
     */
    function setTitle($title)
    {
         $this->title = $title;
    }    
    // }}}
    // {{{ toArray()
    /**
     * Serializes the object as an associative array.
     *
     * The array fits the syntax of the API response prior to being serialized as 
     * JSON.
     *
     * @return array An associative array of the object
     *
     * @access public
     */    
    function toArray() 
    {
        $ary = array("request"=>$this->uri, "type"=>$this->_type, 
        "version"=>$this->_version);
        if (isset($this->title)) {
            $ary["title"] = $this->title;
        }
        if (count($this->entities) > 0) {
            $ary["entities"] = array();
            for ($i = 0; $i < count($this->entities); $i++) {
                $a                        = $this->entities[$i]->toArray();
                $ary["entities"][key($a)] = $a[key($a)];
            }
        }
        if (count($this->categories) > 0) {
            $ary["categories"] = array();
            for ($i = 0; $i < count($this->categories); $i++) {
                $a                          = $this->categories[$i]->toArray();
                $ary["categories"][key($a)] = $a[key($a)];
            }            
        }        
        return $ary;
    }    
    // }}}   
    // {{{ toJSON()
    /**
     * Serializes the object as a Connector API JSON object
     *
     * @return array A JSON object
     *
     * @access public
     */
    function toJSON()
    {        
        return(json_encode($this->toArray()));
    }        
    // }}}     
}

// }}}

// {{{ Jangle_Connector_Services_Entity

/**
 * An Entity definition for a Jangle Connector Services response.
 *
 * This defines the availability of a Jangle 'entity' on a given connector.
 *
 * valid entity types are: Actor, Collection, Item, Resource
 *
 * Usage:  
 *  $svc = new Jangle_Connector_Services('http://example.org/jangle/services/');
 *  $entity = new Jangle_connector_Services_Entity('Actor');
 *  $entity->setPath('/actors/');
 *  $entity->setSearchDocument('http://example.org/connector/actors/explain');
 *  array_push($svc->entities, $entity);
 *  $svc->toJSON();
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
 */
class Jangle_Connector_Services_Entity
{
    // {{{ properties
    /**
     * The path to the entity
     *
     * @var string
     */
    var $path;

    
    /**
     * The name of the entity's resources.
     *
     * This should be something descriptive but short.
     *
     * @var string
     */
    var $title;    

    /**
     * The valid entity types.
     *
     * @var string
     * @access private
     */
    var $_types = array('Actor','Collection','Item','Resource');
    
    /**
     * The entity type
     *
     * This must be one of: Actor, Collection, Item, or Resource
     *
     * @var string
     * @access public
     */
    var $type;    
    
    /**
     * Declares whether or not the entity is searchable
     *
     * @var boolean
     * @access private
     */
    var $_searchable = false;    
    
    /**
     * The URL of the explain document
     *
     * This can be either a fully qualified URL or just the path.
     * If this is NULL, $_searchable must be false.
     * If $_searchable is true, this must have a value.
     *
     * @var string
     * @access public
     */
    var $explainLocation;    
    
    /**
     * The category terms used by this entity
     *
     * The terms here should appear in a Jangle_Services_Category object.
     *
     * @var array
     * @access public
     */
    var $categories = array();    

    // }}}
    // {{{ __construct()
    /**
     * Creates a new Jangle Connector Services Entity object
     *
     * Provides the data structure and the serialization of a standard Jangle
     * Services JSON response.
     *
     * @param string $type The entity type being declared.
     * @param string $path The path to the entity.  If omitted will default to 
     *                     the entity name, downcased, pluralized with slashes
     *                     before and after.
     *
     * @access public
     */
    function __construct($type, $path=null)
    {
        if (!in_array($type, $this->_types)) {
            throw new InvalidArgumentException("Invalid type: ${type}!  Type must
             be: Actor, Collection, Item or Resource.");
        }
        $this->type = $type;
        if ($path) {
            $this->path = $path;
        } else {
            $this->path = '/'.strtolower($type).'s/';
        }
         
    }        
    // }}}    
    
    // {{{ setTitle()
    /**
     * Sets the name of the Jangle Connector.
     *
     * Should be descriptive and short.
     *
     * @param string $title Service name
     *
     * @return void
     *
     * @access public
     */
    function setTitle($title)
    {
         $this->title = $title;
    }        
    // }}}    
    // {{{ searchable()
    /**
     * Returns a boolean defining whether or not the entity is searchable.
     *
     * @return boolean
     *
     * @access public
     */
    function searchable()
    {
        return $this->_searchable;
    }        
    // }}}    
    // {{{ setExplainLocation()
    /**
     * Sets the URL of the entity's explain response.
     *
     * Will also set the entity as searchable.  Passing NULL will "unset"
     * the entity's searchability.
     *
     * @param string $url URL of explain response.
     *
     * @return void
     *
     * @access public
     */
    function setExplainLocation($url)
    {
        $this->explainLocation = $url;
        if ($url) {
            $this->_searchable = true;
        } else {
            $this->_searchable = false;
        }
    }        
    // }}}        
    // {{{ toArray()
    /**
     * Serializes the object as an associative array.
     *
     * This is used by Jangle_Connector_Services to build the API response.
     *
     * @return array An associative array of the object
     *
     * @access public
     */
    function toArray()
    {        
        $ary                       = array($this->type=>array('path'=>$this->path));
        $ary[$this->type]['title'] = $this->title ? $this->title : $this->type.'s';
        if ($this->_searchable) {
            $ary[$this->type]['searchable'] = $this->explainLocation;
        } else {
            $ary[$this->type]['searchable'] = false;
        }
        if (count($this->categories) > 0) {
            $ary[$this->type]['categories'] = $this->categories;
        }
        return $ary;
    }        
    // }}}      
}    
// {{{ Jangle_Connector_Services_Category

/**
 * A Category definition for a Jangle Connector Services response.
 *
 * This defines an Atom style category.
 *
 * Usage:  
 *  $svc = new Jangle_Connector_Services('http://example.org/jangle/services/');
 *  $cat = new Jangle_connector_Services_Category('foobar');
 *  $cat->setScheme('http://example.org/categories/foobar');
 *  array_push($svc->categories, $cat);
 *  $svc->toJSON();
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
 */
class Jangle_Connector_Services_Category
{
    // {{{ properties
    /**
     * The one word token to identify this category.
     *
     * @var string
     */
    var $term;


    /**
     * A human readable description of the category
     *
     * This should be something descriptive but short.
     *
     * @var string
     */
    var $label;    

    /**
     * The scheme that this category applies to.
     *
     * @var string
     * @access public
     */
    var $scheme;

    // }}}
    // {{{ __construct()
    /**
     * Creates a new Jangle Connector Services Category object
     *
     * Provides an Atom style category for the Jangle Connector API services 
     * response.
     *
     * @param string $term A single word that is used to identify the category.
     *
     * @access public
     */
    function __construct($term)
    {
        $this->term = $term;
    }        
    // }}}    
    // {{{ setLabel()
    /**
     * Sets a human readable description of the category.
     *
     * Should be descriptive and short.
     *
     * @param string $label Category description
     *
     * @return void
     *
     * @access public
     */
    function setLabel($label)
    {
         $this->label = $label;
    }        
    // }}} 
    // {{{ setScheme()
    /**
     * A URI that sets the context of the category.
     *
     * @param string $uri Scheme URI
     *
     * @return void
     *
     * @access public
     */
    function setScheme($uri)
    {
         $this->scheme = $uri;
    }        
    // }}}           
    // {{{ toArray()
    /**
     * Serializes the object as an associative array.
     *
     * This is used by Jangle_Connector_Services to build the API response.
     *
     * @return array An associative array of the object
     *
     * @access public
     */
    function toArray()
    {        
        $ary = array($this->term=>array());
        if (isset($this->scheme)) {
            $ary[$this->term]["scheme"] = $this->scheme;
        }
        if (isset($this->label)) {
            $ary[$this->term]["label"] = $this->label;
        }        
        return $ary;
    }        
    // }}}      
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