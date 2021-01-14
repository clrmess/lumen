<?php


namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return JsonResponse
     */
    public function showAll() :JsonResponse
    {
        $data = DB::table('users')
            ->select(DB::raw('CONCAT(users.first_name, \' \', users.last_name) as full_name'),
                'email',
                DB::raw('coalesce(string_agg(companies.title, \', \'), \'Empty\') as companies'))
            ->leftJoin('companies', 'users.id', '=','companies.user_id')
            ->groupBy('full_name', 'email')
            ->get();
        return response()->json(['users' => $data], 200);
    }

}
