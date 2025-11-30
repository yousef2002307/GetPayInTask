<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RedisService
{
    public function store($key, $value, $int = 4)
    {
        return Redis::set($key, json_encode($value), "EX", 60 * 60 * $int);
    }

    public function get($key)
    {
        return json_decode(Redis::get($key), true);
    }

    public function delete($key)
    {
        Redis::del($key);
    }

    public function exists($key)
    {
        return json_decode(Redis::exists($key), true);
    }
}