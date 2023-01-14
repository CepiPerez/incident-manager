<?php

class GlobalScope implements Scope
{
    protected $value;

    public function __construct($value=100)
    {
        $this->value = $value;
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('status', '<', $this->value);
    }
}


