<?php
require_once 'PHPUnit/Framework.php';
require_once '../../Connector/Services.php';
class Jangle_Connector_ServicesTest extends PHPUnit_Framework_TestCase
{
    var $service;
    var $entity;
    var $category;
    public function setUp() 
    {
        $this->service = new Jangle_Connector_Services('http://example.org/connector/services');
        $this->entity = new Jangle_Connector_Services_Entity('Resource');
        $this->category = new Jangle_Connector_Services_Category('foobar');
    }
    public function testServiceInit()
    {        
        $this->assertType('Jangle_Connector_Services', $this->service);
        $this->assertEquals('http://example.org/connector/services',$this->service->uri, 'Request URI is not properly set!');
        $this->assertEquals('services',$this->service->getType(), 'Response type is not set!');
        $this->assertEquals('1.0',$this->service->getVersion(), 'Jangle version is not set!');
    }
    
    public function testServiceEntityException()
    {
        $this->setExpectedException('InvalidArgumentException');        
        $entity = new Jangle_Connector_Services_Entity('Non-Entity');
    }
    public function testServiceEntity()
    {   
        $this->assertType('Jangle_Connector_Services_Entity', $this->entity);
        $this->assertEquals('Resource',$this->entity->type, 'Entity type does not match!');
        $this->assertEquals('/resources/',$this->entity->path, 'Path was not automatically set!');
        $this->assertEquals(false, $this->entity->searchable(), 'Entity should not be searchable!');
        $this->entity->setExplainLocation('/resources/search');
        $this->assertEquals(true, $this->entity->searchable(), 'setExplainLocation() is not setting the entity to searchable!');
        $this->assertEquals('/resources/search', $this->entity->explainLocation, 'Incorrect explain location!');
        $this->entity->setExplainLocation(NULL);
        $this->assertEquals(false, $this->entity->searchable(), 'setExplainLocation() is not setting the entity to unsearchable!');
        $this->assertEquals(NULL, $this->entity->explainLocation, 'Incorrect explain location!');        
        $ary = $this->entity->toArray();
        $this->assertTrue(is_array($ary));
        $this->assertTrue(array_keys($ary) == array('Resource'));
        $this->assertEquals('/resources/',$ary['Resource']['path'],'Path is not correct in array!');
        $this->assertEquals('Resources',$ary['Resource']['title'],'Default title was not set in array!');       
        $this->assertEquals('/resources/',$ary['Resource']['path'],'Path is not correct in array!'); 
        $this->assertEquals(false,$ary['Resource']['searchable'],'Entity should not be searchable!');     
        $this->assertTrue(!in_array('categories', array_keys($ary['Resource'])));
        $this->entity->setExplainLocation('/resources/search');
        $this->entity->setTitle('The raison d\'etre');
        $this->assertEquals('The raison d\'etre', $this->entity->title, 'Title was not set!');
        array_push($this->entity->categories, 'foobar');
        $this->assertTrue($this->entity->categories[0] == 'foobar');
        $ary = $this->entity->toArray();
        $this->assertEquals('The raison d\'etre', $ary['Resource']['title'],'Title not displaying properly in toArray()!');
        $this->assertEquals('/resources/search',$ary['Resource']['searchable'],'Explain path not displaying in toArray()!');
        $this->assertTrue(is_array($ary['Resource']['categories']));
        $this->assertTrue($ary['Resource']['categories'][0] == 'foobar');
    }
    public function testServiceCategory()
    {
        $this->assertType('Jangle_Connector_Services_Category', $this->category);
        $this->assertEquals('foobar',$this->category->term, "Term is not set properly!");
        $this->category->setLabel('One bar of foo.');
        $this->assertEquals('One bar of foo.',$this->category->label,'setLabel() is not setting.');
        $this->category->setScheme('http://example.org/categories/');
        $this->assertEquals('http://example.org/categories/',$this->category->scheme,'setScheme() is not setting.');
        $ary = $this->category->toArray();
        $this->assertTrue(is_array($ary));
        $this->assertTrue(array_keys($ary) == array("foobar"));
        $this->assertEquals('One bar of foo.',$ary['foobar']['label'],'Label not displaying properly in toArray()!');
        $this->assertEquals('http://example.org/categories/',$ary['foobar']['scheme'],'Scheme not displaying properly in toArray()!');
    } 
    public function testService()
    {
        $this->testServiceEntity();
        $this->testServiceCategory();
        array_push($this->service->entities, $this->entity);
        array_push($this->service->categories,$this->category);
        $this->assertEquals(1,count($this->service->entities), 'More (or less) than one entity in Service!');
        $this->assertEquals(1,count($this->service->categories), 'More (or less) than one category in Service!');        
        $this->assertEquals($this->entity, $this->service->entities[0], 'Entity in Service not the same as Entity!');
        $this->assertEquals($this->category, $this->service->categories[0], 'Category in Service not the same as Category!');
        $ary = $this->service->toArray();
        $this->assertTrue(is_array($ary));
        $this->assertEquals('http://example.org/connector/services', $ary['request'], 'Array "request" does not equal service request');
        $this->assertEquals('services', $ary['type'], 'Array "type" does not equal "services"!');
        $this->assertEquals('1.0', $ary['version'], 'Array "version" does not equal 1.0!');
        $this->assertEquals($this->service->title, $ary['title'], 'Array "title" does not match service title!');
        $this->assertTrue(in_array('entities',array_keys($ary)));
        $this->assertTrue(is_array($ary['entities']['Resource']));
        $this->assertEquals('/resources/search', $ary['entities']['Resource']['searchable']);
        $this->assertEquals('The raison d\'etre', $ary['entities']['Resource']['title']);
        $this->assertEquals('/resources/', $ary['entities']['Resource']['path']);
        $this->assertTrue(is_array($ary['entities']['Resource']['categories']));
        $this->assertTrue($ary['entities']['Resource']['categories'] == array('foobar'));
        $this->assertTrue(is_array($ary['categories']));
        $this->assertTrue(array_key_exists('foobar',$ary['categories']));
        $this->assertTrue(is_array($ary['categories']['foobar']));
        $this->assertEquals('One bar of foo.',$ary['categories']['foobar']['label']);
        $this->assertEquals('http://example.org/categories/',$ary['categories']['foobar']['scheme']);        
        $json = $this->service->toJSON();
        $this->assertType('string',$json);
        $this->assertEquals($ary, json_decode($json, true));
    }   
}
?>