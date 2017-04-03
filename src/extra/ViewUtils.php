<?php

namespace Leantony\Database\Extra;

trait ViewUtils
{
    /**
     * Make an array that can be used as a drop-down list of values in html
     * @param array $params
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function dropDownList($params = [])
    {
        // get params along with defaults
        $condition = array_pull($params, 'condition');
        $key = array_pull($params, 'key', 'id');
        $value = array_pull($params, 'value', 'name');
        $order = array_pull($params, 'order', []);

        if (empty($condition)) {
            $data = $this->all([$key, $value], $order)->pluck($value, $key);
        } else {
            if ($condition instanceof \Closure) {
                $data = $condition($this);
            } else {
                $data = $this->where($condition)->all([$key, $value], $order)->pluck($value, $key);
            }
        }

        return $data;
    }

    /**
     * Get the name of the underlying model class
     *
     * @param bool $raw
     * @return string
     */
    public function className($raw = false)
    {
        return $raw ? get_class($this->model) : class_basename($this->model);
    }
}