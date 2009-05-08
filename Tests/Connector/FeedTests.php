<?php
require_once 'PHPUnit/Framework.php';
require_once '../../Connector/Feed.php';
class Jangle_Connector_FeedTest extends PHPUnit_Framework_TestCase
{
    var $feed;
    var $item;
    var $itemUpdated;
    var $itemCreated;
    
    function setUp()
    {
        $this->feed = new Jangle_Connector_Feed('/connector/resources?offset=100');
        $this->item = new Jangle_Feed_Item('http://lccn.loc.gov/2004570428');
    }
    
    function testFeedInit()
    {
        $this->populateFeed();
        $this->assertType('Jangle_Connector_Feed',$this->feed);
        $this->assertEquals('/connector/resources?offset=100',$this->feed->uri);
        $this->assertEquals(100, $this->feed->_offset);
        $this->assertEquals(100, $this->feed->getOffset());
        $this->feed->setOffset(200);
        $this->assertEquals(200, $this->feed->getOffset(),'setOffset() not setting the offset properly!');
        $this->feed->setOffset('300');
        $this->assertEquals(300, $this->feed->getOffset(), 'setOffset() not properly converting numerics to ints!');
        $this->assertEquals(1234, $this->feed->_totalResults);
        $this->assertEquals(1234, $this->feed->getTotalResults());
        $this->feed->setTotalResults('4321');        
        $this->assertEquals(4321, $this->feed->getTotalResults(), 'setTotalResults() not properly converting numerics to ints!');
        $this->assertTrue(is_array($this->feed->alternateFormats));
        $this->assertTrue(array_key_exists('http://jangle.org/vocab/formats#mods',$this->feed->alternateFormats));
        $this->assertEquals('http://example.org/connector/resources?offset=100&format=mods',
        $this->feed->alternateFormats['http://jangle.org/vocab/formats#mods']);
        $this->assertTrue(count($this->feed->stylesheets) == 1);
        $this->assertEquals('http://jangle.googlecode.com/svn/trunk/xsl/AtomMARC21slim2RDFDC.xsl',$this->feed->stylesheets[0]);
        $this->assertTrue(in_array('http://jangle.org/vocab/formats#dc', $this->feed->formats));
    }
    
    function populateFeed() 
    {
        $this->feed->setTotalResults(1234);        
        $this->feed->addAlternateFormat('http://jangle.org/vocab/formats#mods', 'http://example.org/connector/resources?offset=100&format=mods');        
        array_push($this->feed->stylesheets, 'http://jangle.googlecode.com/svn/trunk/xsl/AtomMARC21slim2RDFDC.xsl');     
        array_push($this->feed->formats, 'http://jangle.org/vocab/formats#dc');   
    }
    
    function populateEntry()
    {
        $xml = $this->marcXmlRecord();
        $this->item->setContent($xml, 'application/rdf+xml', 'http://jangle.org/vocab/formats#dc');       
        $tz = Date('P');
        $this->itemCreated = '2009-04-29T15:44:00'.$tz;
        $this->item->setCreated($this->itemCreated);   
        $this->itemUpdated = Time();
        $this->item->setUpdated($this->itemUpdated);   
        $this->item->addRelationship('item');                   
        $this->item->addRelationship('actor', 'http://another.example.org/actors/4321');        
        $this->item->addAlternateFormat('http://metadataregistry.org/uri/DataFormat/mods',
         $this->item->baseUri()."?format=mods");    
        $this->item->addLink('alternate', 'http://lccn.loc.gov/2004570428.html', 'text/html', 'View as HTML'); 
        $this->item->stylesheet = 'http://www.loc.gov/standards/mods/v3/MARC21slim2RDFDC.xsl';
        $this->item->title = 'J\'son [sound recording]';
        array_push($this->item->categories, 'opac');
        $this->item->description = 'Take a look -- Silly games -- I\'ll never stop loving you --
         Don\'t blame me -- Love games -- Need a friend -- Don\'t hold back -- Down, down baby 
         -- Radio -- It should\'ve been me -- I can\'t go for that -- Thinkin\' about u.';
        $this->item->author = 'J\'son';           
    }
    
    function populateSearch()
    {
        $this->feed->setTotalResults(1234);
        $this->loadMarcRecords();
    }
    
