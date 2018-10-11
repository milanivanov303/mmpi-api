<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait Filterable
{
    /**
     * Supported operators
     *
     * @var array
     */
    protected $operators = [
        '=',
        '>',
        '<',
        '>=',
        '<=',
        '!=',
        '<>',
        '<=>',
        'like'
    ];

    /**
     * Default operator if non was set
     *
     * @var string
     */
    protected $defaultOperator = '=';

    /**
     * Get model table columns
     *
     * @return array
     */
    protected function getColumns()
    {
        return Schema::getColumnListing($this->getTable());
    }

    /**
     * Get defined filters
     *
     * @return array
     */
    public function getFilterableAttributes()
    {
        if (method_exists($this, 'filters')) {
            return array_keys($this->filters());
        }
 
        return array_diff($this->getColumns(), $this->getHidden());
    }

    /**
     * Get filterable parameters
     *
     * @param Request $request
     * @return array
     */
    protected function getOnlyValidParameters ($parameters)
    {    
        // Get mapped attributes if model uses mappable trait
        if (method_exists($this, 'getMappededAttributes')) {
            $parameters = $this->getMappededAttributes($parameters);
        }

        return array_intersect_key(
            $parameters,
            array_flip(
                $this->getFilterableAttributes()
            )
        );
    }

    /**
     * Get filter operator
     *
     * @param $value
     * @return string
     */
    protected function getFilterOperator($name, $value)
    {
        // Get operator from parameter value if exists
        $matches = [];
        preg_match('/' . implode('|', $this->operators) . '/', $value, $matches);

        if ($matches) {
            return $matches[0];
        }

        // Get operator from model filters if there is one defined
        if (method_exists($this, 'filters')) {
            if (
                array_key_exists($name, $this->filters()) &&
                array_key_exists('operator', $this->filters()[$name])
            ) {
                $operator = $this->filters()[$name]['operator'];
                if (in_array($operator, $this->operators)) {
                    return $operator;
                }
            }
        }

        return $this->defaultOperator;
    }

    /**
     * Get filter value
     *
     * @param $value
     * @return string
     */
    protected function getFilterValue($value, $operator)
    {
        $value = trim(
            str_replace($this->operators, '', $value)
        );

        if ($operator === 'like') {
            $value = "%{$value}%";
        }

        return $value;
    }

    /**
     * Get filter callback
     *
     * @param string $name
     * @return null|callable
     */
    protected function getFilterCallback($name)
    {
        // Get calback from model filters if there is one defined
        if (method_exists($this, 'filters')) {
            if (
                array_key_exists($name, $this->filters()) &&
                array_key_exists('callback', $this->filters()[$name])
            ) {
                return $this->filters()[$name]['callback'];
            }
        }
        
        return null;
    }

    /**
     * Get filters
     *
     * @param Request $request
     * @return array
     */
    protected function getFilters($parameters)
    {
        $filters    = [];
        $parameters = $this->getOnlyValidParameters($parameters);

        foreach ($parameters as $name => $value) {
            $array = is_array($value) ? $value : [$value];
            foreach ($array as $value) {
                $operator = $this->getFilterOperator($name, $value);
                array_push(
                    $filters,
                    [
                        'column'   => $name,
                        'operator' => $this->getFilterOperator($name, $value),
                        'value'    => $this->getFilterValue($value, $operator),
                        'callback' => $this->getFilterCallback($name)
                    ]
                );
            }
        }
        return $filters;
    }

    /**
     * Set model filters
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setFilters($parameters)
    {
        $filters = $this->getFilters($parameters);

        $model = $this;
        foreach ($filters as $filter) {
            if (is_callable($filter['callback'])) {
                $model = call_user_func($filter['callback'], $model, $filter['value']);
            } else {
                $model = $model->where($filter['column'], $filter['operator'], $filter['value']);
            }
        }
        
        if (array_key_exists('order_by', $parameters)) {
            $order_by = $parameters['order_by'];

            // Get mapped attribute if model uses mappable trait
            if (method_exists($this, 'getMappededAttribute')) {
                $order_by = $this->getMappededAttribute($order_by);
            }

            $model = $model->orderBy($order_by, $parameters['order_dir'] ?? 'ASC');
        }

        if (array_key_exists('limit', $parameters)) {
            $model = $model->limit($parameters['limit']);
        }
        
        return $model;
    }
}
