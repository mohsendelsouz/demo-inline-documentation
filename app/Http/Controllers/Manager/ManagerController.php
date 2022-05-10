<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManagerResource;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return ManagerResource::collection(executeQuery(User::where('type', 'manager')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ManagerResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6',
            'operational_manager_commission' => 'required|numeric|min:0|max:100',
            'general_manager_commission' => 'required|numeric|min:0|max:100',
            'sales_commission' => 'required|numeric|min:0|max:100',
            'active' => 'required|boolean',
        ]);

        $data['password'] = bcrypt($data['password']);
        $data['type'] = 'manager';

        return new ManagerResource(User::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return ManagerResource
     */
    public function show(User $manager)
    {
        if ($manager->type !== 'manager')
            return response()->json(['message' => 'Not found.'], 404);

        return new ManagerResource($manager);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function update(Request $request, User $manager)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$manager->id,
            'password' => 'nullable|min:6',
            'operational_manager_commission' => 'required|numeric|min:0|max:100',
            'general_manager_commission' => 'required|numeric|min:0|max:100',
            'sales_commission' => 'required|numeric|min:0|max:100',
            'active' => 'required|boolean',
        ]);

        if (isset($data['password']) && $data['password'] !== '')
            $data['password'] = bcrypt($data['password']);
        else
            unset($data['password']);

        return $manager->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function destroy(User $manager)
    {
        return $manager->delete();
    }
}
