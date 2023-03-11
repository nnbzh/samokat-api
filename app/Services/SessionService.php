<?php

namespace App\Services;

use App\Helpers\ApiResponder;
use App\Models\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SessionService
{
    const MINUTES_TO_START_NEW = 5;

    public function new(string $ip, string $step): Model|Builder|Session
    {
        $latest = Session::query()->where('ip', $ip)->valid()->latest()->first();
        if ($latest && $latest->created_at->diffInMinutes() < self::MINUTES_TO_START_NEW) {
            return ApiResponder::fail('Попробуйте позже.')->throwResponse();
        } else {
            Session::query()->where('ip', $ip)->valid()->update(['is_invalidated' => true]);
        }

        $salt = Str::random(8);
        $data = $ip . $salt;

        $session = hash_hmac('sha256', $data, $salt);

        return Session::query()->create([
            'session' => $session,
            'ip'      => $ip,
            'step'    => $step,
            'salt'    => $salt
        ]);
    }

    public function update(string $session, string $step): Model|Builder|Session
    {
        $model       = Session::query()->where('session', $session)->firstOrFail();
        $model->step = $step;
        $model->saveOrFail();

        return $model;
    }

    public function find(string $session): Model|Builder|Session|null
    {
        return Session::query()->where('session', $session)->first();
    }
}
