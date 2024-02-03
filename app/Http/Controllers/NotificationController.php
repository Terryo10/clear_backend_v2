<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Interfaces\Notifications\NotificationRepoInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationRepo;
    public function __construct(NotificationRepoInterface $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }
    public function getAdminNotifications()
    {
        $notifications = $this->notificationRepo->getAdminNotifications();
        return NotificationResource::collection($notifications);
    }

    public function getUserNotifications()
    {
        $notifications = $this->notificationRepo->getNotifications(auth()->user()->id);
        return NotificationResource::collection($notifications);
    }

    public function readAllNotifications(Request $request)
    {
        // dd($request->all());
        $this->notificationRepo->readAllNotifications(auth()->user()->id);

        return redirect()->back()->with('message', 'Notifications Marked as Read');
    }

    public function readNotification(Request $request)
    {
        $this->notificationRepo->readNotification($request->id);

        return redirect()->back()->with('message', 'Notification Marked as Read');
    }

    public function readAllAdminNotifications()
    {
        $this->notificationRepo->readAllAdminNotifications();

        return redirect()->back()->with('message', 'Notifications Marked as Read');
    }
}
