<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponses;
use App\Http\Requests\Api\InspectionForm\StoreInspectionFormRequest;
use App\Http\Requests\Api\InspectionForm\UpdateInspectionFormRequest;
use App\Http\Resources\Api\InspectionFormResource;
use App\Models\InspectionForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InspectionFormController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $forms = InspectionForm::with(['assetCategory', 'fields'])
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->boolean('active_only'), fn($q) => $q->where('is_active', true))
            ->paginate($request->integer('per_page', 15));

        return $this->success(InspectionFormResource::collection($forms)->response()->getData(true), 'Inspection forms retrieved');
    }

    public function store(StoreInspectionFormRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $fields = $validated['fields'] ?? [];
        unset($validated['fields']);

        $form = InspectionForm::create($validated);

        foreach ($fields as $index => $fieldData) {
            $fieldData['order'] = $fieldData['order'] ?? $index;
            $form->fields()->create($fieldData);
        }

        $form->load(['assetCategory', 'fields']);

        return $this->created(new InspectionFormResource($form), 'Inspection form created');
    }

    public function show(InspectionForm $inspectionForm): JsonResponse
    {
        $inspectionForm->load(['assetCategory', 'fields']);

        return $this->success(new InspectionFormResource($inspectionForm), 'Inspection form retrieved');
    }

    public function update(UpdateInspectionFormRequest $request, InspectionForm $inspectionForm): JsonResponse
    {
        $inspectionForm->update($request->validated());
        $inspectionForm->load(['assetCategory', 'fields']);

        return $this->success(new InspectionFormResource($inspectionForm), 'Inspection form updated');
    }

    public function destroy(InspectionForm $inspectionForm): JsonResponse
    {
        $inspectionForm->delete();

        return $this->success(null, 'Inspection form deleted');
    }
}
