<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notification = Notification::all();
        return view('admin.notifications', ['data' => $notification]);
    }
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string'
        ]);
    }
}
