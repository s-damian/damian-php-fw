<?php

namespace DamianPhp\Support\String;

use DamianPhp\Support\Facades\Str;
use DamianPhp\Support\Facades\Input;

/**
 * To manage order by. Useful for SQL queries and for views (table thead and table tfoot).
 */
class OrderBy
{
    private array $columns = [];

    private array $sqlOrderbyToColumns = [];

    private array $columnsDefaultDesc = [];

    private string $queryParams = '';

    /**
     * The default column must be put in the 1st position.
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * To possibly specify for an order by in the URL, ORDER BY to be done in SQL.
     * This function is especially useful for SQL queries with joins.
     *
     * @param array $sqlOrderbyToColumns -
     * - In keys: Value that we give to the orderby of the href.
     * - In values: 'table.column'.
     * - Example: 'customer_first_name' => Database::CUSTOMERS.'.first_name'.
     */
    public function assignSqlOrderbyToColumns(array $sqlOrderbyToColumns): void
    {
        $this->sqlOrderbyToColumns = $sqlOrderbyToColumns;
    }

    /**
     * To possibly specify the columns which are by default at order desc during the 1st click on its link.
     */
    public function setColumnsDefaultDesc(array $columnsDefaultDesc): void
    {
        $this->columnsDefaultDesc = $columnsDefaultDesc;
    }

    public function addQueryParams(array $queryParams): void
    {
        $this->queryParams = Str::andIfHasQueryString($queryParams);
    }

    /**
     * @param array $options
     * - $options['default_order'] - To possibly change the default order.
     */
    public function get(array $options = []): array
    {
        $defaultOrder = isset($options['default_order']) ? $options['default_order'] : 'desc';

        // For ORDER BY in URL: the 1st column is set by default, but will be changed if orderby in URL.
        $orderBy = $this->columns[0];
        // For ORDER BY in SQL: the 1st column is set by default, but will be changed if specified in "assignSqlOrderbyToColumns()" and/or orderby in URL.
        $orderByInSql = $orderBy;
        // Will be 'asc' or 'desc' for SQL: it is set to default order, but will be changed if order in URL.
        $order = strtolower($defaultOrder);

        // Will be returned in the view (in the thead and tfoot of the array).
        $attrs = [];

        if (
            Input::hasGet('orderby') &&
            Input::hasGet('order') &&
            in_array(Input::get('orderby'), $this->columns) &&
            in_array(Input::get('order'), ['asc', 'desc'])
        ) {
            $orderBy = Input::get('orderby');
            $order = Input::get('order');
        }
        
        // To specify the table in the SQL (if requested with "assignSqlOrderbyToColumns()").
        if (array_key_exists($orderBy, $this->sqlOrderbyToColumns)) {
            $orderByInSql = $this->sqlOrderbyToColumns[$orderBy];
        } else {
            $orderByInSql = $orderBy;
        }

        // For each column allowed to be in order by, assign it its CSS class and its clickable href.
        foreach ($this->columns as $column) {
            if ($column === $orderBy) { // If active column in GET.
                if ($order === 'asc') {
                    $attrs[$column]['href'] = 'orderby='.$column.'&amp;order=desc'.$this->queryParams;
                    $attrs[$column]['class'] = 'orderby-icon-1';
                } else {
                    $attrs[$column]['href'] = 'orderby='.$column.'&amp;order=asc'.$this->queryParams;
                    $attrs[$column]['class'] = 'orderby-icon-2';
                }
            } else {
                if (in_array($column, $this->columnsDefaultDesc)) {
                    $attrs[$column]['href'] = 'orderby='.$column.'&amp;order=desc'.$this->queryParams;
                    $attrs[$column]['class'] = 'orderby-icon-n-2';
                } else {
                    $attrs[$column]['href'] = 'orderby='.$column.'&amp;order=asc'.$this->queryParams;
                    $attrs[$column]['class'] = 'orderby-icon-n-1';
                }
            }
        }

        return [
            'orderBy' => $orderByInSql,
            'order' => $order,
            'attrs' => $attrs,
        ];
    }
}
