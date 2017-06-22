<?php

namespace Mfissehaye\LaravelEasyRestAPI;

class LarapiError {
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function apiDatabaseError() {
        return response()->json([
            'errors' => [
                'database' => 'Could not complete request'
            ]
        ], 500);
    }
}