<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function getNotifications()
    {
        $notifications = Notification::where('user_id', Auth::user()->id)->paginate(20);

        return $this->jsonSuccess(200, 'Request Successful', $notifications, 'notifications');
    }
}
