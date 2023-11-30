<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slave;
use App\Models\SlaveCategory;
use App\Models\SlaveSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

Class SlaveController extends Controller
{
    //get all existing slaves
    public function index()
    {
        $data = Slave::all();
        //alternatively
        //$data = DB::table('slaves')->get();

        //both queries^
        //$query = "SELECT * FROM `slaves`";
        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        return response()->json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    //get one slave
    public function getOne(Slave $id)
    {
        $slave = $id;
        //query^
        //$query = "SELECT * FROM `slaves` WHERE `id` = " . $slave->id;
        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        return response()->json($slave, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    //get slave categories
    public function getCategories()
    {
        $data = SlaveCategory::all();
        //query^
        //$query = "SELECT * FROM `slave_category`";
        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        return response()->json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    //get all slaves by category id
    public function getSlavesByCategory(SlaveCategory $id)
    {
        $category = $id;
        $data = Slave::whereBelongsTo($category)->get();
        //query^
        //$query = "SELECT * FROM `slaves` WHERE `slaves`.`category_id` IN (" . $category->id . ")";
        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        return response()->json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function rentSlave(Request $request, Slave $id)
    {
        $slave = $id;
        $user['is_vip'] = false;
        $schedules_data = [];

        //to timestamp
        $new_start = Carbon::createFromFormat('Y-m-d H:m:s', $request->new_start)->timestamp / 3600;
        $new_end = Carbon::createFromFormat('Y-m-d H:m:s', $request->new_end)->timestamp / 3600;
        $days = $request->days;

        $schedules = SlaveSchedule::where('slave_id', $slave->id)->get();
        $new_schedule = new SlaveSchedule();
        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        //calculate price if user has selected № of days
        if($days && ($new_end - $new_start > 23)) {
            $total_price = (16 * intval($days) * $slave->hour_price);
        } elseif($days && ($new_end - $new_start < 23)) {
            $count_hours = $new_end - $new_start;
            $total_price = ($count_hours * $days) * $slave->hour_price;
        } else {
            $count_hours = $new_end - $new_start;
            $total_price = $count_hours * $slave->hour_price;
        }

        if(!$schedules->isEmpty() && $user['is_vip'] !== true) {
            foreach($schedules as $item) {
                //to timestamp
                $start = Carbon::createFromFormat('Y-m-d H:m:s', $item->start)->timestamp / 3600;
                $end = Carbon::createFromFormat('Y-m-d H:m:s', $item->end)->timestamp / 3600;
                $schedules_data[] = [$item->start, $item->end];

                //checkInter – time interval intersection check method from SlaveSchedule::class
                if($new_schedule->checkInter($start, $end, $new_start, $new_end) === true) {
                    $message = "This time interval is already occupied, see intervals below";
                    return response()->json([
                        'message' => $message,
                        'occupied_intervals' => $schedules_data,
                    ], 200, $headers, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        SlaveSchedule::create([
            'slave_id' => $id->id,
            'start' => $request->new_start,
            'end' => $request->new_end,
        ]);

        $response = "You have successfully rented a slave from " . $request->new_start . " to " . $request->new_end . ".\n\n" .
            "Your total price: " . $total_price . ".";

        return response()->json($response, 200, $headers, JSON_UNESCAPED_UNICODE);
    }
}
