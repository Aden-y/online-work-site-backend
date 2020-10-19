<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Order;
use App\Message;
use App\MessageFile;
use App\OrderMessage;
use App\OrderMessageFile;

class MessagesController extends Controller
{
    public function send(Request $request)
    {
        $user = auth()->user();
        $destination = $request->input('destination');
        $message = ltrim(rtrim($request->input('message')));
        $filescount = (int)$request->input('filescount');
        if($message == '' && $filescount == 0) {
            return response()->json(['error'=>'Incomplete data'],400);
        }
        if($dest = User::find($destination)) {
            if($orderid = $request->input('orderid')){
                if($order = Order::find($orderid)){
                    OrderMessage::create([
                        'destination' => $dest->id,
                        'source'=> $user->id,
                        'message'=> $message,
                        'order_id'=> $order->id
                    ]);
                    return response()->json(['message'=>'Message sent.']);
                }else{
                    return response()->json(['error'=>'Unknown Order'],404);
                }
            }else{
                Message::create([
                    'destination' => $dest->id,
                    'source'=> $user->id,
                    'message'=> $message
                ]);
                return response()->json(['message'=>'Message sent.']);
            }
        }else {
            return response()->json(['error'=>'Unknown Destination'],404);
        }
    }


    public function order_messages($id) {
        if($order = Order::find($id)){
            $user = auth()->user();
            $messages = $order->messages;
            $_messages = [];
            if($user->type == 'Client') {
                $conversations = MessagesController::getConversations($messages, $user);
                foreach($conversations as $conversation) {
                    $_user = User::find($conversation);
                    $_conversation = ['reply_to'=>$conversation, 'name'=>$user->firstname.' '.$user->lastname, 'messages'=>MessagesController::getMessages($conversation, $user, $order->id)];
                    array_push($_messages, $_conversation);
                }
                $data = ['role' => $user->type, 'messages'=>$_messages, 'order'=>$order->topic];
                return response()->json($data, 200);
            }else {
                foreach($messages as $message) {
                    // $message->status = 'Delivered';
                    if($message->source == $user->id || $message->destination == $user->id){
                        $_message = ['text'=> $message->message, 'time'=>$message->created_at];
                        $_message['type'] = $message->source == $user->id ? 'Outgoing' : 'Incomming';
                        // $message->save();
                        array_push($_messages, $_message);
                    }
                }
                $data = ['role' => $user->type, 'messages'=>$_messages, 'order'=>$order->topic, 'reply_to'=> $order->client_id];
                return response()->json($data, 200);
            }
        }else {
            return response()->json(['error'=> 'Job not found'], 404);
        }
        //$user = auth()->user();

        //$messages = OrderMessage::where('destination', '=', $user->id)->orWhere('source', '=', $user->id)->get();

       // return response()->json($messages);
    }

    private static function getConversations($messages, $user) {
        $conversations = [];
        foreach($messages as $message) {
            if(!in_array($message->source, $conversations) && $message->source != $user->id) {
                array_push($conversations, $message->source);
            }
            if(!in_array($message->destination, $conversations) && $message->destination != $user->id) {
                array_push($conversations, $message->destination);
            }
        }
        return $conversations;
    }

    private static function getMessages($userid, $user, $order= null) {
        if($order !=  null) {
            $messages = OrderMessage::where('order_id', '=', $order)->where('destination', '=' , $userid)->orWhere('source', '=' , $userid)->get();
        }else {
            $message = [];
        }
        $_messages = [];
        foreach($messages as $message) {
            if($message->source == $user->id || $message->destination == $user->id){
                $_message = ['text'=> $message->message, 'time'=>$message->created_at];
                $_message['type'] = $message->source == $user->id ? 'Outgoing' : 'Incomming';
                array_push($_messages, $_message);
            }
        }
        return $_messages;
    }
}
