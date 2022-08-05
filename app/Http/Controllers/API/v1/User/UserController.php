<?php

namespace App\Http\Controllers\API\v1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Main\Controller;
use App\Models\User\UserWGSKarawang;
use App\Models\User\UserWGSSubang;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(
            [
                'message' => "What are you looking for?",
            ],
            200
        );
    }

    public function listUserSubang()
    {
        return UserWGSSubang::paginate(10);
    }

    public function listUserKarawang()
    {
        return UserWGSKarawang::paginate(10);
    }

    public function removeUserKarawang($id)
    {
        $user = UserWGSKarawang::whereId($id)->first();

        if($user === NULL){
            return response(
                [
                    'message' => "User tidak ditemukan",
                ],
                404
            );
        }else{

            $user->is_active = 0;
            $user->is_deleted = 1;
            $user->email = "isremoved-".carbon::now()->format("Ymdhis")."-".$user->email;
            $user->phone = "rmv".$user->phone;
            $user->save();

            return response(
                [
                    'message' => "Akun $user->name berhasil dihapus",
                ],
                200
            );
        }

    }

    public function removeUserSubang($id)
    {
        $user = UserWGSSubang::whereId($id)->first();

        if($user === NULL){
            return response(
                [
                    'message' => "User tidak ditemukan",
                ],
                404
            );
        }else{

            $user->is_active = 0;
            $user->is_deleted = 1;
            $user->email = "isremoved-".carbon::now()->format("Ymdhis")."-".$user->email;
            $user->phone = "rmv".$user->phone;
            $user->save();

            return response(
                [
                    'message' => "Akun $user->name berhasil dihapus",
                ],
                200
            );
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
