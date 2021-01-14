<?php


namespace App\Http\Controllers;


use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return JsonResponse
     */
    public function show() :JsonResponse
    {
        $user = User::find(auth()->id());
        $companies = $user->companies;
        return response()->json(['companies' => $companies], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request) :JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|string',
            'phone' => 'required|string',
            'description' => 'required|string'
        ]);

        try {
            $company = new Company();
            $company->title = $request->input('title');
            $company->phone = $request->input('phone');
            $company->description = $request->input('description');

            $company->user_id = auth()->id();
            $company->save();

            return response()->json(['company' => $company, 'message' => 'Created'], 201);
        } catch (\Exception $e)
        {
            return response()->json(['message' => 'Company creation failed!'], 409);
        }
    }

}
