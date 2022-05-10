<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CompanyResource::collection(
            executeQuery(User::query()->where('type', 'company'))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CompanyResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'sales_person_id' => 'nullable|exists:users,id',
            'operation_manager_id' => 'nullable|exists:users,id',
            'general_manager_id' => 'nullable|exists:users,id',
            'production_goal' => 'required|numeric|min:0',
            'wows_goal' => 'required|numeric|min:0',
            'job_goal' => 'required|integer|min:0',
            'active' => 'required|boolean',
        ]);

        $data['type'] = 'company';

        return new CompanyResource(User::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $company
     * @return CompanyResource
     */
    public function show(User $company)
    {
        if ($company->type !== 'company')
            return response()->json(['message' => 'Not found.'], 404);

        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function update(Request $request, User $company)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$company->id,
            'sales_person_id' => 'nullable|exists:users,id',
            'operation_manager_id' => 'nullable|exists:users,id',
            'general_manager_id' => 'nullable|exists:users,id',
            'production_goal' => 'required|numeric|min:0',
            'wows_goal' => 'required|numeric|min:0',
            'job_goal' => 'required|integer|min:0',
            'active' => 'required|boolean',
        ]);

        return $company->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function destroy(User $company)
    {
        return $company->delete();
    }
}
