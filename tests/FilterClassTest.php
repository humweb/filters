<?php

use Ryun\Filters\Filter;

class FilterClassTest extends PHPUnit_Framework_TestCase
{

    protected $filter;

    /**
     * Setup resources and dependencies.
     *
     * @return void
     */
    public function setUp()
    {
        $this->filter = new Filter();
    }

    public function testFilterBasicUsage()
    {

        $this->addFilter('title');
        
        $this->filter->add('title', function($v)
        {
            return 'FooFilter #1';
        });
        
        $this->filter->add('title', function($v)
        {
            return 'FooFilter #2';
        });

        $expects = 'FooFilter #1';
        $result = $this->filter->title('Foo title');
        
        $this->assertEquals($expects, $result);
    }

    //Remove By Ref

    //Use apply
    

    public function testFilterPriority()
    {
        $this->addFilter('subtitle', 99, function($v)
        {
            return 'FooFilter #00';
        });

        $this->addFilter('subtitle');
        
        $this->addFilter('subtitle', 101, function($v)
        {
            return 'FooFilter #2';
        });

        $expects = 'FooFilter #2';
        $result =$this->filter->subtitle('Foo subtitle');
        
        $this->assertEquals($expects, $result);
    }

    public function testAddAndClearFilters()
    {

        $this->addFilter('cleartitle');
        
        $expects = 'FooFilter #1';
        $result = $this->filter->cleartitle('Foo cleartitle');
        $this->assertEquals($expects, $result);

        //clear filters
        $this->filter->clear('cleartitle');

        $expects = 'Foo cleartitle';
        $result =$this->filter->cleartitle('Foo cleartitle');
        
        $this->assertEquals($expects, $result);
    }


    // tests

    private function addFilter($name, $priority = 100, $callback = null)
    {
        $callback = $callback ?: function($v) { return 'FooFilter #1'; };

        $this->filter->add($name, $callback, $priority);
    }

}