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

// {{{ Jangle_Connector_Feed
/**
 * A Jangle Connector Feed object.
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
class Jangle_Connector_Feed
{
    /**
     * The request URI
     *
     * @var string
     **/
    var $uri;
    
    /**
     * Time of response
     *
     * @var time
     **/
    var $time;
    
    /**
     * Offset of first data item in set.
     *
     * @var int
     **/
    var $_offset;
    
    /**
     * Total number of items in data set
     *
     * @var int
     **/
    var $_totalResults;
    
    /**
     * Type of API response.  Should always be 'feed'.
     *
     * @var string
     * @access private
     **/
    var $_type = 'feed';
    
    /**
     * An array to store extensions.  There is no real expectation of how these
     * would work.
     *
     * @var array
     **/
    var $extensions = array();
    
    /**
     * An array of URLs to XSLT Stylesheets.
     *
     * @var array
     **/
    var $stylesheets = array();
    
    /**
     * An array of Jangle format URIs that appear in the feed items.
     *
     * @var array
     **/
    var $formats = array();
    
    /**
     * An array of resources object contained in the feed.
     *
     * @var array
     **/
    var $_dataItems = array();
    
    /**
     * An array of categories that apply to *all* of dataItems in the feed.
     *
     * Categories should (but do not have to) be defined in the 
     * Jangle_Connector_Services response.
     *
     * @var array
     **/
    var $categories = array();
    
    /**
     * An associative array of alternate formats available for the feed.
     *
     * @var array
     **/
    var $alternateFormats = array();
    
    /**
     * The base URI of the connector.
     *
     * @var string
     **/
    var $_connectorBase;
    

    /**
     * Constructor for Jangle Connector API Feed responses.
     *
     * @param string $uri Request URI
     *
     * @return void
     * @author Ross Singer
     **/

    function __construct($uri)
    {
        $this->uri     = $uri;
        $this->_offset = 0;
        $url           = parse_url($uri);
        // If the offset is present in the request URI, set it from there.
        if ($url['query']) {
            parse_str($url['query']);
            if (isset($offset)) {
                $this->_offset = $offset;
                settype($this->_offset, 'int');
            }
        }
    }
    
    /**
     * Sets the item offset of the resources.
     *
     * @param int $int offset
     *
     * @return void
     * @author Ross Singer
     **/
    function setOffset($int) 
    {
        if (!is_numeric($int)) {
            throw new InvalidArgumentException('Offset must be a number!');
        }
        if (!is_int($int)) {
            settype($int, 'int');
        }
        $this->_offset = $int;
    }
    
    /**
     * Gets the offset of the first data item in the set.
     *
     * @return int
     * @author Ross Singer
     **/
    function getOffset()
    {
        return $this->_offset;
    }
    
    /**
     * Sets the total number of resources in the set.
     *
     * @param int $int Number of results
     *
     * @return void
     * @author Ross Singer
     **/
    function setTotalResults($int) 
    {
        if (!is_numeric($int)) {
            throw new InvalidArgumentException('Total results must be a number!');
        }
        if (!is_int($int)) {
            settype($int, 'int');
        }
        $this->_totalResults = $int;
    }
    
    /**
     * Gets the number of total results in the set.
     *
     * @return int
     * @author Ross Singer
     **/
    function getTotalResults()
    {
        return $this->_totalResults;
    }
    
    /**
     * Adds an alternate format to the feed data.
     *
     * @param string $formatUri   Jangle Format URI
     * @param string $uriToFormat URL to alternate format feed
     *
     * @return void
     * @author Ross Singer
     **/
    function addAlternateFormat($formatUri, $uriToFormat) 
    {
        $this->alternateFormats[$formatUri] = $uriToFormat;
    }
    
    /**
     * Adds a Jangle_Feed_Item to the feed.
     *
     * @param Jangle_Feed_Item $dataItem Feed Item object
     *
     * @return void
     * @author Ross Singer
     **/
    function addDataItem($dataItem) 
    {
        if (get_class($dataItem) != 'Jangle_Feed_Item') {
            throw new InvalidArgumentException('Only Jangle_Feed_Items can be added
             to array!');
        }
        array_push($this->_dataItems, $dataItem);
    }
    
    /**
     * Gets the array of feed data items
     *
     * @return array
     * @author Ross Singer
     **/
    function getDataItems()
    {
        return $this->_dataItems;
    }
    
    /**
     * Deletes the data item at the requested index.
     *
     * @param int $pos Array position to delete
     *
     * @return bool Boolean whether or not anything was deleted
     * @author Ross Singer
     **/
    function deleteDataItem($pos)
    {
        if ($this->_dataItems[$pos]) {
            $orig             = $this->_dataItems[$pos];
            $this->_dataItems = array_splice($this->_dataItems, $pos);
            if ($this->_dataItems[$pos] == $orig) {
                $deleted = false;
            } else {
                $deleted = true;
            }
        } else {
            $deleted = false;
        }
        return $deleted;
    }
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
        if (!$this->time) {
            $this->time = time();
        }
        $ary = array('request'=>$this->uri, 'type'=>$this->_type, 
        'time'=>date('c', $this->time), 
        'offset'=>$this->offset);
        if (count($this->alternateFormats) > 0) {
            $ary['alternate_formats'] = $this->alternateFormats;
        }
        $ary['totalResults'] = $this->totalResults ? $this->totalResults : 0;
        if (count($this->extensions) > 0) {
            $ary['extensions'] = $this->extensions;
        }
        
        if (count($this->stylesheets) > 0) {
            $ary['stylesheets'] = $this->stylesheets;
        }
        
        if (count($this->categories) > 0) {
            $ary['categories'] = $this->categories;
        }
        if (count($this->formats) > 0) {
            $ary['formats'] = $this->formats;
        }        
        $data = array();
        foreach ($this->_dataItems as $item) {
            array_push($data, $item->toArray());
        }        
        $ary['data'] = $data;
        return $ary;
    } 
    
    /**
     * Convenience function to turn the Feed array into its JSON serialization.
     *
     * @return string
     * @author Ross Singer
     **/
    function toJSON()
    {
        return json_encode($this->toArray());
    }
    // }}}    
}
// }}}
// {{{ Jangle_Feed_Item
/**
 * An individual resource to be transported in a Jangle Feed.
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
 **/
