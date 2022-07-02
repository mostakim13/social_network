<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PageController extends Controller
{
    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                "page_name" => "required",
            ];
            $customMessage = [
                "page_name.required" => "Page name is required",
            ];
            $validator = validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $page = new Page();
            $page->page_name = $data['page_name'];
            $page->created_by = auth()->id();
            $page->save();
            $message = 'Page successfully added!';
            return response()->json(['message' => $message], 201);
        }
    }
}
