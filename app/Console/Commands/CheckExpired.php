<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Expired;
use App\Models\Good;
use App\Models\StockTransaction;
use Auth;

class CheckExpired extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */

      protected $signature = 'expired:cron';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For Check Expired Goods. if expired Now Status go to Expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
            $data = DB::table('stock_entries')->whereDate('date_expired', '=', Carbon::now())->get();
      
            foreach ($data as $item) {
                $amount = Good::find($item->good_id);
                $amount_ttl = StockTransaction::where('location_id', $item->location_id)->where('good_id', $item->good_id)->orderBy('created_at', 'desc')->first();
              
                $expired = New Expired;
                $expired->good_id = $item->good_id; 
                $expired->entry_id = $item->id;
                $expired->location_id = $item->location_id;
                $expired->amount = $amount_ttl->end_balance;
                $expired->save();

                $stocktransaction = New stocktransaction;
                $stocktransaction->start_balance = $amount->getBalanceByWarehouse($amount_ttl->location_id);
                $stocktransaction->amount = $amount_ttl->end_balance;
                $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
                $stocktransaction->type = StockTransaction::TYPE_OUT;
                $stocktransaction->good_id = $item->good_id;
                $stocktransaction->user_id = 1;
                $stocktransaction->location_id = $item->location_id;
                $expired->stock_transaction()->save($stocktransaction);
            }
           

  

    }
}
