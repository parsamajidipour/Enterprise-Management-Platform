<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mobile\MobileProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileProfileController extends Controller
{
    use ApiResponses;

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'technicianProfile',
            'technicianLocations' => fn($query) => $query->latest()->limit(1),
        ]);

        return $this->success(new MobileProfileResource($user), 'Mobile profile retrieved');
    }
}
