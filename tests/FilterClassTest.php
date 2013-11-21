<?php

use Humweb\Filters\Filter;

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

    //Basic filter usage
    public function testBasicAddAndApplyUsage()
    {

        $this->addFilter('title');
        
        $this->filter->add('title', function($v)
        {
            return 'default-filter';
        });
        
        $this->filter->add('title', function($v)
        {
            return 'FooFilter #2';
        });

        $result = $this->filter->apply('title', 'Foo title');
        
        $this->assertEquals('default-filter', $result);
    }


    //Apply filter while passing filter value
    public function testFilterApplyWithPassThruValues()
    {
        $this->addFilter('applytitle', 99, function($v)
        {
            return $v.' FooFilter #1';
        });

        $result =$this->filter->apply('applytitle', 'Foo subtitle');
        
        $this->assertEquals('Foo subtitle FooFilter #1', $result);
    }

    //Filter priority
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

        $result =$this->filter->apply('subtitle', 'Foo subtitle');
        
        $this->assertEquals('FooFilter #2', $result);
    }

    //Clear filters
    public function testAddAndClearFilters()
    {

        $this->addFilter('cleartitle');
        
        $result = $this->filter->apply('cleartitle', 'Foo cleartitle');
        $this->assertEquals('default-filter', $result);

        //clear filters
        $this->filter->clear('cleartitle');

        $result =$this->filter->apply('cleartitle', 'Foo cleartitle');
        
        $this->assertEquals('Foo cleartitle', $result);
    }

    //Remove by Referrence
    public function testAddAndRemoveByReferrence()
    {
        $this->addFilter('reftitle', 100, function($v)
        {
            return 'Ref Title #1';
        }, 'foo-ref');
        
        $this->addFilter('reftitle', 100, function($v)
        {
            return 'Ref Title #2';
        }, 'foo-bar');
        
        $this->addFilter('reftitle', 100, function($v)
        {
            return 'Ref Title #3';
        }, 'foo-ref');
        
        $this->filter->remove('reftitle', 'foo-ref');
        
        $result = $this->filter->apply('reftitle', 'Foo reftitle');
        
        $this->assertEquals('Ref Title #2', $result);
    }

    // Helper method to quickly add tests
    private function addFilter($name, $priority = 100, $callback = null, $ref = null)
    {
        $callback = $callback ?: function($v) { return 'default-filter'; };

        $this->filter->add($name, $callback, $priority, $ref);
    }

}