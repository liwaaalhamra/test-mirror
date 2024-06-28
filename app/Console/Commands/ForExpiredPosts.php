<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use App\Models\Api\Item;
use App\Models\Api\Users;
use App\Models\Api\Inbox;
use App\Models\Api\UserPush;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ForExpiredPosts extends Command 
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expired:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dynamic Scheduled to send push notifications for users that have expired posts.';

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
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d');
        Item::whereDate('expired_at','<',$now)->whereNotIn('status',['expired'])->get()->map(function($value, $key) {
            if($user = Users::where('id','=',$value->user_id)->where('is_blocked','=',0)->first()){
                if($user_push = UserPush::whereUsersId($user->id)->first()){
                    $player_id = $user_push->player_id;
                    $contents = ["en" => "Unfortunately, your $value->name_en post is expired. Go ahead and renew it!"];
                    $headings = ["en" => "Expired Post!"];

                    $info['contents'] = $contents;
                    $info['headings'] = $headings;
                    $info['player_ids'] = [$player_id];
                    if(isset($request->date)){
                        $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
                    }
                    $controller = new Controller();
                    $result = $controller->oneSignal($info);
                    
                    $code = Controller::number_generator(6);

                    Inbox::create([
                        'user_id' => $user->id,
                        'title' => 'Expired Post!',
                        'message' => "Unfortunately, your $value->name_en post is expired. Go ahead and renew it!",
                        'image' => null,
                        'type' => 'targeted',
                        'code' => $code
                    ]);
                }
            }

            $value->status = 'expired';
            $value->is_available = false;
            $value->save();
            return $value;
        })->values();

        $before_30_days = Carbon::now()->subDays('30')->startOfDay();
        $items_to_be_auto_deleted = Item::where('expired_at','<',$before_30_days)->get();
        foreach($items_to_be_auto_deleted as $delete){
            $delete->delete();
        }
        return response()->json(['status' => 'Alerts sent of expired Items']);
    }
}
