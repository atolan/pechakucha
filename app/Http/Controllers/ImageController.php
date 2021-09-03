<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ImageController extends Controller
{
    public function companion(Request $request)
    {
        $mode = $request->input('mode');
        if('recreate' !== $mode) {
            $validator = \Validator::make($request->all(), [
                "id" => "required|not_regex:/[^0-9]/",
                "img_url" => "required|url",
                "template_id" => "not_regex:/[^0-9]/",
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => "IMG-009<br>話者の取得のパラメータに不備があります。"]);
            }
            $ai_id1 = (int)$request->input('id');
            $img_url = $request->input('img_url');
        }
        else{
            $initial = json_decode(session()->get('initial'));
            if(empty($initial)){
                return response()->json(
                    ['errors' => "IMG-013<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"]
                );
            }
            $ai_id1 = $initial->ai_id;
            $img_url = $initial->img_url;
            $res['mode'] = 'recreate';
        }
        $template_id = $request->input('template_id');
        $templates = json_decode(session()->get('template'));
        if(empty($templates)){
            return response()->json(['errors' => "IMG-012<br>テンプレートの取得に失敗しました。"]);
        }
        $template_id = empty($template_id) ? 1 : $template_id;
        $template = $templates[0];
        foreach($templates as $tmp){
            if($tmp->template_id == $template_id){
                $template = $tmp;
                break;
            }
        }
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $response = $client->post("get_companion", [
            'multipart' => [
                [
                    'name' => 'ai_id1',
                    'contents' => $ai_id1
                ],
                [
                    'name' => 'template_id',
                    'contents' => $template_id
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'IMG-010 データの取得に失敗しました。');
            //return response()->json(['errors' => "IMG-010<br>話者の取得に失敗しました。"]);
        }
        $result = json_decode($response->getBody()->getContents());
        if(empty($result->ai_id2) || empty($result->img_url)){
            throw new HttpException(404, 'IMG-011 データの取得に失敗しました。');
            //return response()->json(['errors' => "IMG-011<br>話者の取得に失敗しました。"]);
        }

        $res['ai_ids'] = [$ai_id1, $result->ai_id2];
        $res['talker_list'] = [
            ['ai_id' => $ai_id1, 'img_url' => $img_url],
            ['ai_id' => $result->ai_id2, 'img_url' => $result->img_url],
        ];
        $res['template'] = $template;

        if(empty(session()->get('initial'))) {
            session()->put('initial', json_encode($res['talker_list'][0]));
        }
        return response()->json(['result' => $res]);
    }

    public function registerSpeakerImage(Request $request)
    {
        // バリデーションルール
        $validator = \Validator::make($request->all(), [
            'image' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "IMG-001<br>画像の登録のパラメータに不備があります。"]);
        }
        $value = $request->input('image');
        $image = \Image::make($value);
        $image->orientate();
        $image->resize(
            ($image->width() > $image->height()) ? 256 : null,
            ($image->width() > $image->height()) ? null : 256,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        )->stream('jpeg', 50);
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $uuid = session()->get('uuid');
        $response = $client->post("upload_new_speaker_img", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ],
                [
                    'name' => 'img_content',
                    'contents' => $image,
                    'filename' => 'speaker_'.$uuid.date('YmdHis').'.png'
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'IMG-002 データの取得に失敗しました。');
            //return response()->json(['errors' => "IMG-002<br>画像の登録に失敗しました。"]);
        }
        $result = json_decode($response->getBody()->getContents());
        if(empty($result->ai_id) || empty($result->img_url)){
            throw new HttpException(404, 'IMG-003 データの取得に失敗しました。');
            //return response()->json(['errors' => "IMG-003<br>画像の登録に失敗しました。"]);
        }
        return response()->json(['result' => $result]);
    }

    public function registerTalkImage(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'file' => 'required|file|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "IMG-004<br>画像の登録のパラメータに不備があります。"]);
        }
        $image = \Image::make($request->file('file'));
        $image->orientate();
        $image->resize(
            ($image->width() > $image->height()) ? 256 : null,
            ($image->width() > $image->height()) ? null : 256,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        )->stream('jpeg', 50);

        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $uuid = session()->get('uuid');
        $response = $client->post("upload_new_img", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ],
                [
                    'name' => 'img_content',
                    'contents' => $image,
                    'filename' => 'talk_'.$uuid.date('YmdHis').'.jpeg'
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'IMG-005 データの登録に失敗しました。');
            //return response()->json(['errors' => "IMG-005<br>画像の登録に失敗しました。"]);
        }
        $result = json_decode($response->getBody()->getContents());
        if(empty($result->img_url)){
            throw new HttpException(404, 'IMG-006 データの登録に失敗しました。');
            //return response()->json(['errors' => "IMG-006<br>画像の登録に失敗しました。"]);
        }
        return response()->json(['result' => $result]);
    }

    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    public function imageList()
    {
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $response = $client->request('GET', "get_img_list", [
            'multipart' => []
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'IMG-007 データの取得に失敗しました。');
            //return response()->json(['errors' => "IMG-007<br>画像リストの取得に失敗しました。"]);
        }
        $result = json_decode($response->getBody()->getContents());
        if(empty($result->responses)){
            throw new HttpException(404, 'IMG-008 データの取得に失敗しました。');
            //return response()->json(['errors' => "IMG-008<br>画像リストの取得に失敗しました。"]);
        }
        $img_camera = new \stdClass();
        $img_camera->img_id = 0;
        $img_camera->img_url = '/img/icon_camera.png';
        array_unshift($result->responses, $img_camera);
        session()->put('image_list', $result->responses);
        $view = view('common.image_list', ['image_list' => $result->responses])->render();
        return response()->json(['result' => $view]);
    }

    public function changeSpeaker(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "position*" => "required|not_regex:/[^0-9]/",
            "ai_id" => "required|not_regex:/[^0-9]/",
            "img_url" => "required|url",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-003<br>会話の作成のパラメータに不備があります。"]);
        }
        $ai_id = (int)$request->input('ai_id');
        $position = (int)$request->input('position');
        $img_url = $request->input('img_url');

        $uuid = session()->get('uuid');
        $talk_list = session()->get('talk_list');
        if(empty($talk_list) || empty($uuid)){
            return response()->json([
                'errors' => "TALK-010<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"
            ]);
        }
        $talk_list = json_decode($talk_list);

        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $multi = [
            ['name' => 'ai_id', 'contents' => $ai_id],
            ['name' => 'user_id', 'contents' => $uuid],
            ['name' => 'turn', 'contents' => $position],
            ['name' => 'process_id', 'contents' => $talk_list->status->process_id],
        ];

        $response = $client->post("update_talk", [
            'multipart' => $multi
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'ALK-005 データの作成に失敗しました。');
            //return response()->json(['errors' => "TALK-005<br>会話の作成に失敗しました。"]);
        }


        $talk_list = json_decode($response->getBody()->getContents());
        if(empty($talk_list->status)){
            throw new HttpException(404, 'TALK-CN-005 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-CN-005<br>会話の変更に失敗しました。"]);
        }

        $fixed_positions = [];
        foreach($talk_list->responses as $talk){
            if(!empty($talk->fixed)){
                $fixed_positions[] = $talk->turn;
            }
        }

        session()->forget('talk_list');
        $talk_list = json_encode($talk_list);
        session()->put('talk_list', $talk_list);
        session()->put('fixed_positions', json_encode($fixed_positions));
        session()->save();
        return response()->json(['talk_list' => json_decode($talk_list), 'fixed_positions' => $fixed_positions]);
    }
}
