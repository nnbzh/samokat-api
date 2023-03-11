<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromocodeLog extends Model
{
    protected $guarded = ['id'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session', 'session');
    }
}
