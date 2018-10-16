<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

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
    protected function getColumns(): array
    {
        return Schema::getColumnListing($this->getTable());
    }

    /**
     * Get defined filters
     *
     * @return array
     */
    public function getFilterableAttributes(): array
    {
        $filters = method_exists($this, 'filters') ? array_keys($this->filters()) : [];
 
        return array_unique(
            array_merge(
                array_diff($this->getColumns(), $this->getHidden()),
                $filters
            )
        );
    }

    /**
     * Get filterable parameters
     *
     * @param array $parameters
     * @return array
     */
    protected function getOnlyValidParameters($parameters): array
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
     * @param $name
     * @param $value
     * @return string
     */
    protected function getFilterOperator($name, $value): string
    {
        // Get operator from parameter value if exists
        $matches = [];
        preg_match('/' . implode('|', $this->operators) . '/', $value, $matches);

        if ($matches) {
            return $matches[0];
        }

        return $this->defaultOperator;
    }

    /**
     * Get filter value
     *
     * @param string $value
     * @param string $operator
     * @return string
     */
    protected function getFilterValue(string $value, string $operator): string
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
     * @return false|callable
     */
    protected function getFilterCallback(string $name)
    {
        // Get calback from model filters if there is one defined
        if (method_exists($this, 'filters')) {
            if (array_key_exists($name, $this->filters())) {
                return $this->filters()[$name];
            }
        }
        
        return false;
    }

    /**
     * Get filters
     *
     * @param array $parameters
     * @return array
     */
    protected function getFilters(array $parameters): array
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
     * Get order by callback
     *
     * @param string $name
     * @return false|callable
     */
    protected function getOrderByCallback($name)
    {
        // Get calback from model filters if there is one defined
        if (method_exists($this, 'orderBy')) {
            if (array_key_exists($name, $this->orderBy())) {
                return $this->orderBy()[$name];
            }
        }
        
        return false;
    }

    /**
     * Set model order
     *
     * @param EloquentBuilder $builder
     * @param string $order_by
     * @param string $order_dir
     * @return EloquentBuilder
     */
    protected function setOrder(EloquentBuilder $builder, string $order_by, string $order_dir): EloquentBuilder
    {
        // Get mapped attribute if model uses mappable trait
        if (method_exists($this, 'getMappededAttribute')) {
            $order_by = $this->getMappededAttribute($order_by);
        }

        $callback = $this->getOrderByCallback($order_by);

        if (is_callable($callback)) {
            return call_user_func($callback, $builder, $order_dir);
        }

        return $builder->orderBy($order_by, $order_dir);
    }

    /**
     * Set model filter
     *
     * @param EloquentBuilder $builder
     * @param array $filter
     * @return EloquentBuilder
     */
    protected function setFilter(EloquentBuilder $builder, array $filter): EloquentBuilder
    {
        if (is_callable($filter['callback'])) {
            return call_user_func($filter['callback'], $builder, $filter['value'], $filter['operator']);
        }

        return $builder->where($filter['column'], $filter['operator'], $filter['value']);
    }

    /**
     * Set model filters
     *
     * @return EloquentBuilder
     */
    public function setFilters(array $parameters): EloquentBuilder
    {
        $builder = $this->newModelQuery();

        foreach ($this->getFilters($parameters) as $filter) {
            $builder = $this->setFilter($builder, $filter);
        }

        // set order
        if (array_key_exists('order_by', $parameters)) {
            $builder = $this->setOrder($builder, $parameters['order_by'], $parameters['order_dir'] ?? 'asc');
        }

        // set limit
        if (array_key_exists('limit', $parameters)) {
            $builder = $builder->limit($parameters['limit']);
        }
        
        return $builder;
    }
}
