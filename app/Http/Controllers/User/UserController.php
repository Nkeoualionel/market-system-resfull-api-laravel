<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::all();
        return $this->showAll($users);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validate = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $this->validate($request, $validate);
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);
       if($user) {
          return $this->showOne($user, 201);
       }

       return $this->error('Not Created', 205);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        return $this->showOne($user);
    }


    /**
     * Display specifyc ressource
     * 
     * @param int $string
     * @return \Illuminate\Http\Client
     *
     */
    public function showByName($name) {
        
        //
        $user = User::findOfFail($name);
        return $this->showOne($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
        $validate = [
            'email' => 'email|unique:users,email' .$user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER. ',' . User::REGULAR_USER,
        ];

        $this->validate($request, $validate);

        if($request->has('name')) {
            $user->name = $request->name;
        }

        if($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        if($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if($request->has('admin')) {
            if(!$user->isVerified()) {
                return $this->error('Only verified user can modify admin field', 409);
            }
        }

        if(!$user->isDirty()) {
            return $this->error('You need to specify a different value to update', 422);
        }

        $user->save();
        //return response()->json(['data' => $user], 200);
        return $this->showOne($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        //return response()->json(['data' => $user], 200);
        $this->showOne($user, 200);
    }
}
