<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\ManagerChatMessageResource;
use App\Http\Resources\ManagerChatResource;
use App\Models\ManagerChat;
use App\Models\ManagerChatMessage;
use Illuminate\Http\Request;

class ManagerChatMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ManagerChat $chat)
    {

        $data = $request->validate([
            'message' => 'required',
            'id' => 'required',
        ]);
        $chat = ManagerChat::find($data['id']);

        //store message file
        if ($request->hasFile('attachement')) {
            $path = $request->file('attachement')->store('chat/messages', ['disk' => 'public']);
            $data['attachement'] = $path;
        }
        $data['user_id'] = auth()->user()->id;
        $data['manager_chat_id'] = $chat->id;

        $message = ManagerChatMessage::create([
            'message' => $data['message'],
            'user_id' => $data['user_id'],
            'manager_chat_id' => $data['manager_chat_id'],
            'attachment' => $request->hasFile('attachement') ? $data['attachement'] : null,
        ]);
        $messageToEvent =
            [
                'title' => 'New message from ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'body' => $data['message'],
                'type' => 'manager',
                'chat_id' => $chat->id,
                'user_id' => auth()->user()->id,
            ];
        broadcast(new MessageSent($messageToEvent))->toOthers();

        return $this->jsonSuccess(200, "ManagerChatMessage sent", new ManagerChatMessageResource($message), "message");
    }

    public function adminSendMessage(Request $request, ManagerChat $chat)
    {

        $data = $request->validate([
            'message' => 'required',
            'id' => 'required',
        ]);
        $chat = ManagerChat::findOrfail($data['id']);

        //store message file
        if ($request->hasFile('attachement')) {
            $path = $request->file('attachement')->store('chat/messages', ['disk' => 'public']);
            $data['attachement'] = $path;
        }
        $data['user_id'] = auth()->user()->id;
        $data['manager_chat_id'] = $chat->id;

        $message = ManagerChatMessage::create([
            'message' => $data['message'],
            'user_id' => $data['user_id'],
            'manager_chat_id' => $data['manager_chat_id'],
            'attachment' => $request->hasFile('attachement') ? $data['attachement'] : null,
        ]);

        $messageToEvent =
            [
                'title' => 'New message from ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'body' => $data['message'],
                'type' => 'manager',
                'chat_id' => $chat->id,
                'user_id' => auth()->user()->id,
            ];
        broadcast(new MessageSent($messageToEvent))->toOthers();

        return  $this->jsonSuccess(200, "ManagerChatMessage sent",new ManagerChatMessageResource($message) , "message");
    }

    public function storeContractorMessage(Request $request, ManagerChat $chat)
    {
        $data = $request->validate([
            'message' => 'required',
            'id' => 'required',
        ]);
        $chat = ManagerChat::find($data['id']);

        //store message file
        if ($request->hasFile('attachement')) {
            $path = $request->file('attachement')->store('chat/messages', ['disk' => 'public']);
            $data['attachement'] = $path;
        }
        $data['user_id'] = auth()->user()->id;
        $data['manager_chat_id'] = $chat->id;

        $message = ManagerChatMessage::create([
            'message' => $data['message'],
            'user_id' => $data['user_id'],
            'manager_chat_id' => $data['manager_chat_id'],
            'attachement' => $data['attachement'] ? $data['attachement'] : null,

        ]);

        $messageToEvent =
            [
                'title' => 'New message from ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'body' => $data['message'],
                'type' => 'manager',
                'chat_id' => $chat->id,
                'user_id' => auth()->user()->id,
            ];

        broadcast(new MessageSent($messageToEvent))->toOthers();
        return $this->jsonSuccess(200, "ManagerChatMessage sent", null, "message");

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ManagerChatMessage $message
     * @return \Illuminate\Http\Response
     */
    public function show(ManagerChatMessage $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ManagerChatMessage $message
     * @return \Illuminate\Http\Response
     */
    public function edit(ManagerChatMessage $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ManagerChatMessage $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ManagerChatMessage $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ManagerChatMessage $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManagerChatMessage $message)
    {
        //
    }

}
