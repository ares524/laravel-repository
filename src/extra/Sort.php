<?php

namespace Leantony\Database\Extra;

trait Sort
{
    /**
     * Default order mode
     *
     * @var array
     */
    protected $defaultOrder = ['column' => 'id', 'order' => 'asc'];

    /**
     * Extract sort params
     *
     * @param $order
     * @return array
     */
    protected function extractSortValues($order)
    {
        if (empty($order)) {
            $order = $this->defaultOrder;
        }
        $orderColumn = array_pull($order, 'column');
        $orderType = array_pull($order, 'order');
        return [$orderColumn, $orderType];
    }
}