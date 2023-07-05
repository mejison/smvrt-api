<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpenAIController extends Controller
{
    public function create_req(Request $request) {
        $post = json_decode(file_get_contents('php://input'), true);

        $prompt = ! empty($post['prompt']) ? $post['prompt'] : 'Summarize this agreement document: ';
        $max_tokens = ! empty($post['max_tokens']) ? $post['max_tokens'] : 256;
        $content = ! empty($post['content']) ? $post['content'] : '';

        $content = ltrim(strip_tags($content), '"');

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
            "prompt": "' . $prompt . $content . '",
            "temperature": 0.7,
            "max_tokens": ' . $max_tokens . ',
            "top_p": 1,
            "frequency_penalty": 0,
            "presence_penalty": 0
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('OPENAI_TOKEN')
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return response()->json([
            'data' => $response,
        ]);
    }
}
