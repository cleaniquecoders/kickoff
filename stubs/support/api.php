<?php

declare(strict_types=1);

use CleaniqueCoders\Traitify\Contracts\Api;
use Illuminate\Http\JsonResponse;

if (! function_exists('api_exception')) {
    function api_exception(Throwable $th): JsonResponse
    {
        $code = (is_int($th->getCode()) && $th->getCode() >= 100 && $th->getCode() <= 599)
            ? $th->getCode()
            : 500;

        $data = [
            'message' => $th->getMessage(),
            'code' => $code,
        ];
        if (config('app.debug')) {
            $data['trace'] = $th->getTrace();
        }

        return response()->json($data, $code);
    }
}

if (! function_exists('api_response')) {
    function api_response(Api $api): JsonResponse
    {
        return response()->json(
            $api->getApiResponse(request()),
            $api->getCode()
        );
    }
}

if (! function_exists('api_accept_header')) {
    function api_accept_header(): string
    {
        $config = config('api');

        return 'application/'
            .$config['standardsTree'].'.'
            .$config['subtype'].'.'
            .$config['version'].'+json';
    }
}
