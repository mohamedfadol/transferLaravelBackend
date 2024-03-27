<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::get();
        if(is_null($users)) {
            return  ["success" => false, "message" => "There no users data"];
        }
        return $this->success([
            'users' => $users,
        ], 'User successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function fetchUser($id)
    {
        \Log::info($id);
        $user = User::findOrFail($id);
        if(is_null($user)) {
            return  ["success" => false, "message" => "There no user data"];
        }
        return $this->success([
            'user' => $user,
        ], 'User successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
