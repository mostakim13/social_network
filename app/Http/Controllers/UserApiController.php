<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                "first_name" => "required",
                "last_name" => "required",
                "email" => "required|email|unique:users",
                "password" => "required"
            ];
            $customMessage = [
                "first_name.required" => "First name is required",
                "last_name.required" => "Last name is required",
                "email.required" => "Email is required",
                "email.email" => "Email must be a valid email",
                "password.required" => "Password is required"
            ];
            $validator = validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = new User();
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();

            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                $user = User::where('email', $data['email'])->first();
                $access_token = $user->createToken($data['email'])->accessToken;
                User::where('email', $data['email'])->update(['access_token' => $access_token]);
                $message = "User successfully registered";
                return response()->json(['message' => $message, 'access_token' => $access_token], 201);
            } else {
                $message = "Oops! Something went wrong";
                return response()->json(['message' => $message], 422);
            }
        }
    }


    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                "email" => "required|email|exists:users",
                "password" => "required"
            ];
            $customMessage = [
                "email.required" => "Email is required",
                "email.email" => "Email must be valid",
                "email.exists" => "Email does not exists",
                "password.required" => "Password is required"
            ];
            $validator = validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                $user = User::where('email', $data['email'])->first();
                $access_token = $user->createToken($data['email'])->accessToken;
                User::where('email', $data['email'])->update(['access_token' => $access_token]);

                $message = "User successfully login";
                return response()->json(['message' => $message, "access_token" => $access_token], 201);
            } else {
                $message = "Invalid email or password";
                return response()->json(['message', $message], 422);
            }
        }
    }

    public function followPerson(Request $request, $personId)
    {
        if ($request->isMethod("put")) {
            $user = User::findOrFail($personId);
            $check = implode(',', [$user->follow_person]);

            if ($personId == auth()->id()) {
                $message = "You Can Not follow Youself!";
                return response()->json(["message" => $message]);
            }

            if (str_contains($check, auth()->id() . ",")) {
                $message = "You Are Already Followed!";
                return response()->json(["message" => $message]);
            }

            $implode = implode(',', [auth()->id(), $user->follow_person]);
            $user->follow_person = $implode;
            $user->save();

            $message = "Follow successfully!";
            return response()->json(["message" => $message]);
        }
    }

    public function followPage(Request $request, $pageId)
    {
        if ($request->isMethod("put")) {

            $page = Page::findOrFail($pageId);
            $check = implode(',', [$page->follower]);

            if (str_contains($check, auth()->id() . ",")) {
                $message = "You Are Already Followed!";
                return response()->json(["message" => $message]);
            }

            $implode = implode(',', [auth()->id(), $page->follower]);
            $page->follower = $implode;
            $page->save();

            $message = "Follow successfully!";
            return response()->json(["message" => $message]);
        }
    }

    public function attachpost(Request $request)
    {
        if ($request->isMethod("post")) {
            $data = $request->all();

            $rules = [
                "post_content" => "required",
            ];
            $customMessage = [
                "post_content.required" => "Post Content is required",
            ];
            $validator = validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $page = new Post();
            $page->post_content = $data['post_content'];;
            $page->page_id = 0;
            $page->post_by = auth()->id();
            $page->save();

            $message = "Post Create Successfully!";
            return response()->json(["message" => $message]);
        }
    }
    public function attachpagepost(Request $request, $page_id)
    {
        if ($request->isMethod("post")) {
            $data = $request->all();

            $rules = [
                "post_content" => "required",
            ];
            $customMessage = [
                "post_content.required" => "Post Content is required",
            ];
            $validator = validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $page =  Page::where('created_by', auth()->id())->where('id', $page_id)->first();
            if (!$page) {
                $message = "You can't Create Post in this Page!";
                return response()->json(["message" => $message]);
            }
            $page = new Post();
            $page->post_content = $data['post_content'];
            $page->page_id = $page_id;
            $page->post_by = auth()->id();
            $page->save();

            $message = "Post Create Successfully!";
            return response()->json(["message" => $message]);
        }
    }
    public function personfeed(Request $request)
    {
        if ($request->isMethod("get")) {
            $data = $request->all();

            $data = [
                "Page" => auth()->user()->page,
                "Post_By_Page"  => auth()->user()->postByPage,
                "Single_Post"  => auth()->user()->singlePost,
            ];

            $message = $data;
            return response()->json(["message" => $message]);
        }
    }
}