    function testFeedEntry()
    {
        $this->populateEntry();
        $this->assertType('Jangle_Feed_Item',$this->item);        
        $this->assertEquals('http://lccn.loc.gov/2004570428', $this->item->baseUri());
        $this->assertEquals('http://lccn.loc.gov/2004570428', $this->item->id);

        $this->assertEquals($this->marcXmlRecord(), $this->item->_content);
        $this->assertEquals('application/rdf+xml', $this->item->_contentType);
        $this->assertEquals('http://jangle.org/vocab/formats#dc', $this->item->_format);

        $this->assertEquals(strtotime($this->itemCreated), $this->item->_created);

        $this->assertEquals($this->itemUpdated, $this->item->_updated);

        $this->assertTrue(array_key_exists('http://jangle.org/vocab/Entities#Item', $this->item->relationships));
        $this->assertEquals('http://lccn.loc.gov/2004570428/items',
         $this->item->relationships['http://jangle.org/vocab/Entities#Item']);

        $this->assertEquals('http://another.example.org/actors/4321',
         $this->item->relationships['http://jangle.org/vocab/Entities#Actor']);    

        $this->assertTrue(array_key_exists('http://metadataregistry.org/uri/DataFormat/mods',
         $this->item->alternateFormats));
        $this->assertEquals('http://lccn.loc.gov/2004570428?format=mods',
         $this->item->alternateFormats['http://metadataregistry.org/uri/DataFormat/mods']);

        $this->assertTrue(array_key_exists("alternate",$this->item->_links));
        $this->assertTrue(is_array($this->item->_links['alternate']));
        $this->assertEquals('http://lccn.loc.gov/2004570428.html', $this->item->_links['alternate'][0]['href']);
        $this->assertEquals('text/html', $this->item->_links['alternate'][0]['type']);
        $this->assertEquals('View as HTML', $this->item->_links['alternate'][0]['title']);
 
        $ary = $this->item->toArray();
        $this->assertEquals('http://lccn.loc.gov/2004570428', $ary['id']);
        $this->assertEquals('J\'son [sound recording]', $ary['title']);
        $this->assertEquals($this->itemUpdated, strtotime($ary['updated']));
        $this->assertEquals($this->itemCreated, $ary['created']);
        $this->assertEquals('Take a look -- Silly games -- I\'ll never stop loving you --
         Don\'t blame me -- Love games -- Need a friend -- Don\'t hold back -- Down, down baby 
         -- Radio -- It should\'ve been me -- I can\'t go for that -- Thinkin\' about u.',
         $ary['description']);
        $this->assertEquals($this->marcXmlRecord(), $ary['content']);
        $this->assertEquals('application/rdf+xml', $ary['content_type']);
        $this->assertEquals('http://www.loc.gov/standards/mods/v3/MARC21slim2RDFDC.xsl', $ary['stylesheet']);
        $this->assertEquals('http://jangle.org/vocab/formats#dc', $ary['format']);
        $this->assertTrue(is_array($ary['alternate_formats']));
        $this->assertTrue(array_key_exists('http://metadataregistry.org/uri/DataFormat/mods', $ary['alternate_formats']));
        $this->assertEquals('http://lccn.loc.gov/2004570428?format=mods',
         $ary['alternate_formats']['http://metadataregistry.org/uri/DataFormat/mods']);     
        $this->assertTrue(is_array($ary['relationships']));
        $this->assertTrue(array_key_exists('http://jangle.org/vocab/Entities#Actor', $ary['relationships']));
        $this->assertEquals('http://another.example.org/actors/4321', $ary['relationships']['http://jangle.org/vocab/Entities#Actor']);
        $this->assertTrue(array_key_exists('http://jangle.org/vocab/Entities#Item', $ary['relationships']));
        $this->assertEquals('http://lccn.loc.gov/2004570428/items', $ary['relationships']['http://jangle.org/vocab/Entities#Item']);     
        $this->assertTrue(is_array($ary['categories']));
        $this->assertTrue(in_array('opac', $ary['categories']));
        $this->assertEquals('J\'son', $ary['author']);
        $this->assertTrue(is_array($ary['links']));
        $this->assertTrue(array_key_exists('alternate', $ary['links']));
        $this->assertTrue(is_array($ary['links']['alternate']));
        $this->assertEquals('text/html', $ary['links']['alternate'][0]['type']);
        $this->assertEquals('http://lccn.loc.gov/2004570428.html', $ary['links']['alternate'][0]['href']);
        $this->assertEquals('View as HTML', $ary['links']['alternate'][0]['title']);
    }
    
