<?php namespace Ryun\Filters;

use Closure;

class Filter
{
    /**
     * Filters array
     * @var array
     */
    protected $filters = array();
    protected $sorted = array();

    /**
     * Add a filter
     * 
     * @param string  $name     filter name
     * @param Closure $func     closure function to be applied
     * @param integer $priority priority/weight used for sorting
     * @param string  $ref      reference allows us to remove a specific filter
     */
    public function add($name, Closure $func, $priority = 100, $ref = null)
    {
        if ($func instanceof Closure)
        {
            if ( ! isset($this->filters[$name]))
            {
                $this->filters[$name] = array();
            }

            $payload = array(
                'ref'		 => $ref ?: '',
                'function'   => $func,
                'priority'   => $priority,
            );

            $this->filters[$name][] = $payload;
            unset($this->sorted[$name]);
        }
    }

    /**
     * Apply a filter to something
     * @param  string $name  filter name
     * @param  mixed  $value an item we want to apply the filter for
     * @return mixed
     */
    public function apply($name, $value)
    {
        if ( ! isset($this->filters[$name]))
        {
            return $value;
        }

        foreach ($this->getFilters($name) as $filter)
        {
            $value = $filter['function']($value);
        }

        return $value;
    }

    /**
     * Get all of the filters by priority
     *
     * @param  string  $name
     * @return array
     */
    public function getFilters($name)
    {

        if ( ! isset($this->sorted[$name]))
        {
            $this->sortFilters($name);
        }

        return $this->sorted[$name];
    }

    /**
     * Remove filter by reference
     * 
     * @param  string $name filter name
     * @param  string $ref  filter reference id
     * @return void
     */
    public function remove($name, $ref = null)
    {
        foreach ($this->filters[$name] as $i => $ary)
        {
            if ($ary['ref'] == $ref)
            {
                unset($this->filters[$name][$i]);
            }
        }
        
        unset($this->sorted[$name]);

        return $this;
    }


    /**
     * Clear fiilters by filter name
     * @param  string $name filter name
     * @return void
     */
    public function clear($name)
    {
        unset($this->filters[$name]);
        unset($this->sorted[$name]);
        return $this;
    }

    /**
     * Handles dynamic apply calls
     * @param  string $method     filter name
     * @param  array  $parameters parameters get passed to apply
     * @return mixed
     */
    public function __call($method, $parameters = array())
    {

        if (isset($this->filters[$method]))
        {
            return $this->apply($method, $parameters[0]);
        }

        return $parameters[0];
    }

    /**
     * Sort the filters array by priority.
     *
     * @param  string  $name
     * @return void
     */
    protected function sortFilters($name)
    {
        $this->sorted[$name] = array();

        // If listeners exist for the given event, we will sort them by the priority
        // so that we can call them in the correct order. We will cache off these
        // sorted event listeners so we do not have to re-sort on every events.
        if (isset($this->filters[$name]))
        {
            uasort($this->filters[$name], array($this, 'sortHandler'));

            $this->sorted[$name] = $this->filters[$name];
        }
    }
    
    /**
     * Sort handler (uasort)
     * @param  int $a compare a
     * @param  int $b compare b
     * @return int
     */
    private function sortHandler($a, $b)
    {
        if ($a['priority'] == $b['priority'])
        {
            return 0;
        }

        return ($a['priority'] < $b['priority']) ? -1 : 1;
    }
}
