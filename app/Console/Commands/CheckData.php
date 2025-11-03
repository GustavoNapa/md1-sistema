<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check seeded data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Payment Platforms:');
        foreach(DB::table('payment_platforms')->get() as $p) {
            $this->line($p->id . ' - ' . $p->name);
        }

        $this->info("\nPayment Channels:");
        foreach(DB::table('payment_channels')->get() as $c) {
            $this->line($c->id . ' - ' . $c->name);
        }

        $this->info("\nVendors:");
        foreach(DB::table('vendors')->get() as $v) {
            $this->line($v->id . ' - ' . $v->name);
        }
    }
}
