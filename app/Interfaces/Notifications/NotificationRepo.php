<?php

namespace App\Interfaces\Notifications;

use App\Events\NotifyUser;
use App\Models\Notification as ModelsNotification;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Support\Facades\Mail;

class NotificationRepo implements NotificationRepoInterface
{
    public function getNotifications($user_id)
    {
        return ModelsNotification::where('user_id', $user_id)
            ->where('read', false)
            ->orderBy('created_at', 'desc')->paginate(10);
    }

    public function createNotification(array $data)
    {
        return ModelsNotification::create($data);
    }

    public function broadCastNotification(array $users, array $notificationData)
    {
        if ($notificationData['type'] == 'Global') {
            $notification = $this->createNotification([
                'title' => $notificationData['title'],
                'body' => $notificationData['body'],
                'type' => $notificationData['type'],
            ]);
            $adminUsers = $this->getAdminUsers();
            foreach ($adminUsers as $adminUser) {
                $this->broadcastNotifyEvent($adminUser->id, $notification);
                $this->firebaseNotification([
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'type' => $notificationData['type'],
                ], $adminUser);
            }
        } else {
            foreach ($users as $user) {
                $notification = $this->createNotification([
                    'user_id' => $user->user_id ? $user->user_id : $user->id,
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'type' => $notificationData['type'],
                ]);
                $this->broadcastNotifyEvent($user->id, $notification);
                $this->firebaseNotification([
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'type' => $notificationData['type'],
                ], $user->user_id ? $user->user : $user);
            }
        }
    }

    public function getAdminUsers()
    {
        return User::whereHas('roles', function ($query) {
            $query->whereHas('role', function ($query) {
                $query->where('name', 'admin');
            });
        })->get();
    }

    //firebase notification
    public function firebaseNotification($data, $user)
    {
        try {
            $user->notify(new SendPushNotification($data, [$user->fcm_token]));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return true;
    }

    //send email notification
    public function sendEmailNotification(array $data)
    {
        $email = $data['email'];
        $subject = $data['subject'];
        $email_message = $data['email_message'];
        try {
            Mail::send('emails.email', ['message' => $email_message], function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
        return true;
    }

    public function broadcastNotifyEvent($user_id, $notification)
    {
        try {
            broadcast(new NotifyUser($user_id, $notification))->toOthers();
        } catch (\Throwable $th) {
            //throw $th;
        }
        return true;
    }

    //read all notifications
    public function readAllNotifications($user_id)
    {
        $notifications = ModelsNotification::where('user_id', $user_id)->get();
        foreach ($notifications as $notification) {
            $notification->read = true;
            $notification->save();
        }
        return true;
    }

    //read all admin notifications
    public function readAllAdminNotifications()
    {
        $notifications = ModelsNotification::where('type', 'Global')->get();
        foreach ($notifications as $notification) {
            $notification->read = true;
            $notification->save();
        }
        return true;
    }



    //read single notification
    public function readNotification($notification_id)
    {
        $notification = ModelsNotification::find($notification_id);
        $notification->read = true;
        $notification->save();
        return true;
    }

    public function getAdminNotifications()
    {
        return ModelsNotification::where('type', 'Global')
            ->where('read', false)
            ->orderBy('created_at', 'desc')->paginate(10);
    }
}
