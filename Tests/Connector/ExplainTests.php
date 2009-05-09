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
    }
    
}
?>
