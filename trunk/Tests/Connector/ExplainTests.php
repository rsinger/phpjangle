<?php
require_once 'PHPUnit/Framework.php';
require_once '../../Connector/Explain.php';
class Jangle_Connector_ExplainTest extends PHPUnit_Framework_TestCase
{
    var $explain;
    
    function setUp() 
    {
        $this->explain = new Jangle_Connector_Explain('http://example.org/resources/search/');
    }
    
    function populateExplain()
    {
        $this->explain->template = 'http://example.org/resources/search/?query={searchTerms}&offset={startIndex?}&count={count?}';
        $this->explain->contact = 'developer@example.org';
        array_push($this->explain->tags, "jangle");
        array_push($this->explain->tags, "resources");        
        $this->explain->exampleQuery = 'dc:title = dog';
        $this->explain->adultContent = false;
        array_push($this->explain->languages, 'en-US');
        array_push($this->explain->inputEncodings, "UTF-8");
        array_push($this->explain->outputEncodings, "UTF-8");       
        $this->explain->setShortName('A test search');
        $this->explain->setLongName('An example search for testing');
        $this->explain->setDescription('Use this search for unit testing the
         Jangle_Connector_Explain class.');
        $this->explain->setImage('http://example.org/imgs/1234.jpg', 'image/jpg', 20, 30);
        $this->explain->setDeveloper('Dee Veloper');
        $this->explain->setAttribution('Data courtesy of Jangle.org.');
        $this->explain->setSyndicationRight('open');
        $ctx1 = new Jangle_Connector_Context_Set('dc', 'info:srw/cql-context-set/1/dc-v1.1');
        $ctx1->indexes = array_merge($ctx1->indexes, array('title', 'creator', 'subject', 'identifier'));
        $this->explain->addContextSet($ctx1);
        $ctx2 = new Jangle_Connector_Context_Set('rec', 'info:srw/cql-context-set/2/rec-1.1');
        $ctx2->indexes = array_merge($ctx2->indexes, array('identifier', 'lastModificationDate'));        
        $this->explain->addContextSet($ctx2);        
    }
    
    function testExplainSettings()
    {
        $this->populateExplain();
        $this->assertType('Jangle_Connector_Explain', $this->explain);
        $this->assertEquals('http://example.org/resources/search/', $this->explain->uri);
        $this->assertEquals('http://example.org/resources/search/?query={searchTerms}&offset={startIndex?}&count={count?}',
            $this->explain->template);
        $this->assertEquals('developer@example.org', $this->explain->contact);
        $this->assertTrue(is_array($this->explain->tags));
        $this->assertEquals(array('jangle', 'resources'), $this->explain->tags);
        $this->assertEquals('dc:title = dog', $this->explain->exampleQuery);
        $this->assertTrue(is_array($this->explain->getImage()));
        $img = $this->explain->getImage();
        $this->assertEquals('http://example.org/imgs/1234.jpg', $img['location']);
        $this->assertEquals('image/jpg', $img['type']);
        $this->assertEquals(30, $img['height']);
        $this->assertEquals(20, $img['width']);
        $this->assertEquals('Dee Veloper', $this->explain->getDeveloper());
        $this->assertEquals('Data courtesy of Jangle.org.', $this->explain->getAttribution());
        $this->assertEquals('open', $this->explain->getSyndicationRight());
        $this->assertEquals(2, count($this->explain->getContextSets()));
        $this->assertTrue(array_key_exists('dc', $this->explain->getContextSets()));
        $this->assertTrue(array_key_exists('rec', $this->explain->getContextSets()));
        $dc = $this->explain->getContextSetByName('dc');
        $this->assertTrue(get_class($dc) == 'Jangle_Connector_Context_Set');
        $this->assertEquals('dc', $dc->getName());
        $this->assertEquals('info:srw/cql-context-set/1/dc-v1.1', $dc->getIdentifier());
        $this->assertEquals(array('title', 'creator', 'subject', 'identifier'), $dc->indexes);
        $this->assertEquals(array(), array_diff(array('name' => 'dc', 'identifier'
          => 'info:srw/cql-context-set/1/dc-v1.1', 'indexes' => array('title',
          'creator', 'subject', 'identifier')), $dc->toArray()));
        $rec = $this->explain->getContextSetByIdentifier('info:srw/cql-context-set/2/rec-1.1');
        $this->assertType('Jangle_Connector_Context_Set', $rec);
        $this->assertEquals('rec', $rec->getName());
        $this->assertEquals('info:srw/cql-context-set/2/rec-1.1', $rec->getIdentifier());
        $this->assertEquals(array('identifier', 'lastModificationDate'), $rec->indexes);
    }
    
    function testExplainSerialization()
    {
        $this->populateExplain();
        $ary = $this->explain->toArray();
        $this->assertTrue(is_array($ary));
        $this->assertEquals('explain', $ary['type']);
        $this->assertEquals('http://example.org/resources/search/', $ary['request']);
        $this->assertEquals('A test search', $ary['shortname']);
        $this->assertEquals('An example search for testing', $ary['longname']);
        $this->assertEquals('Use this search for unit testing the
         Jangle_Connector_Explain class.', $ary['description']);
        $this->assertEquals(
            'http://example.org/resources/search/?query={searchTerms}&offset={startIndex?}&count={count?}',
            $ary['template']);
        $this->assertEquals('Dee Veloper', $ary['developer']);
        $this->assertEquals('developer@example.org', $ary['contact']);
        $this->assertEquals(array('jangle', 'resources'), $ary['tags']);
        $this->assertEquals(array('location' => 'http://example.org/imgs/1234.jpg',
         'type' => 'image/jpg', 'height' => 30, 'width' => 20), $ary['image']);
        $this->assertTrue(is_array($ary['query']));
        $this->assertArrayHasKey('example', $ary['query']);
        $this->assertEquals('dc:title = dog', $ary['query']['example']);
    }
    
}
?>
