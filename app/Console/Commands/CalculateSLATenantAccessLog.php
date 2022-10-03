<?php

namespace App\Console\Commands;

use App\Models\TenantAccess\TenantAccessSlaCalculate;
use Illuminate\Console\Command;
use DB;

class CalculateSLATenantAccessLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant-access:calculate-sla-tenant-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate SLA Tenant Access for Report feat Alvin Susanto';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenantAccessReports = DB::connection('mysql-karawang')->table('view_tenant_access_report_dashboard')->first();
        DB::beginTransaction();
        try {

            //foreach ($tenantAccessReports as $tenantAccessReport) {
                $tenantAccessSLACalculate = new TenantAccessSlaCalculate();
                $tenantAccessSLACalculate->ticket_no = $tenantAccessReports->ticket_no;
                $tenantAccessSLACalculate->time_required_by_admin_to_dispatch = "01:00:00";
                $tenantAccessSLACalculate->save();
            //}
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
        }
    }
}
