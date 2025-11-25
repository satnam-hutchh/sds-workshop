<?php

namespace Sds\Workshop\Models;

abstract class BaseModel
{
    public function __construct(public array $attributes = [])
    {
        foreach ($attributes as $key => $val) {
            $this->{$key} = $val;
        }
    }
}
