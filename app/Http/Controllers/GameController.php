<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponder;
use App\Helpers\RequestHelper;
use App\Http\Requests\GetPromoCodeRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\PromocodeLog;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    const GAME_INITIAL_STEP = 'G1.1';
    const GAME_FINAL_STEP = 'F';


    public function __construct(private SessionService $service)
    {
        $this->defineMiddlewares();
    }

    public function start(Request $request): JsonResponse
    {
        return ApiResponder::ok([
            'session' => $this->service->new(RequestHelper::getIp(), self::GAME_INITIAL_STEP)->session
        ]);
    }

    public function update(UpdateGameRequest $request)
    {
        $this->service->update($request->headers->get('x-api-session'), $request->step);

        return ApiResponder::ok();
    }

    public function status(Request $request, SessionService $service)
    {
        $model = $service->find($request->headers->get('x-api-session'));

        return ApiResponder::ok(['status' => $model->step ?? self::GAME_INITIAL_STEP]);
    }

    public function givePromoCode(GetPromoCodeRequest $request): JsonResponse
    {
        $model = $this->service->find($request->headers->get('x-api-session'));

        if ($model->step !== self::GAME_FINAL_STEP) {
            return ApiResponder::fail('Попытка перепрыгнуть игру.', 400);
        }

        if ($model->promocode()->exists()) {
            return ApiResponder::fail('Вы уже получали промокод', 400);
        }

        $log = PromocodeLog::query()->create([
            'session'   => $model->session,
            'promocode' => Str::upper(Str::random(8))
        ]);

        return ApiResponder::ok(['code' => $log->promocode]);
    }

    private function defineMiddlewares()
    {
        $this
            ->middleware('auth.session.custom')->only('update', 'status', 'givePromoCode');
        $this
            ->middleware('throttle.success:' . 'ip-promo/' . RequestHelper::getIp() . ',1,10')
            ->only('givePromoCode');
        $this
            ->middleware('throttle.success:' . 'session-promo/' . \request()->headers->get('x-api-session') . ',1,10')
            ->only('givePromoCode');
    }
}
