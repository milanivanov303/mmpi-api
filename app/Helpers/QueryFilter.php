<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class QueryFilter
{
    /**
     * @var EloquentModel
     */
    protected $model;

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
     * DataFilter constructor
     *
     * @param EloquentModel $model
     */
    public function __construct(EloquentModel $model)
    {
        $this->model = $model;
    }

    /**
     * Get builder query for model
     *
     * @param EloquentModel $model
     * @param array $filters
     * @return EloquentBuilder
     */
    public static function for(EloquentModel $model, array $filters = [])
    {
        $instance = new self($model);

        $builder = $instance->getBuilder($filters);

        return $builder;
    }

    /**
     * Get model table columns
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return Schema::getColumnListing($this->model->getTable());
    }

    /**
     * Get defined filters
     *
     * @return array
     */
    public function getFilterableAttributes(): array
    {
        $filters = method_exists($this->model, 'filters') ? array_keys($this->model->filters()) : [];
 
        return array_unique(
            array_merge(
                array_diff($this->getColumns(), $this->model->getHidden()),
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
        $parameters = $this->model->mapper->mapRequestData($parameters);

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
    protected function getFilterOperator($value): string
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
        // Get callback from model filters if there is one defined
        if (method_exists($this->model, 'filters')) {
            if (array_key_exists($name, $this->model->filters())) {
                return $this->model->filters()[$name];
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
                $operator = $this->getFilterOperator($value);
                array_push(
                    $filters,
                    [
                        'column'   => $name,
                        'operator' => $this->getFilterOperator($value),
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
        // Get callback from model filters if there is one defined
        if (method_exists($this->model, 'orderBy')) {
            if (array_key_exists($name, $this->model->orderBy())) {
                return $this->model->orderBy()[$name];
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
        $order_by = $this->model->mapper->getMappedAttribute($order_by);

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
     * Get query builder with applied filters
     *
     * @param array $filters
     * @return EloquentBuilder
     */
    protected function getBuilder(array $filters): EloquentBuilder
    {
        $builder = $this->model->newModelQuery();

        foreach ($this->getFilters($filters) as $filter) {
            $builder= $this->setFilter($builder, $filter);
        }

        // set order
        if (array_key_exists('order_by', $filters)) {
            $builder = $this->setOrder($builder, $filters['order_by'], $filters['order_dir'] ?? 'asc');
        }

        // set limit
        if (array_key_exists('limit', $filters)) {
            $builder = $builder->limit($filters['limit']);
        }

        return $builder;

    }
}