    function testFeedSerialization()
    {
        $this->populateFeed();
        $this->loadMarcRecords();
        $this->assertEquals(100, count($this->feed->getDataItems()));
        $json = $this->feed->toJSON();
        $this->assertEquals('string', getType($json));
        $this->assertEquals($this->feed->toArray(), json_decode($json, true));
        $d = $this->feed->getDataItems();
        $this->assertEquals('http://lccn.loc.gov/2006579928', $d[0]->id);
        $this->assertEquals('http://lccn.loc.gov/2004570428', $d[99]->id);
    }
    
    function testSearchInit()
    {
        $this->feed = new Jangle_Connector_Search('http://conn.example.org/connector/resources?offset=100&query=foo');
        $this->populateSearch();        
        $this->assertType('Jangle_Connector_Search', $this->feed);
        $this->assertEquals($this->feed->_type, 'search');
        $this->assertEquals(100, count($this->feed->getDataItems()));
        $ary = $this->feed->toArray();
        $this->assertEquals('search', $ary['type']);
    }
    
    function marcXmlRecord()
    {
        $marc = <<<___MARC
<record xmlns="http://www.loc.gov/MARC21/slim" xmlns:cinclude="http://apache.org/cocoon/include/1.0" xmlns:zs="http://www.loc.gov/zing/srw/">
  <leader>01401cjm a22003611a 4500</leader>
  <controlfield tag="001">13477248</controlfield>
  <controlfield tag="005">20081119085419.0</controlfield>
  <controlfield tag="007">sd fsngnnmmned</controlfield>
  <controlfield tag="008">040202s1996    cauppn|           | eng d</controlfield>
  <datafield tag="010" ind1=" " ind2=" ">

    <subfield code="a">  2004570428</subfield>
  </datafield>
  <datafield tag="024" ind1="1" ind2=" ">
    <subfield code="a">720616202826</subfield>
  </datafield>
  <datafield tag="028" ind1="0" ind2="2">
    <subfield code="a">HR-62028-2</subfield>

    <subfield code="b">Hollywood Records</subfield>
  </datafield>
  <datafield tag="035" ind1=" " ind2=" ">
    <subfield code="a">(DLC)   2004570428</subfield>
  </datafield>
  <datafield tag="035" ind1=" " ind2=" ">
    <subfield code="a">(OCoLC)ocm34507522</subfield>

  </datafield>
  <datafield tag="040" ind1=" " ind2=" ">
    <subfield code="a">SVP</subfield>
    <subfield code="c">SVP</subfield>
    <subfield code="d">DLC</subfield>
  </datafield>
  <datafield tag="042" ind1=" " ind2=" ">

    <subfield code="a">lcderive</subfield>
  </datafield>
  <datafield tag="050" ind1="0" ind2="0">
    <subfield code="a">SDA 88132</subfield>
  </datafield>
  <datafield tag="100" ind1="0" ind2=" ">
    <subfield code="a">J'son,</subfield>

    <subfield code="c">singer.</subfield>
    <subfield code="4">prf</subfield>
  </datafield>
  <datafield tag="245" ind1="1" ind2="0">
    <subfield code="a">J'son</subfield>
    <subfield code="h">[sound recording] /</subfield>
    <subfield code="c">J'son.</subfield>

  </datafield>
  <datafield tag="246" ind1="3" ind2=" ">
    <subfield code="a">Jason</subfield>
  </datafield>
  <datafield tag="260" ind1=" " ind2=" ">
    <subfield code="a">Burbank, CA :</subfield>
    <subfield code="b">Hollywood Records,</subfield>

    <subfield code="c">p1996.</subfield>
  </datafield>
  <datafield tag="300" ind1=" " ind2=" ">
    <subfield code="a">1 sound disc :</subfield>
    <subfield code="b">digital ;</subfield>
    <subfield code="c">4 3/4 in.</subfield>
  </datafield>

  <datafield tag="511" ind1="0" ind2=" ">
    <subfield code="a">J'son, vocals; with instrumental acc. and background vocals.</subfield>
  </datafield>
  <datafield tag="508" ind1=" " ind2=" ">
    <subfield code="a">Executive producers Minetta Gammage and David Esterson.</subfield>
  </datafield>
  <datafield tag="500" ind1=" " ind2=" ">
    <subfield code="a">Compact disc.</subfield>

  </datafield>
  <datafield tag="505" ind1="0" ind2=" ">
    <subfield code="a">Take a look -- Silly games -- I'll never stop loving you -- Don't blame me -- Love games -- Need a friend -- Don't hold back -- Down, down baby -- Radio -- It should've been me -- I can't go for that -- Thinkin' about u.</subfield>
  </datafield>
  <datafield tag="650" ind1=" " ind2="0">
    <subfield code="a">Rhythm and blues music.</subfield>
  </datafield>
  <datafield tag="650" ind1=" " ind2="0">

    <subfield code="a">Popular music</subfield>
    <subfield code="y">1991-2000.</subfield>
  </datafield>
  <datafield tag="906" ind1=" " ind2=" ">
    <subfield code="a">7</subfield>
    <subfield code="b">cbc</subfield>
    <subfield code="c">copycat</subfield>

    <subfield code="d">3</subfield>
    <subfield code="e">ncip</subfield>
    <subfield code="f">20</subfield>
    <subfield code="g">y-genmusic</subfield>
  </datafield>
  <datafield tag="925" ind1="0" ind2=" ">
    <subfield code="a">acquire</subfield>

    <subfield code="b">2 copies</subfield>
    <subfield code="x">policy default</subfield>
  </datafield>
  <datafield tag="952" ind1=" " ind2=" ">
    <subfield code="a">muzerec</subfield>
  </datafield>
  <datafield tag="955" ind1=" " ind2=" ">

    <subfield code="a">vn28 2004-02-02 to MBRS/RS</subfield>
    <subfield code="e">vn28 2004-02-02 copy 2 to MBRS/RS</subfield>
    <subfield code="i">vk37 2008-11-19 cdam</subfield>
  </datafield>
  <datafield tag="985" ind1=" " ind2=" ">
    <subfield code="c">OCLC</subfield>
    <subfield code="e">srreplace 2005-08</subfield>

  </datafield>
</record>
___MARC;
          return $marc;
    }
    
