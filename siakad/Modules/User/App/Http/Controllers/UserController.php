<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Master\App\Models\MGenerateCodeTab;
use Modules\User\App\Models\UsersTab;

class UserController extends Controller
{
    protected $usersTab;
    protected $controller;
    protected $mGenerateCodeTab;
    public function __construct(
        UsersTab $user, Controller $controller,
        MGenerateCodeTab $mGenerateCodeTab
    ) {
        $this->usersTab = $user;
        $this->mGenerateCodeTab = $mGenerateCodeTab;
        $this->controller = $controller;
    }

    public function register(Request $request)
    {
        $this->controller->validasi($request, [
            'email' => 'required|email|unique:users_tabs,email',
            'name' => 'required',
            'password' => 'required|min:8',
            'nim' => 'required|unique:users_tabs,nim',
            'birthday' => 'required',
            'code' => 'required|unique:users_tabs,code',
        ]);

        try {
            DB::beginTransaction();
            $request['password'] = Hash::make($request->password,);
            $request['code'] = $this->mGenerateCodeTab->generateCode('USR');
            $user = $this->usersTab->create($request->all());
            DB::commit();
            return response(
                'USER CREATED', 200,
                [
                    'token' => $user->createToken('siakad')->plainTextToken
                ],
                [
                    'title'=> 'user berhasil dibuat',
                    'body'=> '',
                    'type'=> '',
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('user::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('user::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('user::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
