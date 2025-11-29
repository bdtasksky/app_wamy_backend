<?php

namespace Modules\Accounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SubController extends Controller
{
    public function subtype($id = null)
    {
        $title = 'Subtype'; 

        if (!empty($id)) {
            $intInfo = DB::table('acc_subtype')->where('id', $id)->first();
            return view('accounts::sub.subtype', compact('title', 'intInfo'));
        }

        $perPage = 25; // Number of items per page
        $subtypes = DB::table('acc_subtype')->paginate($perPage);

        // Passing data to the view
        return view('accounts::sub.subtype', compact('title', 'subtypes'));
    }

    public function store(Request $request){

    }

    public function destroy($id){

    }
}
