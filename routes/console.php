<?php
    use Illuminate\Foundation\Inspiring;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Schedule;
    // Schedule::command('invoice:billing-cycle')->monthlyOn(1, '04:00')->withoutOverlapping();
    // Schedule::command('invoice:fixed-date')->dailyAt('06:00')->withoutOverlapping();
    // Schedule::command('invoice:renewable')->dailyAt('06:00')->withoutOverlapping();
    // Schedule::command('invoice:reminder-notification')->dailyAt('07:00')->withoutOverlapping();
    // Schedule::command('pppoe:suspend')->dailyAt('07:05')->withoutOverlapping(); // harus duluan ini
    // // Schedule::command('pppoe:suspend-overdue')->dailyAt('12:30')->withoutOverlapping(); // gak usah
    // Schedule::command('user:suspend')->dailyAt('09:00')->withoutOverlapping();
    // Schedule::command('hotspot:expired')->everyFiveMinutes();
    // Schedule::command('dinetkan_invoice_service:check:cron')->dailyAt('04:00')->withoutOverlapping();

    Schedule::command('dinetkan_suspend_service:check:cron')->dailyAt('04:30')->withoutOverlapping();
    // Schedule::command('get_mrtg_cycle:cron')->everyFiveMinutes();
    // Schedule::command('billing_service:cron')->monthlyOn(1, '04:00')->withoutOverlapping();

    // Schedule::command('reload_whatsapp:cron')->everyTenMinutes();
    // Schedule::command('pppoeinvoice:reminder')->dailyAt('09:00')->withoutOverlapping();
    // Schedule::command('dinetkaninvoice:reminder')->dailyAt('09:30')->withoutOverlapping();

