<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Collective\Html\FormFacade;
use App\Traits\CommonTrait;
use Intervention\Image\Exception\NotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TalkController extends Controller
{
    use CommonTrait;

    private $checkTalkerParam = [
        'talker',
        'listener',
        'create'
    ];

    public function index(Request $request, $talk_id=null)
    {
        session()->forget('msg_error');
        if(!empty(session()->get('talker'))){
            \Session::flash('msg_error', 'ERR-001<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。');
            return redirect()->to('/');
        }
        $uuid = $this->register_uuid();
        if(! $uuid){
            return redirect()->to('/');
        }
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $response = $client->get("get_template_list", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ]
            ]
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-002 データの取得に失敗しました。');
            //\Session::flash('msg_error', 'TALK-002 ジャンルの取得に失敗しました。');
            //return redirect()->to('/');
        }
        $template_list = json_decode($response->getBody()->getContents());
        if(empty($template_list->responses)){
            throw new HttpException(404, 'TALK-003 データの取得に失敗しました。');
            //\Session::flash('msg_error', 'TALK-003 ジャンルの取得に失敗しました。');
            //return redirect()->to('/');
        }
        session()->put('template', json_encode($template_list->responses));

        $talk_list = null;
        if(!empty($talk_id)){
            $response = $client->post("load_talk", [
                'multipart' => [
                    [
                        'name' => 'user_id',
                        'contents' => $uuid
                    ],
                    [
                        'name' => 'media_id',
                        'contents' => $talk_id
                    ],
                ]
            ]);
            if(200 != $response->getStatusCode()){
                throw new HttpException(404, 'TALK-101 データの取得に失敗しました。');
                //\Session::flash('msg_error', 'TALK-101<br>会話の取得に失敗しました。');
                //return redirect()->to('/');
            }
            $result = json_decode($response->getBody()->getContents());
            if(empty($result->responses)){
                throw new HttpException(404, 'TALK-102 データの取得に失敗しました。');
                //\Session::flash('msg_error', 'TALK-102<br>会話の取得に失敗しました。');
                //return redirect()->to('/');
            }
            $talk_list = $result;
            $fixed_positions = [];
            foreach($talk_list->responses as $k => $v){
                if($v->fixed){
                    $fixed_positions[] = $v->turn;
                }
            }
            if(session()->get('template_id'))
            {
                $template_id = session()->get('template_id');
            }
            else{
                $template_id = !empty($result->status->group_id) ? $result->status->group_id : 1;
            }

           
            session()->put('talk_list', json_encode($result));
            session()->put('template_id', $template_id);
            session()->put('fixed_positions', json_encode($fixed_positions));
        }
        else{
            $talk_list = json_decode(session()->get('talk_list'));
            $template_id = session()->get('template_id');
            $fixed_positions = json_decode(session()->get('fixed_positions'));
            if(empty($fixed_positions)){
                $fixed_positions = [];
            }
        }

        if(empty($talk_list)){
            if(session()->get('template_id'))
            {
                $template_id = session()->get('template_id');
            }
            else
            {
                $response = $client->get("get_template_id",[
                    "connect_timeout" => self::API_TIMEOUT,
                    "timeout" => self::API_TIMEOUT
                ]);
                if(200 != $response->getStatusCode()){
                    throw new HttpException(404, 'TALK-301 データの取得に失敗しました。');
                    //\Session::flash('msg_error', 'TALK-301<br>会話の取得に失敗しました。');
                    //return redirect()->to('/');
                }
                $result = json_decode($response->getBody()->getContents());
                if(empty($result->template_id)){
                    throw new HttpException(404, 'TALK-302 データの取得に失敗しました。');
                    //\Session::flash('msg_error', 'TALK-302<br>会話の取得に失敗しました。');
                    //return redirect()->to('/');
                }
                $template_id = $result->template_id;
            }
        }

        $template = array();
        if(!empty($template_id)){
            foreach($template_list->responses as $v){
                if($template_id == $v->template_id){
                    $template = $v;
                    break;
                }
            }
        }
        else{
            $template = current($template_list->responses);
        }

        return view('talk/index', [
                'template' => $template,
                'fixed_positions' => $fixed_positions,
                'init_flg' => !empty($talk_list) ? 0 : 1,
                'talk_list'=> $talk_list
            ]
        );
    }

    public function create(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "ai_ids.*" => "required|not_regex:/[^0-9]/",
            "talker_list.*.ai_id" => "required|not_regex:/[^0-9]/",
            "talker_list.*.img_url" => "required|url",
            "init_flg" => "digits_between:0,1"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-CR-001<br>会話の作成のパラメータに不備があります。"]);
        }
        $ai_ids = $request->input('ai_ids');
        $template = json_decode(session()->get('template'));
        if(empty($template)){
            return response()->json([
                'errors' => "TALK-004<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"
            ]);
        }
        $template_id = $request->input('template_id');
        $template_check_flg = false;
        foreach($template as $v){
            if($template_id == $v->template_id){
                $template_check_flg = true;
                break;
            }
        }
        if(!$template_check_flg){
            return response()->json(['errors' => "TALK-003<br>会話の作成のパラメータに不備があります。"]);
        }
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $uuid = session()->get('uuid');
        $multi = [
            ['name' => 'ai_ids', 'contents' => implode(',',$ai_ids)],
            ['name' => 'user_id', 'contents' => $uuid],
            ['name' => 'template_id', 'contents' => $template_id]
        ];
        if(!empty($process_id)){
            $multi = array_merge([['name' => 'process_id', 'contents' => $process_id]],$multi);
        }
        $response = $client->post("create_talk", [
            'multipart' => $multi,
           
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-005 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-005<br>会話の作成に失敗しました。"]);
        }
        $talk_list = json_decode($response->getBody()->getContents());
        if(empty($talk_list->responses)){
            throw new HttpException(404, 'TALK-006 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-006<br>会話の作成に失敗しました。"]);
        }
        session()->put('talk_list', json_encode($talk_list));
        session()->put('template_id', $template_id);
        session()->forget('fixed_positions');

        $fixed_positions = [];
        foreach($talk_list->responses as $talk){
            if(!empty($talk->fixed)){
                $fixed_positions[] = $talk->turn;
            }
        }
        session()->put('fixed_positions', json_encode($fixed_positions));

        // $view = view('talk.comment', [
        //     'talk_list' => $talk_list, 'fixed_positions' => $fixed_positions
        // ])->render();

        return response()->json(['talk_list' => $talk_list, 'fixed_positions' => $fixed_positions]);
    }

    public function update(Request $request)
    {
        $talk_list = session()->get('talk_list');
        if(empty($talk_list)){
            return response()->json([
                'errors' => "TALK-UD-001<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"
            ]);
        }
        $talk_list = json_decode($talk_list);
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $uuid = session()->get('uuid');
        $multi = [
            ['name' => 'user_id', 'contents' => $uuid],
            ['name' => 'process_id', 'contents' => $talk_list->status->process_id]
        ];
        $response = $client->post("update_talk", [
            'multipart' => $multi
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-005 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-005<br>会話の作成に失敗しました。"]);
        }
        $talk_list = json_decode($response->getBody()->getContents());
        if(empty($talk_list->responses)){
            throw new HttpException(404, 'TALK-006 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-006<br>会話の作成に失敗しました。"]);
        }
        session()->put('talk_list', json_encode($talk_list));
        $fixed_positions = session()->get('fixed_positions');
        // $view = view('talk.comment', [
        //     'talk_list' => $talk_list, 'fixed_positions' => json_decode($fixed_positions)
        // ])->render();
        return response()->json(['talk_list' => $talk_list]);
    }

    public function alternatives(Request $request)
    {
        set_time_limit(60);
        $validator = \Validator::make($request->all(), [
            "position" => "required|not_regex:/[^0-9]/"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-009<br>セリフ取得のパラメータに不備があります。"]);
        }
        $talk_list = session()->get('talk_list');
        if(empty($talk_list)){
            return response()->json([
                'errors' => "TALK-010<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"
            ]);
        }
        $talk_list = json_decode($talk_list);
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $uuid = session()->get('uuid');
        $position = (int)$request->input('position');
        $response = $client->post("get_alternatives", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ],
                [
                    'name' => 'turn',
                    'contents' => $position
                ],
                [
                    'name' => 'process_id',
                    'contents' => $talk_list->status->process_id
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::ALT_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-011 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-011<br>セリフの取得に失敗しました。"]);
        }
        $alternative = json_decode($response->getBody()->getContents());
        if(empty($alternative->sub_process_id)){
            throw new HttpException(404, 'TALK-012 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-012<br>セリフの取得に失敗しました。"]);
        }
        $response = $client->post("get_alternatives_return", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ],
                [
                    'name' => 'sub_process_id',
                    'contents' => $alternative->sub_process_id
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::ALT_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-013 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-013<br>セリフの取得に失敗しました。"]);
        }
        $alternativeReturn = json_decode($response->getBody()->getContents());
        if(empty($alternativeReturn->responses)){
            throw new HttpException(404, 'TALK-014 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-014<br>セリフの取得に失敗しました。"]);
        }
        $img_path = '';
        if(!empty($talk_list->responses[$position]->ai_img_url)){
            $img_path = $talk_list->responses[$position]->ai_img_url;
        }
        $std = new \stdClass();
        $std->remark_id = null;
        $std->click = 0;
        $std->img = $img_path;
        for($i = 0; $i < $alternativeReturn->status->max_num; $i++){
            if(!empty($alternativeReturn->responses[$i])){
                $alternativeReturn->responses[$i]->img = $img_path;
                $alternativeReturn->responses[$i]->click = 1;
            }
            else {
                $alternativeReturn->responses[$i] = $std;
            }
        }
        $fixed_positions = session()->get('fixed_positions');
        $view = view('talk/talk_change', [
            'alternative' => $alternativeReturn,
            'talk_list' => $talk_list,
            'position' => $position,
            'spid' => $alternative->sub_process_id,
            'is_reload' => (int)$alternative->is_reload,
            'fixed_positions' => json_decode($fixed_positions)
        ])->render();
        return response()->json([
            'result' => $view,
            'sub_process_id' => $alternative->sub_process_id,
            'status' => $alternativeReturn->status->done
        ]);
    }

    public function alternativesReturn(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "sub_process_id" => "required|not_regex:/[^0-9]/",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-015<br>セリフ取得のパラメータに不備があります。"]);
        }
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $sub_process_id = (int)$request->input('sub_process_id');
        $uuid = session()->get('uuid');
        $response = $client->post("get_alternatives_return", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ],
                [
                    'name' => 'sub_process_id',
                    'contents' => $sub_process_id
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::ALT_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-017 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-017<br>セリフの取得に失敗しました。"]);
        }
        $alternativeReturn = json_decode($response->getBody()->getContents());
        if(empty($alternativeReturn->responses)){
            throw new HttpException(404, 'TALK-018 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-018<br>セリフの取得に失敗しました。"]);
        }
        return response()->json([
            'result' => $alternativeReturn,
            'status' => $alternativeReturn->status->done]);
    }

    public function candidate(Request $request)
    {
        set_time_limit(60);
        $validator = \Validator::make($request->all(), [
            "position" => "required|not_regex:/[^0-9]/"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-019<br>セリフ取得のパラメータに不備があります。"]);
        }
        $talk_list = session()->get('talk_list');
        $uuid = session()->get('uuid');
        if(empty($talk_list) || empty($uuid)){
            return response()->json([
                'errors' => "TALK-020<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"
            ]);
        }
        $talk_list = json_decode($talk_list);
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $position = (int)$request->input('position');
        $response = $client->post("get_alternatives", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ],
                [
                    'name' => 'turn',
                    'contents' => $position
                ],
                [
                    'name' => 'process_id',
                    'contents' => $talk_list->status->process_id
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-021 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-021<br>セリフの取得に失敗しました。"]);
        }
        $alternative = json_decode($response->getBody()->getContents());
        if(empty($alternative->sub_process_id)){
            throw new HttpException(404, 'TALK-022 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-022<br>セリフの取得に失敗しました。"]);
        }
        $response = $client->post("get_alternatives_return", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ],
                [
                    'name' => 'sub_process_id',
                    'contents' => $alternative->sub_process_id
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-023 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-023<br>セリフの取得に失敗しました。"]);
        }
        $alternativeReturn = json_decode($response->getBody()->getContents());
        if(empty($alternativeReturn->responses)){
            throw new HttpException(404, 'TALK-024 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-024<br>セリフの取得に失敗しました。"]);
        }
        $img_path = '';
        if(!empty($talk_list->responses[$position]->ai_img_url)){
            $img_path = $talk_list->responses[$position]->ai_img_url;
        }
        $std = new \stdClass();
        $std->remark_id = null;
        $std->click = 0;
        $std->img = $img_path;
        for($i = 0; $i < $alternativeReturn->status->max_num; $i++){
            if(!empty($alternativeReturn->responses[$i])){
                $alternativeReturn->responses[$i]->img = $img_path;
                $alternativeReturn->responses[$i]->click = 1;
            }
            else {
                $alternativeReturn->responses[$i] = $std;
            }
        }
        $view = view('talk/change_list', [
            'alternative' => $alternativeReturn,
            'position' => $position,
        ])->render();
        return response()->json([
            'result' => $view,
            'sub_process_id' => $alternative->sub_process_id,
            'status' => $alternativeReturn->status->done,
            'is_reload' => (int)$alternative->is_reload,
        ]);
    }

    public function change(Request $request)
    {
        $talk_list = session()->get('talk_list');
        if(empty($talk_list)){
            return response()->json([
                'errors' => "TALK-CN-001<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"
            ]);
        }
        $validator = \Validator::make($request->all(), [
            "position" => "required|not_regex:/[^0-9]/",
            "img_id" => "nullable|not_regex:/[^0-9]/",
            "img_url" => "nullable|url",
            "remark_id" => "nullable|not_regex:/[^0-9]/",
            "user_content" => "required|boolean",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-CN-002<br>会話の変更のパラメータに不備があります。"]);
        }
        $talk_list= json_decode($talk_list);

        $position = (int)$request->input('position');
        $text = $request->input('text');
        $img_id = $request->input('img_id');
        $user_content = (int)$request->input('user_content');
        $remark_id = (int)$request->input('remark_id');
        $fixed_positions = (string)$request->input('fixed_positions');
        if("" !== $fixed_positions){
            $fixed_positions = explode(',', $fixed_positions);
            if (in_array(false, array_map('is_numeric', $fixed_positions))) {
                return response()->json(['errors' => "TALK-CN-003<br>会話の変更のパラメータに不備があります。"]);
            }
            $fixed_positions = array_map('intval', $fixed_positions);
        }
        else{
            $fixed_positions = [];
        }
        $uuid = session()->get('uuid');

        $multipart = [
            [
                'name' => 'user_id',
                'contents' => $uuid
            ],
            [
                'name' => 'process_id',
                'contents' => (int)$talk_list->status->process_id
            ],
            [
                'name' => 'turn',
                'contents' => $position
            ],
            [
                'name' => 'text',
                'contents' => $text
            ],
            [
                'name' => 'user_content',
                'contents' => $user_content
            ],
        ];
        // if(!empty($fixed_positions)){
        //     $multipart = array_merge($multipart, [[
        //         'name' => 'fixed_positions',
        //         'contents' => implode(',', $fixed_positions)
        //     ]]);
        // }
        if(!is_null($img_id)){
            $multipart = array_merge($multipart, [[
                'name' => 'img_id',
                'contents' => (int)$img_id
            ]]);
        }
        if(!empty($remark_id)){
            $multipart = array_merge($multipart, [[
                'name' => 'remark_id',
                'contents' =>  (int)$remark_id
            ]]);
        }
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $response = $client->post("update_talk", [
            'multipart' => $multipart,
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-CN-004 データの取得に失敗しました。');
            //return response()->json(['errors' => "TALK-CN-004<br>会話の変更に失敗しました。"]);
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

    public function fixed(Request $request)
    {
        $talk_list = session()->get('talk_list');
        $uuid = session()->get('uuid');
        if(empty($talk_list) || empty($uuid)){
            return response()->json(['errors' => "TALK-FX-001<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"]);
        }
        $validator = \Validator::make($request->all(), [
            "position" => "required|not_regex:/[^0-9]/",
            "fixed" => "required|boolean",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-FX-002<br>会話の変更のパラメータに不備があります。"]);
        }
        $position = $request->input('position');
        $fixed = $request->input('fixed');
        $result = $this->change_fixed(json_decode($talk_list), $uuid, $position, $fixed);
        if(! $result['result']){
            return response()->json(['errors' => $result['errors']]);
        }
        $fixed_positions = json_decode(session()->get('fixed_positions'));
        if($fixed){
            if(! is_array($fixed_positions)){
                $fixed_positions[] = $position;
            }
            elseif(!in_array($position, $fixed_positions)){
                $fixed_positions[] = $position;
            }
        }
        else{
            if(is_array($fixed_positions) && false !== $idx = array_search($position, $fixed_positions)){
                unset($fixed_positions[$idx]);
                $fixed_positions = array_values($fixed_positions);
            }
        }
        session()->put('fixed_positions', json_encode($fixed_positions));
        return response()->json(true);
    }

    private function change_fixed($talk_list, $uuid, $position, $fixed=true)
    {
        $multipart = [
            [
                'name' => 'user_id',
                'contents' => $uuid
            ],
            [
                'name' => 'process_id',
                'contents' => (int)$talk_list->status->process_id
            ],
            [
                'name' => 'turn',
                'contents' => $position
            ],
            [
                'name' => 'fixed',
                'contents' => $fixed
            ]
        ];
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $response = $client->post("change_fixedness", [
            'multipart' => $multipart,
      
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'TALK-CF-001 データの取得に失敗しました。');
            /*
            return [
                'result' => false,
                'errors' => "TALK-CF-001<br>会話の変更に失敗しました。"
            ];
            */
        }
        $result = json_decode($response->getBody()->getContents());
        if(empty($result->ok)){
            throw new HttpException(404, 'TALK-CF-002 データの取得に失敗しました。');
            /*
            return [
                'result' => false,
                'errors' => "TALK-CF-002<br>会話の変更に失敗しました。"
            ];
            */
        }
        return ['result' => true];
    }

    public function templateList(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "template_id" => "required|not_regex:/[^0-9]/"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "TALK-009<br>セリフ取得のパラメータに不備があります。"]);
        }
        $template_id = $request->input('template_id');
        $template = session()->get('template');
        if(empty($template)){
            return response()->json([
                'errors' => "TALK-019<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。"
            ]);
        }
        $view = view('common.template_list',
            [
                'template_list' => json_decode($template),
                'template_id' => $template_id
            ])->render();
        return response()->json(['result' => $view]);
    }

    public function renew(Request $request)
    {
        $template_id = session()->get('template_id');
        $this->session_clear();
        session()->put('template_id', $template_id);
        return response()->json(['result' => "success",'template_id'=> $template_id]);
    }
}
