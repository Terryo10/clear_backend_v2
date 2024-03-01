<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\MessageResource;
use App\Models\GroupChat;
use App\Models\Message;
use Illuminate\Http\Request;


class MessageController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, GroupChat $chat)
    {
        $data = $request->validate([
            'message' => 'nullable',
        ]);
        //store message file
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('chat/messages', ['disk' =>   'public']);
            $data['attachment'] = $path;
        }

        if ($request->hasFile('attachment')) {
            //check the file type if its audio and convert it to mp3
            $file = $request->file('attachment');
            $fileType = $file->getMimeType();

            //change file extension to mp3 before storing
            //generate random string to append to file name
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
            $file->move(public_path('/storage/chat/messages'), $randomString . $file->getClientOriginalName());
            $path = 'chat/messages/' . $randomString . $file->getClientOriginalName();

            $data['attachement'] = $path;
        }
        $data['user_id'] = auth()->user()->id;
        $message = $chat->messages()->create($data);
        $messageToEvent =
            [
                'title' => 'New message from ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'body' => $data['message'],
                'type' => 'group',
                'chat_id' => $chat->id,
                'user_id' => auth()->user()->id,
            ];

        broadcast(new MessageSent($messageToEvent))->toOthers();

        return $this->jsonSuccess(200, "Message sent", new MessageResource($message), "message");
    }

    //create function to broadcast notification to users

    public function adminSendMessage(Request $request, GroupChat $chat)
    {
        // $request->validate([
        //     'attachement' => 'mimes:mp3,mp4,doc,docx,pdf,txt,jpg,jpeg,png'
        // ]);

        $data = $request->only([
            'message',
        ]);
        //store message file
        if ($request->hasFile('attachment')) {
            //check the file type if its audio and convert it to mp3
            $file = $request->file('attachment');
            $fileType = $file->getMimeType();

            // dd($fileType);

            //change file extension to mp3 before storing
            //generate random string to append to file name
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
            $file->move(public_path('/storage/chat/messages'), $randomString . $file->getClientOriginalName());
            $path = 'chat/messages/' . $randomString . $file->getClientOriginalName();

            // $path = $request->file('attachement')->store('chat/messages', ['disk' =>   'public']);
            $data['attachment'] = $path;
        }

        $data['user_id'] = auth()->user()->id;
        $message = $chat->messages()->create($data);

        $messageToEvent =
            [
                'title' => 'New message from ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'body' => $data['message'],
                'type' => 'group',
                'chat_id' => $chat->id,
                'user_id' => auth()->user()->id,
            ];

        broadcast(new MessageSent($messageToEvent))->toOthers();
        return  $this->jsonSuccess(200, "Message sent", new MessageResource($message), "message");
    }
    public function contractorSendMessage(Request $request, Chat $chat)
    {
        $data = $request->only([
            'message',
        ]);

        //store message file
        if ($request->hasFile('attachement')) {
            //check the file type if its audio and convert it to mp3
            $file = $request->file('attachement');
            $fileType = $file->getMimeType();
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
            $file->move(public_path('/storage/chat/messages'), $randomString . $file->getClientOriginalName());
            $path = 'chat/messages/' . $randomString . $file->getClientOriginalName();

            // $path = $request->file('attachement')->store('chat/messages', ['disk' =>   'public']);
            $data['attachement'] = $path;
        }
        $data['user_id'] = auth()->user()->id;
        $message = $chat->messages()->create($data);
        $messageToEvent =
            [
                'title' => 'New message from ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'body' => $data['message'],
                'type' => 'group',
                'chat_id' => $chat->id,
                'user_id' => auth()->user()->id,
            ];

        broadcast(new MessageSent($messageToEvent))->toOthers();

        return back()->with('message', 'Message sent succsessfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
