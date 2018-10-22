<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModelFilter
{
    /**
     * @var Model
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
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get query builder for model
     *
     * @param Model $model
     * @param array $filters
     * @return Builder
     */
    public static function for(Model $model, array $filters = [])
    {
        $instance = new static($model);

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
     * @param array $filters
     * @return array
     */
    protected function getOnlyValidFilters($filters): array
    {
        $filters = $this->model->mapper->mapRequestData($filters);

        return array_intersect_key(
            $filters,
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

        if ($matches && $matches[0]) {
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
     * @param array $filters
     * @return array
     */
    protected function getFilters(array $filters): array
    {
        $validFilters = [];

        foreach ($this->getOnlyValidFilters($filters) as $name => $value) {
            $array = is_array($value) ? $value : [$value];
            foreach ($array as $value) {
                $operator = $this->getFilterOperator($value);
                array_push(
                    $validFilters,
                    [
                        'column'   => $name,
                        'operator' => $this->getFilterOperator($value),
                        'value'    => $this->getFilterValue($value, $operator),
                        'callback' => $this->getFilterCallback($name)
                    ]
                );
            }
        }
        return $validFilters;
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
     * @param Builder $builder
     * @param string $order_by
     * @param string $order_dir
     * @return Builder
     */
    protected function setOrder(Builder $builder, string $order_by, string $order_dir): Builder
    {
        $order_by = current(
            array_keys($this->model->mapper->mapRequestData([$order_by => 0]))
        );

        $callback = $this->getOrderByCallback($order_by);

        if (is_callable($callback)) {
            return call_user_func($callback, $builder, $order_dir);
        }

        return $builder->orderBy($order_by, $order_dir);
    }

    /**
     * Set model filter
     *
     * @param Builder $builder
     * @param array $filter
     * @return Builder
     */
    protected function setFilter(Builder $builder, array $filter): Builder
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
     * @return Builder
     */
    public function getBuilder(array $filters): Builder
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
