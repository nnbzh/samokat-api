<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Session extends Model
{
    protected $guarded = ['id'];

    public function promocode(): HasOne
    {
        return $this->hasOne(PromocodeLog::class, 'session', 'session');
    }

    public function scopeValid(Builder $builder)
    {
        return $builder->where('is_invalidated', false);
    }

    public function isValid()
    {
        return ! $this->is_invalidated;
    }
}
