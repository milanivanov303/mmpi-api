<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait Filterable
{
    protected $operators = [
        '>',
        '<',
        '>=',
        '<=',
        '!=',
        '<>',
        '<=>',
        'like'
    ];

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

    public function getFilterAttributes()
    {
        if ($this->filterable) {
            return $this->filterable;
        }

        return $this->getColumns();
    }

    /**
     * Get filterable parameters
     *
     * @param Request $request
     * @return array
     */
    protected function getFilterableAttributes(Request $request)
    {
        $data = $request->all();
            
        // Get mapped attributes if model uses mappable trait
        if (method_exists($this, 'getMappededAttributes')) {
            $data = $this->getMappededAttributes($data);
        }
 
        return array_intersect_key(
            $data,
            array_flip(
                $this->getFilterAttributes()
            )
        );
    }

    /**
     * Get filter operator
     *
     * @param $value
     * @return string
     */
    protected function getFilterOperator($value)
    {
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
     * @param $value
     * @return string
     */
    protected function getFilterValue($value)
    {
        return trim(
            str_replace($this->operators, '', $value)
        );
    }

    /**
     * Get filters
     *
     * @param Request $request
     * @return array
     */
    protected function getFilters(Request $request)
    {
        $filters = [];
        $params = $this->getFilterableAttributes($request);

        foreach ($params as $name => $value) {
            array_push(
                $filters,
                [
                    $name,
                    $this->getFilterOperator($value),
                    $this->getFilterValue($value)
                ]
            );
        }
        return $filters;
    }

    /**
     * Set model filters
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setFilters(Request $request)
    {
        $model = $this->where($this->getFilters($request));
        
        if ($request->input('order_by')) {
            $order_by = $request->input('order_by');

            // Get mapped attribute if model uses mappable trait
            if (method_exists($this, 'getMappededAttribute')) {
                $order_by = $this->getMappededAttribute($order_by);
            }

            $model = $model->orderBy($order_by, $request->input('order_dir', 'ASC'));
        }

        if ($request->input('limit')) {
            $model = $model->limit($request->input('limit'));
        }
        
        return $model;
    }
}
