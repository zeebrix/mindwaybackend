<?php

namespace App\Repositories;

abstract class BaseRepository {

    public $model;

    /**
     * @param $filters
     * @param $paginated
     * @return mixed
     */
    public function getAll($filters = [], $paginated = true) {
        return ($paginated ? $this->model->paginate(50) : $this->model->all());
    }

    public function getCount($filters = []) {
        return ($this->model->count());
    }

    /**
     * @param $value
     * @param string $column
     * @param array $filters
     * @param array $with
     * @param string $select
     * @param array $whereIn
     * @return mixed
     */
    public function getOne($value, $column = 'id', $filters = [], $with = [], $select = '*', $whereIn = []) {

        $model = $this->model;
        if (isset($value) && $value != "") {
            $model = $model->where($column, $value);
        }

        foreach ($filters as $col => $val) {
            $model = $model->where($col, $val);
        }

        // WhereIn filters
        foreach ($whereIn as $col => $val) {
            $model = $model->whereIn($col, $val);
        }

        // get specific
        $model = $model->with($with)
                ->select($select)
                ->first();
        return $model;
    }

    /**
     * @param array $info
     * @return mixed
     */
    public function store(array $info) {
        return $this->model->create($info);
    }

    /**
     * @param $params
     * @param $value
     * @param string $column
     * @param array $filters
     * @return mixed
     * @internal param $id
     */
    public function update($params, $value, $column = 'id', $filters = []) {
        $model = $this->model->where($column, $value);
        foreach ($filters as $col => $val) {
            $model = $model->where($col, $val);
        }
        return $model->update($params);
    }

    /**
     * @param $id
     * @return int
     */
    public function destroy($id) {
        return $this->model->destroy($id);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function delete(array $filters) {
        $model = $this->model;

        foreach ($filters as $col => $val) {
            $model = $model->where($col, $val);
        }
        return $model->delete();
    }

    public function getUniqueValue(
            $length = 10, $column = 'subscription_key', $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"
    ) {
        if ($length < 3) {
            return null;
        }
        $secret_key = "";
        for ($i = 0; $i < $length; $i++) {
            $secret_key .= substr($chars, rand(0, strlen($chars)), 1);
        }
        $checkKey = $this->model->where($column, $secret_key)->count();
        if ($checkKey > 0) {
            $this->getUniqueValue($length, $column);
        } else {
            return $secret_key;
        }
    }



    /**
     * @param array $info
     * @return mixed
     */
    public function seats(array $info) {
        return $this->model->create($info);
    }


     /**
     * @param array $info
     * @return mixed
     */
    public function bookings(array $info) {
        return $this->model->create($info);
    }

    /**
     * @param array $info
     * @return mixed
     */
    public function bookingDetails(array $info) {
        return $this->model->create($info);
    }

     /**
     * @param array $info
     * @return mixed
     */
    public function addCoupon(array $info) {
        return $this->model->create($info);
    }

    /**
     * @param array $info
     * @return mixed
     */
    public function applyCoupon(array $info) {
        return $this->model->create($info);
    }

}
