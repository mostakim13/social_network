<?php

namespace App\Http\Controllers;

use App\Models\FollowPerson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class FollowPersonCOntroller extends Controller
{
    public function followPerson(Request $request, $personId)
    {

        if ($request->isMethod("put")) {
            $data = $request->all();

            $follow = new FollowPerson();
            $follow->user_id =  auth()->user()->id;
            $follow->following_id = $personId;
            $follow->save();

            $message = "Follow successfully!";
            return response()->json(["message" => $message]);
        }
    }
}