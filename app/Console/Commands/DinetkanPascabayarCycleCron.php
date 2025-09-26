<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Models\AdminDinetkanInvoice;
use App\Models\AdminInvoice;
use App\Models\MappingUserLicense;
use App\Models\UserDinetkan;
use App\Settings\LicenseDinetkanSettings;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Wablas;
use App\Models\PppoeMember;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\License;
use App\Models\WablasTemplate;
use App\Settings\LicenseSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Services\AdminDinetkanPaymentService;
use Modules\Payments\Services\AdminPaymentService;

class DinetkanPascabayarCycleCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dinetkan_invoice_create:check:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Admin License Prabayar and Send Notification';



    /**
     * Execute the console command.
     */
    public function handle(LicenseDinetkanRepositoryInterface $licenseDinetkanRepo, AdminDinetkanPaymentService $adminDinetkanPaymentService) {
        $users = UserDinetkan::where('role', 'Admin')->get();
        $now   = Carbon::now();

        foreach ($users as $user) {
            $userDue = Carbon::parse($user->dinetkan_next_due);

            // if user is not active, skip the process
//            if ($user->status !== UserStatusEnum::ACTIVE || !$user->license_id) {
//                continue;
//            }

            // if user is past due date, skip the process
            if ($now->gt($userDue)) {
                continue;
            }

            $settings = app(LicenseDinetkanSettings::class);

            $dayBeforeDue = $settings->day_before_due;
            $dayBeforeDue = $userDue->copy()->subDays($dayBeforeDue);
            if($now >= $dayBeforeDue && $now <= $userDue){
                // cari data invoice berdasarkan userdue yang sudah terbayar
                $invoice = AdminDinetkanInvoice::where('dinetkan_user_id', $user->dinetkan_user_id)->where('period', $user->dinetkan_next_due)->where('status', 1)->first();
                $invoiceUnpaid = AdminDinetkanInvoice::where('dinetkan_user_id', $user->dinetkan_user_id)->where('status','!=', 1)->first();
                if($invoice && !$invoiceUnpaid){
                    // recreate invoice
                    $license = $licenseDinetkanRepo->findById($invoice->itemable_id);
                    if($license){
//                        $user->next_due = $request->next_due;
                        $adminDinetkanPaymentService->createLicenseInvoiceMrc($license, $user, '');
                    }
                    Log::info('User ' . $user->username. ' before due '.$dayBeforeDue. ' Now '. $now. ' userDue '. $userDue . ' has on MRC cycle at ' . date('Y-m-d H:i:s'));
                }

                if(!$invoice && !$invoiceUnpaid){
                    // recreate invoice
                    $mappings = MappingUserLicense::where('dinetkan_user_id', $user->dinetkan_user_id)->get();
                    if($mappings){
                        foreach($mappings as $mapp){
                            if($mapp){
//                        $user->next_due = $request->next_due;
                                $license = $licenseDinetkanRepo->findById($mapp->license_id);
                                $adminDinetkanPaymentService->createLicenseInvoiceMrc($license, $user, '');
                            }
                            Log::info('User ' . $user->username. ' before due '.$dayBeforeDue. ' Now '. $now. ' userDue '. $userDue . ' has on MRC cycle at ' . date('Y-m-d H:i:s'));
                        }
                    }
                }

            }

            // if user is not due yet, skip the process
            if ($now->lt($dayBeforeDue)) {
                continue;
            }
        }
    }
}
