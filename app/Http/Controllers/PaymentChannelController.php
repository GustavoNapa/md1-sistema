<?php

namespace App\Http\Controllers;

use App\Models\PaymentChannel;
use Illuminate\Http\Request;

class PaymentChannelController extends Controller
{
    public function index()
    {
        $channels = PaymentChannel::where('active', true)->orderBy('name')->get();
        return view('payment_channels.index', compact('channels'));
    }
}
