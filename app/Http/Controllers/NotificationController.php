<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Exception;

class NotificationController extends Controller
{
    public $notification;

    public function __contruct(){
        $this->notification = new Notification();
    }

    public function userNotification(){
        try {
            return response(Notification::where('user_id',auth()->user()->id)->get());
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function readNotification($notification_id){
        try {
            return response(Notification::where(['user_id' => auth()->user()->id,'id' => $notification_id])->update(['status' => true]));
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function deleteNotification($notification_id){
        try {
            return response(Notification::destroy($notification_id));
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function deleteAllNotification(){
        try {
            return response(Notification::where('user_id',auth()->user()->id)->delete());
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }
}
