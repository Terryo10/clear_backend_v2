<?php

namespace App\Http\Controllers;

use App\Events\NotifyUser;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification as ModelsNotification;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function jsonError($statusCode = 500, $message = "Unexpected Error",$data = [], $key): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "success" => false,
            "status" => $statusCode,
            "message" => $message,
            $key => $data,
        ], $statusCode);
    }

    public function jsonSuccess($statusCode = 200, $message = "Request Successful", $data = [], $key): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "success" => true,
            "status" => $statusCode,
            "message" => $message,
            $key => $data
        ], $statusCode);
    }

    public function sendEmail($subject, $email_message, $email)
    {
        try {
            Mail::send('emails.email', ['message' => $email_message], function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function firebaseNotification($data, $user)
    {
        $user->notify(new SendPushNotification($data['title'], $data, [$user->fcm_token]));
    }

    public function broadcastNotification($users, $notificationData)
    {
        if ($notificationData['type'] == 'Global') {
            //get admin users
            $adminUsers = User::where('role', 'ADMIN')->get();

            foreach ($adminUsers as $adminUser) {

                $notification = ModelsNotification::create([
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'type' => $notificationData['type'],
                    'user_id' => $adminUser->id,
                ]);
                broadcast(new NotifyUser($adminUser->id, $notification))->toOthers();
                $data = [
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'type' => $notificationData['type'],
                ];
                $this->firebaseNotification($data, $adminUser);
            }
        } else {
            foreach ($users as $user) {
                $notification = ModelsNotification::create([
                    'user_id' => $user->user_id ? $user->user_id : $user->id,
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'type' => $notificationData['type'],
                ]);
                broadcast(new NotifyUser($user->id, $notification))->toOthers();
                $data = [
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'type' => $notificationData['type'],
                ];
                $this->firebaseNotification($data, $user->user_id ? $user->user : $user);
            }
        }
    }

    public function createNotification($title, $body, $user, $type)
    {
        $notification = $user->notifications()->create([
            'title' => $title,
            'body' => $body,
            'type' => $type,
        ]);
        broadcast(new NotifyUser($user->id, $notification))->toOthers();
    }
}
