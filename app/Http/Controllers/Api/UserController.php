<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * User Register
     */
    public function register(Request $request)
    {
        $dataValidated = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $dataValidated['password'] = Hash::make($request->password);

        $user = User::create($dataValidated);

        $token = $user->createToken('libreriaDAVID')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            $success['token'] =  $user->createToken('libreriaDAVID')->accessToken;
            $success['user'] =  $user->email;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

    public function show($id = null)
    {
        if ($id == null) {
            $user = Auth::user();
        } else {
            $user = User::find($id);
        }
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User with id ' . $id . ' not found'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $user->toArray()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        if ($id == null) {
            $user = Auth::user();
        } else {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with id ' . $id . ' not found'
                ], 400);
            }
        }
        //Aqui se pondria si tiene un rol de admin que pueda seguir editando
        if (Auth::user()->id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Not same User'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        //Si no hay error actualiza
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('password')) {
            $user->password =  Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Updated user correctly"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
        //Miro si es null por si en un futuro hubiese admins, para que puedan borrar
        //usuarios
        if ($id == null) {
            $user = Auth::user();
        } else {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with id ' . $id . ' not found'
                ], 400);
            }
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted correctly'
        ], 200);
    }
}
