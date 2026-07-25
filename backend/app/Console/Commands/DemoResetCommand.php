<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WorkOrder;
use App\Services\Cmms\CmmsImportService;
use App\Services\DispatchAssignmentService;
use Illuminate\Console\Command;
use Throwable;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset {--assign : Create one sample dispatch assignment after importing CMMS work orders}';

    protected $description = 'Reset demo data, import fake CMMS work orders, and print demo access details.';

    public function handle(CmmsImportService $cmmsImport, DispatchAssignmentService $assignments): int
    {
        $this->info('Resetting database and seed data...');
        $this->call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);

        $this->info('Importing fake CMMS work orders...');
        $summary = $cmmsImport->importOpenWorkOrders();

        $this->line(sprintf(
            'CMMS import: fetched=%d, created=%d, updated=%d',
            $summary['fetched'] ?? 0,
            $summary['created'] ?? 0,
            $summary['updated'] ?? 0
        ));

        if ($this->option('assign')) {
            $this->createSampleAssignment($assignments);
        }

        $this->newLine();
        $this->info('Demo credentials');
        $this->line('Admin: admin@example.com / password');
        $this->line('Technicians: tech.north@example.com, tech.central@example.com, tech.south@example.com / password');

        $this->newLine();
        $this->info('Demo URLs');
        $this->line('Frontend: http://localhost:3000');
        $this->line('Backend API: http://localhost:8010/api');
        $this->line('Adminer: http://localhost:8090');

        return self::SUCCESS;
    }

    private function createSampleAssignment(DispatchAssignmentService $assignments): void
    {
        try {
            $workOrder = WorkOrder::where('source', 'cmms')
                ->where('status', 'pending_dispatch')
                ->orderBy('id')
                ->first();

            $technician = User::role('technician')
                ->whereHas('technicianProfile', fn($query) => $query->where('is_active', true))
                ->orderBy('id')
                ->first();

            $admin = User::role('admin')->first();

            if (!$workOrder || !$technician) {
                $this->warn('Sample assignment skipped: missing imported work order or active technician.');
                return;
            }

            $assignment = $assignments->assign($workOrder, $technician, $admin, 'Demo reset sample assignment');
            $this->line("Sample assignment created: #{$assignment->id}");
        } catch (Throwable $error) {
            $this->warn('Sample assignment skipped: '.$error->getMessage());
        }
    }
}
