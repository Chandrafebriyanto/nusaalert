<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait ApiResponseTrait
{
    /**
     * Return either a Blade view or JSON based on content negotiation.
     *
     * Priority:
     * 1. Explicit ?format=json query parameter
     * 2. Accept: application/json header
     * 3. Request via XMLHttpRequest (AJAX)
     */
    protected function respondWithViewOrJson(Request $request, string $view, array $data, int $statusCode = 200)
    {
        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => $data,
            ], $statusCode);
        }

        return view($view, $data);
    }

    /**
     * Return JSON success response or redirect with flash message.
     */
    protected function respondWithSuccessOrRedirect(Request $request, string $route, string $message, array $data = [], int $statusCode = 200)
    {
        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        }

        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Return JSON success response or redirect back with flash message.
     */
    protected function respondWithSuccessOrBack(Request $request, string $message, array $data = [], int $statusCode = 200)
    {
        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Detect whether the client expects a JSON response.
     */
    protected function wantsJson(Request $request): bool
    {
        return $request->query('format') === 'json'
            || $request->wantsJson()
            || $request->ajax();
    }
}
