<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return Message::latest()->take(50)->get()->reverse()->values();
    }

    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        return Message::create([
            'user_id' => 1,
            'text' => $request->text
        ]);
    }
}
