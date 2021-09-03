<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

trait CommonTrait
{
    function register_uuid()
    {
        $uuid = session()->get('uuid');
        if(empty($uuid)){
            $uuid = (string)Str::uuid();
            $client = new Client([
                'base_uri' => config('api_list.api_base_url')
            ]);
            $response = $client->post("register_user_id", [
                'multipart' => [
                    [
                        'name' => 'user_id',
                        'contents' => $uuid
                    ]
                ]
            ]);
            if(200 != $response->getStatusCode()){
                \Session::flash('msg_error', 'ERR-001 IDの登録に失敗しました。');
                return false;
            }
            $result = json_decode($response->getBody()->getContents());
            if(! $result->ok){
                \Session::flash('msg_error', 'ERR-001 IDの登録に失敗しました。');
                return false;
            }
            session()->put('uuid', $uuid);
        }
        return $uuid;
    }

    function session_clear()
    {
        session()->forget('talk_list');
        session()->forget('talkers');
        session()->forget('fixed_positions');
        session()->forget('template_id');
        session()->forget('movie_info');
        session()->forget('screenshot_info');
        session()->forget('mode');
        session()->forget('color');
        session()->forget('sc_mode');
        session()->forget('cl_mode');
        session()->forget('title');
        session()->forget('img_path');
        session()->forget('img_url');
        session()->forget('speaker_list');
        session()->forget('initial');
    }
}
