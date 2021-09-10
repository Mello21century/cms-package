<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/laravel-cms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://juzaweb.com/cms
 * @license    MIT
 */

namespace Juzaweb\Support;

use Illuminate\Support\Arr;

class GlobalData
{
    protected $values = [];

    public function set($key, $value)
    {
        Arr::set($this->values, $key, $value);
    }

    public function push($key, $value)
    {
        $this->values[$key][] = $value;
    }

    public function get($key, $default = [])
    {
        return Arr::get($this->values, $key, $default);
    }

    public function all()
    {
        return $this->values;
    }
}
