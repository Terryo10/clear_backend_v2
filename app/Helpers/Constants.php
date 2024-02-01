<?php
namespace App\Helpers;


// abstract class RequestStatus
// {
//     const ContractorSent = 'Contractor Sent';
//     const RequestSent = 'Request Sent';
//     const SignedOff = 'Signed Off';
// }
// abstract class ProjectStatus
// {
//     const InProgress = 'Project In Progress';
//     const Paused = 'Paused';
//     const Completed = 'Completed';
//     // etc.
// }

abstract class NotificationTypes
{
    const Global = 'Global';
    const Request = 'Request';
    const Project = 'Project';
    const Chat = 'Chat';
    const ChatRequest = 'Chat Request';
    const ChatMessage = 'Chat Message';

}
