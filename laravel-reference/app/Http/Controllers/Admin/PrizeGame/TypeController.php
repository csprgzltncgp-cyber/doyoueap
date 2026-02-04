<?php

namespace App\Http\Controllers\Admin\PrizeGame;

use App\Http\Controllers\Controller;
use App\Models\PrizeGame\Type;

class TypeController extends Controller
{
    public function index()
    {
        $types = Type::all();

        return view('admin.prizegame.types.index', ['types' => $types]);
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => 'string',
        ]);

        Type::query()->create($attributes);

        return redirect()->back();
    }
}
