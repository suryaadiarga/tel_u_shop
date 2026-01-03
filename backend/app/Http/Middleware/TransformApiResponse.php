<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransformApiResponse
{
    /**
     * Normalize JSON responses to the contract format.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!method_exists($response, 'getData')) {
            return $response;
        }

        $payload = $response->getData(true);
        if (!is_array($payload)) {
            return $response;
        }

        if (array_key_exists('success', $payload)) {
            return $response;
        }

        if (!array_key_exists('status', $payload)) {
            return $response;
        }

        $status = $payload['status'];
        $message = $payload['message'] ?? null;
        $errors = $payload['errors'] ?? ($payload['error'] ?? null);

        $removeKeys = ['status' => true, 'message' => true, 'error' => true, 'errors' => true];
        $dataPayload = array_diff_key($payload, $removeKeys);

        $data = null;
        if ($status === 'success') {
            if (array_key_exists('data', $payload) && count($dataPayload) === 1) {
                $data = $payload['data'];
            } else {
                $data = $dataPayload;
            }
        }

        $response->setData([
            'success' => $status === 'success',
            'message' => $message,
            'data' => $data,
            'errors' => $status === 'success' ? null : $errors,
        ]);

        return $response;
    }
}
