<?php
    
namespace App\Console\Commands;

use App\Models\Api\UserMatchingRecord;
use App\Models\Api\Item;
use Illuminate\Console\Command;
use App\Http\Controllers\Api\MatchingController;
    
class MatchingScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:cron';
     
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Matching Products';
     
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
     * @return mixed
     */
    public function handle()
    {
        // \Log::info("Cron is working fine!");
      
        /*
           Write your database logic we bellow:
           Item::create(['name'=>'hello new']);
        */

        $users = Item::get()->pluck('user_id','user_id');
        foreach($users as $user){
            $result = (new MatchingController)->index($user);
            $set_data = new UserMatchingRecord();
            $set_data->user_id = $user;
            $set_data->matching_records = json_encode($result);
            $set_data->save();
        }

    }
}