    function loadMarcRecords()
    {
        $doc = DOMDocument::load('data/marc.xml');
        $records = $doc->getElementsByTagName('record');
        $lccn_uri = 'http://lccn.loc.gov/';        
        $xpath = new DOMXpath($doc);
        $mrcNs = $xpath->registerNamespace('mrc', 'http://www.loc.gov/MARC21/slim');
        foreach($records as $record) {
            $elem = $xpath->query('./mrc:datafield[@tag="010"]/mrc:subfield[@code="a"]', $record);
            $lccn = trim($elem->item(0)->nodeValue);
            $item = new Jangle_Feed_Item($lccn_uri.$lccn);
            $t = $xpath->query('./mrc:datafield[@tag="245"]/mrc:subfield[@code="a"]', $record);
            $item->title = $t->item(0)->nodeValue;
            $item->setContent($record->ownerDocument->saveXML($record), 'application/rdf+xml', 'http://jangle.org/vocab/formats#dc');
            $item->addRelationship('item');
            $m = $xpath->query('./mrc:controlfield[@tag="005"]');
            $mod = $m->item(0)->nodeValue;
            $dateParts = array(array(8,2), array(10,2), array(12,2), array(4,2), array(6,2), array(0,4));
            $dateArgs = array();
            foreach($dateParts as $part) {
                array_push($dateArgs, setType(substr($mod, $part[0], $part[1]), 'int'));
            }
            $item->setUpdated = mktime($dateArgs[0], $dateArgs[1], $dateArgs[2], $dateArgs[3], $dateArgs[4], $dateArgs[5]);
            $item->addAlternateFormat('http://metadataregistry.org/uri/DataFormat/mods',
             $item->baseUri()."?format=mods");    
            $item->addLink('alternate', $item->baseUri().'.html', 'text/html', 'View as HTML');  
            $a = $xpath->query('./mrc:datafield[@tag="100" or @tag="110" or @tag="111" or @tag="130"]/mrc:subfield[@code="a"]', $record);
            if($a) {
                $item->author = $a->item(0)->nodeValue;
            }          
            array_push($item->categories, 'opac');
            $this->feed->addDataItem($item);
        }
    }
}
?>