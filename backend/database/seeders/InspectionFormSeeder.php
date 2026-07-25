<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\InspectionForm;
use Illuminate\Database\Seeder;

class InspectionFormSeeder extends Seeder
{
    public function run(): void
    {
        $electrical = AssetCategory::where('name', 'Electrical Equipment')->first();

        $form = InspectionForm::firstOrCreate(
            ['name' => 'Electrical Panel Inspection'],
            [
                'description'       => 'Standard inspection checklist for electrical distribution panels',
                'asset_category_id' => $electrical?->id,
                'is_active'         => true,
            ]
        );

        if ($form->wasRecentlyCreated || $form->fields()->count() === 0) {
            $fields = [
                ['label' => 'Panel door condition', 'field_type' => 'select', 'options' => ['Good', 'Fair', 'Poor'], 'is_required' => true, 'order' => 1],
                ['label' => 'Visible damage or burn marks', 'field_type' => 'boolean', 'is_required' => true, 'order' => 2],
                ['label' => 'Breaker labels legible', 'field_type' => 'boolean', 'is_required' => true, 'order' => 3],
                ['label' => 'All breakers functional', 'field_type' => 'boolean', 'is_required' => true, 'order' => 4],
                ['label' => 'Temperature reading (°C)', 'field_type' => 'number', 'is_required' => false, 'order' => 5],
                ['label' => 'Inspector notes', 'field_type' => 'textarea', 'is_required' => false, 'order' => 6],
                ['label' => 'Evidence photo', 'field_type' => 'photo', 'is_required' => false, 'order' => 7],
            ];

            foreach ($fields as $fieldData) {
                $form->fields()->create($fieldData);
            }
        }

        $mechanical = AssetCategory::where('name', 'Mechanical Equipment')->first();

        $form2 = InspectionForm::firstOrCreate(
            ['name' => 'Pump Condition Monitoring'],
            [
                'description'       => 'Monthly pump inspection and condition monitoring form',
                'asset_category_id' => $mechanical?->id,
                'is_active'         => true,
            ]
        );

        if ($form2->wasRecentlyCreated || $form2->fields()->count() === 0) {
            $fields2 = [
                ['label' => 'Vibration level', 'field_type' => 'select', 'options' => ['Normal', 'Elevated', 'High'], 'is_required' => true, 'order' => 1],
                ['label' => 'Unusual noise', 'field_type' => 'boolean', 'is_required' => true, 'order' => 2],
                ['label' => 'Seal leakage observed', 'field_type' => 'boolean', 'is_required' => true, 'order' => 3],
                ['label' => 'Bearing temperature (°C)', 'field_type' => 'number', 'is_required' => false, 'order' => 4],
                ['label' => 'Overall condition', 'field_type' => 'select', 'options' => ['Good', 'Acceptable', 'Needs Attention', 'Critical'], 'is_required' => true, 'order' => 5],
                ['label' => 'Remarks', 'field_type' => 'textarea', 'is_required' => false, 'order' => 6],
            ];

            foreach ($fields2 as $fieldData) {
                $form2->fields()->create($fieldData);
            }
        }
    }
}
