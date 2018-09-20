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
        return Schema::getColumnListing($this->model->getTable());
    }

    /**
     * Fet filterable params
     *
     * @param Request $request
     * @return array
     */
    protected function getFilterableParams(Request $request)
    {
        return array_intersect_key(
            $request->all(),
            array_flip(
                $this->getColumns()
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
        $params = $this->getFilterableParams($request);

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
}
