<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;

class DocumentController extends Controller
{
    public function get_types(Request $request) {
        $types = DocumentType::all();
        return response()->json([
            'data' => $types,
        ]);
    }
}
