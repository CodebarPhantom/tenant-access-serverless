<?php

namespace App\Console\Commands;

use App\Models\Master\MasterConfigSla;
use App\Models\TenantAccess\TenantAccessSlaCalculate;
use Illuminate\Console\Command;
use Carbon\Carbon;
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

    protected $jamMasuk;
    protected $jamPulang;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $tenantAccessReports = DB::connection('mysql-karawang')->table('view_tenant_access_report_dashboard')->where('time_required_total',NULL)->get();
        DB::beginTransaction();
        try {

            $this->jamMasuk = MasterConfigSla::where('id','jam_masuk')->first()->value;
            $this->jamPulang = MasterConfigSla::where('id','jam_pulang')->first()->value;

            foreach ($tenantAccessReports as $tenantAccessReport) {
                $isTicketNoExists = TenantAccessSlaCalculate::where('ticket_no',$tenantAccessReport->ticket_no)->exists();

                if($isTicketNoExists){
                    $tenantAccessSLACalculate = TenantAccessSlaCalculate::where('ticket_no',$tenantAccessReport->ticket_no)->first();
                }else{
                    $tenantAccessSLACalculate = new TenantAccessSlaCalculate();
                }


                $tenantAccessSLACalculate->ticket_no = $tenantAccessReport->ticket_no;

                if ($tenantAccessReport->admin_dispatch_date != $tenantAccessSLACalculate->admin_dispatch_date) {
                    $tenantAccessSLACalculate->time_required_by_admin_to_dispatch =  $this->calculateFromWorkHours($tenantAccessReport->create_date,$tenantAccessReport->admin_dispatch_date);
                    $tenantAccessSLACalculate->admin_dispatch_date = $tenantAccessReport->admin_dispatch_date;
                }

                if ($tenantAccessReport->agent_progress_date != $tenantAccessSLACalculate->agent_progress_date) {
                    $tenantAccessSLACalculate->time_required_by_agent_to_response =  $this->calculateFromWorkHours($tenantAccessReport->admin_dispatch_date,$tenantAccessReport->agent_progress_date);
                    $tenantAccessSLACalculate->agent_progress_date = $tenantAccessReport->agent_progress_date;
                }

                if ($tenantAccessReport->agent_submit_date != $tenantAccessSLACalculate->agent_submit_date) {
                    $tenantAccessSLACalculate->time_required_by_agent_to_complete =  $this->calculateFromWorkHours($tenantAccessReport->agent_progress_date,$tenantAccessReport->agent_submit_date);
                    $tenantAccessSLACalculate->agent_submit_date = $tenantAccessReport->agent_submit_date;
                }

                if ($tenantAccessReport->close_date != $tenantAccessSLACalculate->close_date) {
                    $tenantAccessSLACalculate->time_required_by_admin_to_close =  $this->calculateFromWorkHours($tenantAccessReport->agent_submit_date,$tenantAccessReport->close_date);
                    $tenantAccessSLACalculate->close_date = $tenantAccessReport->close_date;
                }

                if($tenantAccessSLACalculate->admin_dispatch_date != NULL && $tenantAccessSLACalculate->agent_progress_date != NULL && $tenantAccessSLACalculate->agent_submit_date != NULL && $tenantAccessSLACalculate->close_date != NULL){
                   // dd($tenantAccessSLACalculate->admin_dispatch_date, $tenantAccessSLACalculate->agent_progress_date,  $tenantAccessSLACalculate->agent_submit_date,$tenantAccessSLACalculate->close_date);
                    $timeRequiredTotal = $this->timeToSeconds($tenantAccessSLACalculate->time_required_by_admin_to_dispatch) + $this->timeToSeconds($tenantAccessSLACalculate->time_required_by_agent_to_response) +
                    $this->timeToSeconds($tenantAccessSLACalculate->time_required_by_agent_to_complete) + $this->timeToSeconds($tenantAccessSLACalculate->time_required_by_admin_to_close);

                    $tenantAccessSLACalculate->time_required_total = floor($timeRequiredTotal / 3600) . gmdate(":i:s", $timeRequiredTotal % 3600);
                }

                $tenantAccessSLACalculate->save();

            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    private function calculateFromWorkHours($startTime,$endTime)
    {


        $startDateTime  = new Carbon($startTime);
        $endDateTime    = new Carbon($endTime);
        $slaTime = "";
        $slaTimeSecond = 0;
        $tempDate = "";

        if ($startTime == NULL || $endTime == NULL){
            return NULL;
        };


        if($startDateTime < $endDateTime){

            if($startDateTime->copy()->format("Y-m-d") == $endDateTime->copy()->format("Y-m-d")){

                if($startDateTime>=$startDateTime->copy()->setTime($this->jamPulang,0,0) && $endDateTime>=$endDateTime->copy()->setTime($this->jamPulang,0,0)){
                    $slaTimeSecond = 0;
                }elseif($startDateTime<=$startDateTime->copy()->setTime($this->jamMasuk,0,0) && $endDateTime<=$endDateTime->copy()->setTime($this->jamMasuk,0,0)){
                    $slaTimeSecond = 0;
                }elseif($startDateTime<=$startDateTime->copy()->setTime($this->jamMasuk,0,0) && $endDateTime>=$endDateTime->copy()->setTime($this->jamPulang,0,0)){
                    $slaTimeSecond = 32400; // 9 jam
                }else{
                    $slaTimeSecond = $this->calculateDiffSecond($startDateTime, $endDateTime);
                }


            }else{

                $tempDate = $startDateTime->copy();

                if($tempDate>$tempDate->copy()->setTime($this->jamPulang,0,0)){
                    $tempDate->addDay();
                }

                while ( $tempDate->format("Y-m-d") < $endDateTime->format("Y-m-d")) {

                    if ($tempDate->isWeekend()) {
                        $tempDate->addDay();
                    }else{
                        if($tempDate->format("Y-m-d") == $startDateTime->format("Y-m-d")){

                            $slaTimeSecond += $this->calculateDiffSecond($startDateTime, $startDateTime->copy()->setTime($this->jamPulang,00,00));

                        }else{
                            $slaTimeSecond += $this->calculateDiffSecond($tempDate->copy()->setTime($this->jamMasuk,00,00), $tempDate->copy()->setTime($this->jamPulang,00,00));
                        }
                        $tempDate->addDay();

                    }
                }
                $slaTimeSecond += $this->calculateDiffSecond($tempDate->copy()->setTime($this->jamMasuk,00,00), $endDateTime);
            }
            $slaTime = floor($slaTimeSecond / 3600) . gmdate(":i:s", $slaTimeSecond % 3600);

            return $slaTime;
        }else{
            return $slaTime = floor($slaTimeSecond / 3600) . gmdate(":i:s", $slaTimeSecond % 3600);; // tidak valid
        }

    }


    private function calculateDiffSecond($startDateTime,$endDateTime)
    {
        $slaTime = 0;
        $clockIn = $startDateTime->copy()->setTime($this->jamMasuk,00,00);
        $clockOut = $endDateTime->copy()->setTime($this->jamPulang,00,00);

        if($startDateTime >= $clockIn && $endDateTime <= $clockOut){

            $slaTime = $startDateTime->diffInSeconds($endDateTime);

        }elseif($startDateTime <= $clockIn && $endDateTime <= $clockOut){

            $slaTime = $clockIn->diffInSeconds($endDateTime);

        }elseif($startDateTime >= $clockIn && $endDateTime >= $clockOut){

            $slaTime = $startDateTime->diffInSeconds($clockOut);

        }else{
            $slaTime = $clockIn->diffInSeconds($clockOut);
        }

        return $slaTime;
    }

    function timeToSeconds($time)
    {
        $time == NULL ? "00:00:00" : $time;

        $arr = explode(':', $time);
        if (count($arr) === 3) {
            return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
        }
        return $arr[0] * 60 + $arr[1];
    }

        //startdatetime = 5 Oktober 17.01
        // enddatetime = 6 Oktober 08.01
        // jam masuk = 08.00
        // jam pulang = 17.00
        // sla = 0
        // tmpdatetime = startdatetime



        // if(startdatetime < enddatetime)
        // {
        //     if((hari) startdatetime == (hari) enddatetime)
        //     {
        //         sla = enddatetime - startdatetime
        //         //jangan lupa cek jam masuk jam pulang
        //     }
        //     else
        //     {
        //         while((hari) tmpdatetime < (hari) enddatetime)
        //         {
        //             if(isWeekend(tmpdatetime)
        //             {
        //                 tmpdatetime ++hari;
        //             }
        //             else
        //             {
        //                 if(tmpdatetime != startdatetime)
        //                 {
        //                     sla += jam pulang - jam masuk
        //                 }
        //                 else
        //                 {
        //                     sla += jam pulang - jam tmpdatetime
        //                 }
        //                 tmpdatetime ++hari;
        //             }
        //         }

        //         sla += enddatetime - (tmpdatetime + jam masuk)

        //         echo sla
        //     }
        // }
        // else
        // {
        //     echo "ga valid"
        // }

        // $startDateTime         = new Carbon('2022-10-05 17:00:01');
        // $endDateTime           = new Carbon('2022-10-06 08:00:01');
        // $thisFriday = new Carbon('this friday');
        // $weekendStartDateTime  = $thisFriday->setTime($this->jamPulang,00,00);

        // $nextMonday = $startDateTime->copy()->next(Carbon::MONDAY);
        // $weekendEndDateTime = $nextMonday->setTime($this->jamMasuk,00,00);
        // $spentTime = 0;
        // $preWeekendTime = 0;
        // $postWeekendTime = 0;

        // if($startDateTime>$weekendStartDateTime && $endDateTime<$weekendEndDateTime){
        //     // $startDateTime         = new Carbon('2022-10-07 17:00:01'); Jumat
        //     // $endDateTime           = new Carbon('2022-10-09 08:00:00'); Minggu

        // }elseif($startDateTime<$weekendStartDateTime && $endDateTime<$weekendEndDateTime){
        //     // $startDateTime         = new Carbon('2022-10-07 16:53:00'); Jumat
        //     // $endDateTime           = new Carbon('2022-10-09 08:00:00'); Minggu
        //     // Weekday with office hours

        //     if ($startDateTime->copy()->format("H") >= $this->jamMasuk  && $startDateTime->copy()->format("H") <= $this->jamPulang)
        //     {   // tarik dari DB master_config_sla
        //         //cari gmdate biar lebih dari 24 hour
        //         //$start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');
        //         $spentTime = gmdate('H:i:s',$ftdStartTime->diffInSeconds($ftdEndtime));
        //     }else{
        //         //dd($startDateTime->diffInSeconds($endDateTime));
        //         $result = gmdate('H:i:s',$endDateTime->diffInSeconds($endDateTime->format("Y-m-d 08:00:00")));
        //         dd($endDateTime->diffInSeconds($endDateTime->format("Y-m-d 08:00:00")));
        //     }

        //     $preWeekendTime  = $weekendStartDateTime->diffInSeconds($startDateTime);
        //     //$weekend_time = $total-$pre_weekend;
        // }elseif($startDateTime>$weekendStartDateTime && $endDateTime>$weekendEndDateTime){
        //     // $startDateTime         = new Carbon('2022-10-07 17:01:00'); Jumat
        //     // $endDateTime           = new Carbon('2022-10-10 08:01:00'); Senin
        //     //dd("test");
        //     $post_weekend = $endDateTime->diffInSeconds($weekendEndDateTime);
        //     $weekend_time = $total-$post_weekend;
        // }elseif($startDateTime<$weekendStartDateTime && $endDateTime>$weekendEndDateTime){

        //     // $startDateTime         = new Carbon('2022-10-07 19:53:20'); Jumat
        //     // $endDateTime           = new Carbon('2022-10-10 08:05:00'); Senin


        //     $weekend_time = $weekendEndDateTime->diffInSeconds($weekendStartDateTime);
        // }else{

        // }

        // $this->calculateDiffSecond($spentTime, $preWeekendTime,$postWeekendTime);
        // //floor($weekend_time / 3600).gmdate("/i", $weekend_time % 3600);
        // // /Carbon::setTestNow();
        // //dd();
        // dd($startDateTime,$weekendStartDateTime,$weekendEndDateTime,$total,$weekend_time,gmdate('H:i:s',$weekend_time));
        // dd(floor($weekend_time / 3600).gmdate("/i", $weekend_time % 3600));

        // $start  = new Carbon('2022-07-05 14:24:36');
        // $end    = new Carbon('2022-07-05 14:41:07');
        // dd("lolo",gmdate('H:i:s',$start->diffInSeconds($end)));




        // if ($ftdStartTime->copy()->format("H") >= $this->jamMasuk  && $ftdStartTime->copy()->format("H") <= $this->jamPulang)
        // { // tarik dari DB master_config_sla
        //     //cari gmdate biar lebih dari 24 hour
        //     //$start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');
        //     $result = gmdate('H:i:s',$ftdStartTime->diffInSeconds($ftdEndtime));
        // }else{

        //     $result = gmdate('H:i:s',$ftdEndtime->diffInSeconds($tempTime->format("Y-m-d 08:00:00")));
        // }

        // return $result;

}