class Jangle_Feed_Item
{
    /**
     * The Item's "Title"
     *
     * @var string
     **/
    var $title;
    
    /**
     * The actual payload of the data item.  If content is set, 
     * contentType must also be.
     *
     * @var string
     **/
    var $_content;
    
    /**
     * The mime type of the data item's payload.  
     * Must be set if $content has a value.
     *
     * @var string
     **/
    var $_contentType;
    
    /**
     * The Jangle Format URI of the data item's payload.
     *
     * @var string
     **/
    var $_format;
    
    /**
     * A summary of the item's content.  Optional.
     *
     * @var string
     **/
    var $description;
    
    /**
     * The timestamp of the resource's creation.  Optional.
     *
     * @var int
     **/
    var $_created;
    
    /**
     * The timestamp of the resource's last modification date.
     *
     * @var int
     **/
    var $_updated;    
    
    /**
     * URI to an XSLT Stylesheet for this resource's content.  Optional.
     *
     * @var string
     **/
    var $stylesheet;
    
    /**
     * The creator of the resource.  Optional
     *
     * @var string
     **/
    var $author;
    
    /**
     * The URI of the resource
     *
     * @var string
     **/
    var $id;

    /**
     * An associative array of Atom style link relationships.
     *
     * @var array
     **/
    var $_links = array();

    /**
     * An associative array of alternate formats available for the resource.
     *
     * @var array
     **/
    var $alternateFormats = array();

    /**
     * An array of category terms that apply to this resource.
     * These should correspond to category definitions in the services response.
     *
     * @var array
     **/
    var $categories = array();

    /**
     * An associative array of Jangle relationships.
     *
     * @var array
     **/
    var $relationships = array();
    
    /**
     * Constructor for a Feed Item
     *
     * @param string $id URI of resource
     *
     * @return void
     * @author Ross Singer
     **/
    function __construct($id) 
    {
        $this->id = $id;
    }
    
    /**
     * Sets the content and content type of the item's payload.
     *
     * @param string $content resource data
     * @param string $mime    Mime type of the content
     * @param string $format  The Jangle format URI of the content
     *
     * @return void
     * @author Ross Singer
     **/
    function setContent($content, $mime, $format)
    {
        $this->_content     = $content;
        $this->_contentType = $mime;
        $this->_format      = $format;
    }
    
    /**
     * Sets the created time stamp.
     *
     * @param mixed $date This can be either a date or time string or time integer
     *
     * @return void
     * @author Ross Singer
     **/
    function setCreated($date) 
    {
        if (is_int($date)) {
            $this->_created = $date;
        } else {
            $this->_created = strtotime($date);
        }
    }
    
    /**
     * Sets the last modified time stamp.
     *
     * @param mixed $date This can be either a date or time string or time integer
     *
     * @return void
     * @author Ross Singer
     **/
    function setUpdated($date) 
    {
        if (is_int($date)) {
            $this->_updated = $date;
        } else {
            $this->_updated = strtotime($date);
        }
    }    
    
