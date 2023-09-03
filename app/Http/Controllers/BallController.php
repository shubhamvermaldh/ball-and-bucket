<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ball;

class BallController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'color' => 'required|string',
            'size' => 'required',
        ]);

        $bucket = new Ball($validatedData);
        $bucket->save();

        return redirect()->route('home')->with('success', 'Bucket created successfully.');
    }
}
