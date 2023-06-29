<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpenAIController extends Controller
{
    public function create_req(Request $request) {

        $content = $request->input('content');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "model": "text-davinci-003",
            "prompt": "Summarize this agreement document:' . $content . '",
            "temperature": 0.7,
            "max_tokens": 256,
            "top_p": 1,
            "frequency_penalty": 0,
            "presence_penalty": 0
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('OPENAI_TOKEN', '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')
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return response()->json([
            'data' => $response,
        ]);
    }
}