    /**
     * Adds a Jangle Entity Relationship to this resource
     *
     * @param string $type Jangle entity type (singular)
     * @param string $uri  URI to related resource.  Optional.
     *
     * @return void
     * @author Ross Singer
     **/
    function addRelationship($type, $uri=null) 
    {
        if (!in_array(strtolower($type), 
            array('actor', 'collection', 'item', 'resource'))) {
            throw new InvalidArgumentException('Type must be a valid Jangle
             entity!');
        }
        if ($uri != null) {
            $this->relationships["http://jangle.org/vocab/Entities#".
            ucfirst(strtolower($type))] = $uri;
        } else {
            $this->relationships["http://jangle.org/vocab/Entities#".
            ucfirst(strtolower($type))] = $this->id."/".$type."s";
        }
    }
    
    /**
     * Adds an alternate Jangle format URI to the resource.
     *
     * @param string $formatUri URI to define the format of the alternate feed.
     * @param string $uri       URI of the alternate format feed.
     *
     * @return void
     * @author Ross Singer
     **/
    function addAlternateFormat($formatUri,$uri) 
    {
        $this->alternateFormats[$formatUri] = $uri;
    }

    /**
     * Adds a generic "Atom"-style link relationship to the resource.
     *
     * @param string $rel   A valid Atom link relation attribute value
     * @param string $href  The URL of the link relation
     * @param string $type  The content type of the link (optional)
     * @param string $title An optional human readable title string.
     *
     * @return void
     * @author Ross Singer
     **/
    function addLink($rel, $href, $type=null, $title=null) 
    {
        if (!$this->_links[$rel]) {
            $this->_links[$rel] = array();
        }
        $lnk = array("href"=>$href);
        if ($type) {
            $lnk['type'] = $type;
        }
        if ($title) {
            $lnk['title'] = $title;
        }
        array_push($this->_links[$rel], $lnk);
    }
    
    /**
     * Returns the resource URI with no query parameters.
     *
     * @return string
     * @author Ross Singer
     **/
    function baseUri() 
    {
        $uri     = parse_url($this->id);
        $baseUri = '';
        if ($uri["scheme"]) {
            $baseUri .= $uri["scheme"]."://";
        }
        if ($uri["host"]) {
            $baseUri .= $uri["host"];
        }
        if ($uri["port"]) {
            $baseUri .= ":".$uri["port"];
        }
        $baseUri .= $uri["path"];
        return $baseUri;        
    }
    
    /**
     * Serializes the data item as an array to include in a Feed or Search response.
     *
     * @return array
     * @author Ross Singer
     **/
    function toArray() 
    {
        if (!$this->_updated) {
            $this->_updated = Time();
        }
        $hash = array("id"=>$this->id, "updated"=>date("c", $this->_updated));
        if (isset($this->title)) {
            $hash["title"] = $this->title;
        }
        if (isset($this->_contentType)) {
            $hash["content_type"] = $this->_contentType;
        }  
        if (isset($this->_content)) {
            $hash["content"] = $this->_content;
        }  
        if (isset($this->_format)) {
            $hash["format"] = $this->_format;
        }      
        if (isset($this->_created)) {
            $hash["created"] = date("c", $this->_created);
        }
        if (isset($this->author)) {
            $hash["author"] = $this->author;
        }
        if (isset($this->stylesheet)) {
            $hash["stylesheet"] = $this->stylesheet;
        }
        if (isset($this->description)) {
            $hash["description"] = $this->description;
        }        
        if (count($this->relationships) > 0) {
            $hash["relationships"] = $this->relationships;
        }
        if (count($this->categories) > 0) {
            $hash["categories"] = $this->categories;
        }
        if (count($this->alternateFormats) > 0) {
            $hash["alternate_formats"] = $this->alternateFormats;
        }
        if (count($this->_links) > 0) {
            $hash['links'] = $this->_links;
        }
        return $hash;
    }    
} // }}}
/**
 * Jangle_Connector_Search
 *
 * Functionally the same as Jangle_Connector_Feed
 *
 * @category  Services
 * @package   Jangle
 * @author    Ross Singer <rossfsinger@gmail.com>
 * @copyright 2008-2009 Talis Information Limited
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL v. 2.0
 * @version   Release: @package_version@
 * @link      http://code.google.com/p/jangle
 **/
class Jangle_Connector_Search extends Jangle_Connector_Feed
{
    /**
     * Type of API response.  Should always be 'search'.
     *
     * @var string
     * @access private
     **/
    var $_type = 'search';    
    
} // END Jangle_Connector_Search
