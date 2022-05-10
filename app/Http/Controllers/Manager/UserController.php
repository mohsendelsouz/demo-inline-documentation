<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Return all the users with their roles using UserResource
     * @return UserResource collection
     */
    public function index()
    {
        return UserResource::collection(
            executeQuery(
                User::query()
                    ->where('type', 'user')
                    ->with('roles')
            )
        );
    }

    /**
     * Create a new user
     * @return UserResource
     * It's keep bcrypt password
     * There is a one-to-many relation between users and roles table
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6',
            'production_goal' => 'required|numeric|min:0',
            'wows_goal' => 'required|numeric|min:0',
            'scorecard_goal' => 'required|numeric|min:0',
            'job_goal' => 'required|integer|min:0',
            'active' => 'required|boolean',
            'roles.*' => 'required|in:Admin,Technician,Sales Person,Operation Manager,General Manager',
        ]);

        $data['password'] = bcrypt($data['password']);
        $data['type'] = 'user';
        $rolesInput = $data['roles'];

        unset($data['roles']);

        $user = User::create($data);
        $roles = [];

        foreach ($rolesInput as $role) {
            $roles[] = new UserRole([
                'role' => $role,
                'commission_id' => $request->commissions[$role]
            ]);
        }

        $user->roles()->saveMany($roles);

        return new UserResource($user);
    }

    /**
     * Return a specific user details with roles
     * @return UserResource
     */
    public function show(User $user)
    {
        if ($user->type !== 'user')
            return response()->json(['message' => 'Not found.'], 404);

        return new UserResource($user->load('roles'));
    }

    /**
     * Update a specific user
     * @return bool
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6',
            'production_goal' => 'required|numeric|min:0',
            'wows_goal' => 'required|numeric|min:0',
            'job_goal' => 'required|integer|min:0',
            'scorecard_goal' => 'required|numeric|min:0',
            'active' => 'required|boolean',
            'roles.*' => 'required|in:Admin,Technician,Sales Person,Operation Manager,General Manager',
        ]);

        if (isset($data['password']) && $data['password'] !== '')
            $data['password'] = bcrypt($data['password']);
        else
            unset($data['password']);

        // Roles
        $existingRoles = $user->roles->pluck('role')->toArray();
        $rolesInput = $data['roles'];
        $roles = [];
        unset($data['roles']);

        foreach ($rolesInput as $role) {
            if (in_array($role, $existingRoles)) {
                $user->roles()->where('role', $role)->update([
                    'commission_id' => $request->commissions[$role]
                ]);

                $existingRoles = array_filter($existingRoles, function ($t) use ($role) {
                    return $t != $role;
                });
            } else {
                $roles[] = new UserRole([
                    'role' => $role,
                    'commission_id' => $request->commissions[$role]
                ]);
            }
        }

        $user->roles()->saveMany($roles);

        foreach ($existingRoles as $role)
            $user->roles()->where('role', $role)->delete();

        return $user->update($data);
    }

    /**
     * Delete a specific user
     * @return bool
     */
    public function destroy(User $user)
    {
        return $user->delete();
    }

    /**
     * Return all sales persons with their commissions
     * @return UserResource collection
     */
    public function salesPersons()
    {
        return UserResource::collection(User::whereHas('roles', function($q) {
            $q->where('role', 'Sales Person');
        })->with('roles.commission')->get());
    }

    /**
     * Return all operation managers with their commissions
     * @return UserResource collection
     */
    public function operationManagers()
    {
        return UserResource::collection(User::whereHas('roles', function($q) {
            $q->where('role', 'Operation Manager');
        })->with('roles.commission')->get());
    }

    /**
     * Return all general managers with their commissions
     * @return UserResource collection
     */
    public function generalManagers()
    {
        return UserResource::collection(User::whereHas('roles', function($q) {
            $q->where('role', 'General Manager');
        })->with('roles.commission')->get());
    }

    /**
     * Return all technicians with their commissions
     * @return UserResource collection
     */
    public function technicians()
    {
        return UserResource::collection(User::whereHas('roles', function($q) {
            $q->where('role', 'Technician');
        })->with('roles.commission')->get());
    }
}
