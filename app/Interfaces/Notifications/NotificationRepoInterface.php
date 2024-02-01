<?php

namespace App\Interfaces\Notifications;

interface NotificationRepoInterface
{
    public function getAdminNotifications();
    public function getNotifications($user_id);
    public function createNotification(array $data);

    public function broadCastNotification(array $users, array $notificationData);

    //send email notification
    public function sendEmailNotification(array $data);

    //read all notifications
    public function readAllNotifications($user_id);

    //read single notification
    public function readNotification($notification_id);

    //read all admin notifications
    public function readAllAdminNotifications();
}
