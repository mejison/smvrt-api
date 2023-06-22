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

    public function search_property(Request $request) {
        $type = $request->input('type');
        $search = $request->input('search') ?? '';

        $data = [
            'type' => [
                'NDA',
                'MSA',
                'SOW'
            ],
            'category' => [
                'My Documents',
                'Business',
                'Music',
                'Sales',
            ]
        ];

        $items = [];

        if ($search) {
            $items = collect($data[$type])->filter(function($item) use ($search) {
                $search = strtolower($search);
                return strpos(strtolower($item), $search) !== false;
            })->values()->all();
        }

        return response()->json([
            'data' => $items
        ]);
    }
}
