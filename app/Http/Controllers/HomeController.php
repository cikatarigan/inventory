<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockEntry;
use App\Models\Good;
use App\Models\Location;
use App\Models\LocationShelf;
use App\User;
use App\Models\Sample;
use App\Models\Allotment;
use App\Models\AllotmentItem;
use App\Models\Expired;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockTransaction;
use App\Models\GoodShelf;
use App\Models\Borrow;
use App\Models\BorrowItem;
use App\Models\GiveBack;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
         $expired = StockEntry::where('status', '!=','Out Of Stock')->with(['good.stock_transaction', 'location_shelf.location'])->whereHas('good',  function($query){
            $query->whereNotNull('isexpired');
         })->whereBetween('date_expired', [ Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(30)->format('Y-m-d') ])->where('status', '!=' ,'expired')->get();
          // $expired = Good::with('stockentry.location_shelf.location')->whereHas('stockentry', function($query){
          //   $query->whereBetween('date_expired', [ Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(30)->format('Y-m-d') ])->where('status', '!=' ,'expired');       })->whereNotNull('isexpired')->get();


         $borrow = Borrow::with('good.good_images','user')->where('status', 'Still Borrow')->get();
         $allotment = Allotment::where('user_id', Auth::id())->get();
         $borrow_user = Borrow::with('good.good_images')->where('status', 'Still Borrow')->where('user_id', Auth::id())->get();
         $goods = Good::count();
         $users = User::count();
         $sample = Sample::count();

        return view('home.index',['expired' => $expired, 'goods'=> $goods, 'users' => $users ,'sample' => $sample , 'borrow' => $borrow, 'allotment' => $allotment, 'borrow_user' => $borrow_user]);
    }

    public function locations(Request $request)
    {
        $locations = Location::where('name', 'LIKE', "%$request->term%")
            ->select(['id', 'name as text'])->get();

        return response()->json([
            'results'  => $locations
        ]);
    }


    public function shelf(Request $request, Location $location)
    {

        $shelf = DB::table('location_shelves')->join('locations','location_shelves.location_id', '=', 'locations.id')->where('locations.id', $location->id)->select('location_shelves.id as id','name_shelf as text')->get();

        return response()->json([
            'results'  => $shelf
        ]);
    }

    public function goods(Request $request, LocationShelf $shelf)
    {
        $goods = Good::with(['good_shelves.location_shelves',])->whereHas('good_shelves.location_shelves', function($query) use ($shelf){
            $query->where('id', $shelf->id);
        })->select([ '*','goods.name as text'])->get();

        return response()->json([
            'results'  => $goods
        ]);
    }


    public function users(Request $request)
    {
      $user = User::whereHas('borrow', function($q){
           $q->where('status', 'Still Borrow')->groupBy('user_id');
      })->select(['id', 'name as text'])->get();

        return response()->json([
            'results'  => $user
        ]);
    }

    public function borrows(Request $request, User $user)
    {
        $borrow = DB::table('goods')->join('borrows', 'goods.id', '=' , 'borrows.good_id')
        ->where('borrows.user_id' , $user->id)
        ->select([ 'good_id as id','goods.name as text'])->get();

        return response()->json([
            'results'  => $borrow
        ]);
    }

    public function borrows_id(Request $request, User $user, Good $good)
    {

        $borrow_id = Borrow::where('good_id', $good->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'Still Borrow')->select(['id', 'id as text'])->get();

        return response()->json([
            'results'  => $borrow_id
        ]);
    }

    public function scan(Request $request)
    {
            return view('scan.index');
    }

    public function check_result(Request $request)
    {
         if($request->isMethod('POST')){
            $goods = Good::find($request->good_id);
            $entry_id = Stockentry::find($request->entry_id);
            $borrow_id = Borrow::where('good_id',$request->goods_id)
                                ->where('user_id', $request->user_return)
                                ->where('location_shelf_id', $request->location_shelf_id)
                                ->where('status', 'Still Borrow')->first();


            $validator = $request->validate([
                'amount' => 'required',
           ]);

           if($request->log == 3){
                $this->validate($request, [
                    'amount' => ['required', 'numeric','max:' . ($borrow_id->amount - $borrow_id->stock_back),'min:1'],
                ]);
            }else{
                $this->validate($request, [
                    'amount' => ['required', 'numeric','max:' . ($entry_id->amount - $entry_id->stock_use),'min:1'],
                ]);
            }

             return response()->json([
                'success'=>true,
            ]);
        }
    }

    public function result (Request $request)
    {

        $search = $request->q;
        $data = StockEntry::with(['good.good_shelves.location_shelves', 'borrow_item.borrow.user'])->where('qrcode', $search)->first();

        if($data != null){
            $amount = $data->amount -  $data->stock_use;
            $users = User::where('id', '!=', auth()->id())->get();

            $borrow = Borrow::where('good_id', $data->good_id)->where('location_shelf_id', $data->location_shelf_id)->where('status','Still Borrow')->get();

            $user_borrow = User::with('borrow')->whereHas('borrow', function($q)use ($data){
                $q->where('good_id', $data->good_id)->orWhere('location_shelf_id', $data->location_shelf_id)->where('status','Still Borrow');
            })->distinct()->get();

        } else{
            return response()->view('errors.404');
        }

        return view('scan.result', ['data' => $data, 'amount' => $amount ,'users' => $users, 'borrow' => $borrow,  'user_borrow' => $user_borrow]);
    }


    public function action(Request $request)
    {
        if ($request->isMethod('post')){
            $user = User::find($request->data_user);
            $user_return = User::find($request->data_user_return);

            if($request->data_log == 3){
                    if(!Hash::check($request->password, $user_return->password)){
                        return response()->json([
                        'success'=>false,
                        'message'   => 'Password User Salah'
                    ]);
                }
           }else{
                    if(!Hash::check($request->password, $user->password)){
                        return response()->json([
                        'success'=>false,
                        'message'   => 'Password User Salah'
                    ]);
                }
           }

           try{
            DB::statement('SET autocommit=0');
            DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, expireds WRITE, good_shelves WRITE, stock_transactions WRITE, goods WRITE, borrows WRITE, give_backs WRITE, allotments WRITE, allotment_items WRITE, borrow_items WRITE');

                if($request->data_log == 1){
                    $data = New Borrow;
                    $data->description = $request->data_description;
                    $data->status = Borrow::STILL_BORROW;
                    $data->good_id = $request->data_goods;
                    $data->user_id = $request->data_user;
                    $data->handle_by = Auth::id();
                    $data->amount = $request->data_amount;
                    $data->stock_back = 0;
                    $data->location_shelf_id = $request->data_location_shelf_id;
                    $data->save();

                    $itemborrow = New BorrowItem;
                    $itemborrow->entry_id =  $request->data_entryid;
                    $itemborrow->borrow_id = $data->id;
                    $itemborrow->amount= $data->amount;
                    $itemborrow->save();

                    $stockentry = Stockentry::find($request->data_entryid);
                    $amount = $stockentry->amount - $stockentry->stock_use;
                    $stockentry->stock_use = $stockentry->stock_use  + $data->amount;
                    if($amount <= $request->data_amount ){
                        $stockentry->status = Stockentry::TYPE_OUT_STOCK;

                    }
                    $stockentry->save();

                    $goods = $data->good;

                    $stocktransaction = New stocktransaction;
                    $stocktransaction->start_balance = $goods->getBalanceByShelf($request->data_location_shelf_id);
                    $stocktransaction->amount = $request->data_amount;
                    $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
                    $stocktransaction->type = StockTransaction::TYPE_OUT;
                    $stocktransaction->good_id = $request->data_goods;
                    $stocktransaction->user_id = Auth::id();
                    $stocktransaction->location_id = $request->data_location;
                    $stocktransaction->location_shelf_id = $request->data_location_shelf_id;
                    $data->stock_transaction()->save($stocktransaction);


                }else if($request->data_log == 3){
                    $data = New GiveBack;
                    $data->description = $request->data_description;
                    $data->good_id = $request->data_goods;
                    $data->user_id = $request->data_user_return;
                    $data->handle_by = Auth::id();
                    $data->amount = $request->data_amount;
                    $data->location_shelf_id = $request->data_location_shelf_id;
                    $data->borrow_id = $request->data_borrow_id;
                    $data->save();

                    $stockentry = Stockentry::find($request->data_entryid);
                    $amount = $stockentry->amount - $stockentry->stock_use;
                    $stockentry->stock_use = $stockentry->stock_use  - $data->amount;
                    if($stockentry->status == 'Out Of Stock'){
                        $stockentry->status = Stockentry::TYPE_STILL_USE;
                    }
                    $stockentry->save();


                    $borrow =Borrow::find($data->borrow_id);
                    $amount_borrow = $borrow->amount - $borrow->stock_back;
                    $borrow->stock_back = $borrow->stock_back  + $data->amount;
                    if($amount_borrow <= $request->data_amount ){
                        $borrow->status = Borrow::DONE;

                    }
                    $borrow->save();

                    $goods = $data->good;

                    $stocktransaction = New Stocktransaction;
                    $stocktransaction->start_balance = $goods->getBalanceByShelf($request->data_location_shelf_id);
                    $stocktransaction->amount = $request->data_amount;
                    $stocktransaction->end_balance = $stocktransaction->start_balance + $stocktransaction->amount;
                    $stocktransaction->type = StockTransaction::TYPE_IN;
                    $stocktransaction->good_id = $request->data_goods;
                    $stocktransaction->user_id = Auth::id();
                    $stocktransaction->location_id = $request->data_location;
                    $stocktransaction->location_shelf_id = $request->data_location_shelf_id;
                    $data->stock_transaction()->save($stocktransaction);

                }else if($request->data_log == 2) {
                    $data = New Allotment;
                    $data->description = $request->data_description;
                    $data->good_id = $request->data_goods;
                    $data->user_id = $request->data_user;
                    $data->handle_by = Auth::id();
                    $data->amount = $request->data_amount;
                    $data->location_shelf_id = $request->data_location_shelf_id;
                    $data->save();


                    $itemallotment = New AllotmentItem;
                    $itemallotment->entry_id =  $request->data_entryid;
                    $itemallotment->allotment_id = $data->id;
                    $itemallotment->amount= $data->amount;
                    $itemallotment->save();

                    $stockentry = Stockentry::find($request->data_entryid);
                    $amount = $stockentry->amount - $stockentry->stock_use;
                    $stockentry->stock_use = $stockentry->stock_use  + $data->amount;
                    if($amount <= $request->data_amount ){
                        $stockentry->status = Stockentry::TYPE_OUT_STOCK;

                    }
                    $stockentry->save();

                    $goods = $data->good;

                    $stocktransaction = New stocktransaction;
                    $stocktransaction->start_balance = $goods->getBalanceByShelf($request->data_location_shelf_id);
                    $stocktransaction->amount = $request->data_amount;
                    $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
                    $stocktransaction->type = StockTransaction::TYPE_OUT;
                    $stocktransaction->good_id = $request->data_goods;
                    $stocktransaction->user_id = Auth::id();
                    $stocktransaction->location_id = $request->data_location;
                    $stocktransaction->location_shelf_id = $request->data_location_shelf_id;
                    $data->stock_transaction()->save($stocktransaction);


                }
            DB::getPdo()->exec('UNLOCK TABLES');
            }catch(\Exception $e){
                DB::statement('ROLLBACK');

                throw $e;
            }

             return response()->json([
                'success' => true,
                'message'   => 'Successfully'
            ]);

        }
    }

    public function expired(Request $request, $id)
    {
        $data = StockEntry::with('location_shelf.location')->find($id);

        $goods = Good::find($data->good_id);

        if ($request->isMethod('post')){

            try{
            DB::statement('SET autocommit=0');
            DB::getPdo()->exec('LOCK TABLES stock_entries WRITE, stock_transactions WRITE, goods WRITE, allotments WRITE, allotment_items WRITE, expireds WRITE, good_shelves WRITE' );

                $expired = New Expired;
                $expired->good_id = $data->good_id;
                $expired->entry_id = $data->id;
                $expired->location_shelf_id = $data->location_shelf_id;
                $expired->amount =  $data->amount - $data->stock_use;
                $expired->location_id = $data->location_shelf->location->id;
                $expired->save();

                $stockentry = StockEntry::find($data->id);
                $stockentry->status = StockEntry::TYPE_EXPIRED;
                $stockentry->save();

                $goods = $expired->good;

                $stocktransaction = New stocktransaction;
                $stocktransaction->start_balance = $goods->getBalanceByShelf($data->location_shelf_id);
                $stocktransaction->amount = $expired->amount;
                $stocktransaction->end_balance = $stocktransaction->start_balance - $stocktransaction->amount;
                $stocktransaction->type = StockTransaction::TYPE_OUT;
                $stocktransaction->good_id = $expired->good_id ;
                $stocktransaction->user_id = Auth::id();
                $stocktransaction->location_id = $data->location_shelf->location->id;
                $stocktransaction->location_shelf_id = $data->location_shelf_id;
                $expired->stock_transaction()->save($stocktransaction);

                $good_shelf = GoodShelf::where('good_id', $data->good_id)->where('location_shelf_id', $data->location_shelf_id)->first();
                if($stocktransaction->end_balance == 0){
                    $good_shelf->delete();
                }

                DB::getPdo()->exec('UNLOCK TABLES');
                }catch(\Exception $e){
                    DB::statement('ROLLBACK');

                    throw $e;
                }


                return response()->json([
                'success' => true,
                'message'   => 'Successfully'
            ]);
        }
    }

    public function view(Request $request, $id, $loop)
    {
            $data = StockEntry::find($id);
            $loop = $loop;
        return view('stockentry.view',['data' => $data, 'loop' => $loop]);
    }
}
