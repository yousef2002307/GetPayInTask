<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    protected const HEADER_NAME = 'Idempotency-Key';

    protected const EXPIRATION_SECONDS = 60 * 60 * 24; 

    protected const LOCK_TIMEOUT = 30; 

    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header(self::HEADER_NAME);

        if (! $idempotencyKey || ! Str::isUuid($idempotencyKey)) {
            Log::debug('Invalid or missing idempotency key', ['key' => $idempotencyKey]);

            return response()->json([
                'message' => 'Invalid or missing idempotency key',
                'idempotency_key' => $idempotencyKey,
                'timestamp' => now()->toDateTimeString(),
            ], 400);

        }

        // 2. Set the storage key
        $cacheKey = 'idempotency:'.$idempotencyKey;
        $lockKey = $cacheKey.':lock';

        Log::debug('Processing request', [
            'idempotency_key' => $idempotencyKey,
            'cache_key' => $cacheKey,
            'lock_key' => $lockKey,
        ]);

        $lock = Cache::lock($lockKey, self::LOCK_TIMEOUT);

        if (! $lock->get(1)) {
            Log::debug('Could not acquire lock', ['key' => $lockKey]);

            return response()->json([
                'message' => 'The request with this idempotency key is used',
                'idempotency_key' => $idempotencyKey,
                'timestamp' => now()->toDateTimeString(),
            ], 409);
        }
        if ($cachedResponse = Redis::get($cacheKey)) {
            Log::debug('Returning cached response', ['key' => $cacheKey]);
            $responseData = json_decode($cachedResponse, true);

            return response(
                $responseData['data'],
                $responseData['status'],
                $responseData['headers']
            );
        }
        Log::debug('Lock acquired', ['key' => $lockKey]);

        try {

            if ($cachedResponse = Redis::get($cacheKey)) {
                Log::debug('Found cached response after acquiring lock', ['key' => $cacheKey]);
                $responseData = json_decode($cachedResponse, true);

                return response(
                    $responseData['data'],
                    $responseData['status'],
                    $responseData['headers']
                );
            }

            // 6. Process the request
            $response = $next($request);

            if ($response->isSuccessful()) {
                $responseContent = [
                    'status' => $response->getStatusCode(),
                    'headers' => $response->headers->all(),
                    'data' => $response->getContent(),

                ];

                Log::debug('Caching successful response', ['key' => $cacheKey]);
                Redis::setex(
                    $cacheKey,
                    self::EXPIRATION_SECONDS,
                    json_encode($responseContent)
                );
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Error processing idempotent request', [
                'idempotency_key' => $idempotencyKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        } finally {

            $lock->release();
            Log::debug('Lock released', ['key' => $lockKey]);
        }
    }
}
