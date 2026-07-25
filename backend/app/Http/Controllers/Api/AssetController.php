<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Requests\Api\Asset\StoreAssetRequest;
use App\Http\Requests\Api\Asset\UpdateAssetRequest;
use App\Http\Resources\Api\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $assets = Asset::with('category')
            ->when($request->search, fn($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%"))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->category_id, fn($q, $id) => $q->where('asset_category_id', $id))
            ->paginate($request->integer('per_page', 15));

        return $this->success(AssetResource::collection($assets)->response()->getData(true), 'Assets retrieved');
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = Asset::create($request->validated());
        $asset->load('category');

        return $this->created(new AssetResource($asset), 'Asset created');
    }

    public function show(Asset $asset): JsonResponse
    {
        $asset->load('category');

        return $this->success(new AssetResource($asset), 'Asset retrieved');
    }

    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $asset->update($request->validated());
        $asset->load('category');

        return $this->success(new AssetResource($asset), 'Asset updated');
    }

    public function destroy(Asset $asset): JsonResponse
    {
        $asset->delete();

        return $this->success(null, 'Asset deleted');
    }

    public function findByBarcode(string $barcode): JsonResponse
    {
        $asset = Asset::with('category')->where('code', $barcode)->first();

        if (!$asset) {
            return $this->notFound("Asset with barcode '{$barcode}' not found");
        }

        return $this->success(new AssetResource($asset), 'Asset found');
    }
}
