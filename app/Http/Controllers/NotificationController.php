<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public $notification;

    public function __contruct()
    {
        $this->notification = new Notification;
    }

    public function userNotification(Request $request)
    {
        try {
            return response(Notification::when(!empty($request->status), function($query) use ($request){
                $query->where('category',$request->status);
            })->where('user_id', auth()->user()->id)->orderBy('created_at','desc')->paginate(10));
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function readNotification($notification_id)
    {
        try {
            return response(Notification::where(['user_id' => auth()->user()->id, 'id' => $notification_id])->update(['status' => true]));
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function deleteNotification($notification_id)
    {
        try {
            return response(Notification::destroy($notification_id));
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function deleteAllNotification()
    {
        try {
            return response(Notification::where('user_id', auth()->user()->id)->delete());
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }
}
