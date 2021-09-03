<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\CommonTrait;
use Intervention\Image\Exception\NotFoundException;
use Mockery\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TopController extends Controller
{
    use CommonTrait;

    public function index(Request $request)
    {
        $uuid = $this->register_uuid();
        if(! $uuid){
            return view('top/index', [
                    'popular_images' => []
                ]
            );
        }

        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $response = $client->get("get_top_screen_shots", [
            'multipart' => [
                [
                    'name' => 'user_id',
                    'contents' => $uuid
                ]
            ],
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);

        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'ERR-004 データの取得に失敗しました。');
            //\Session::flash('msg_error', 'ERR-004 人気投稿の取得に失敗しました。');
        }
        $result = json_decode($response->getBody()->getContents());
        if(empty($result->responses)){
            throw new HttpException(404, 'ERR-005 データの取得に失敗しました。');
            //\Session::flash('msg_error', 'ERR-005 人気投稿の取得に失敗しました。');
        }

        $this->session_clear();

        return view('top/index', [
            'popular_images' => $result->responses
            ]
        );
    }

    public function usage(Request $request)
    {
        $this->register_uuid();
        return view('top/usage', []);
    }

    public function speakerList(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "init_flg" => "digits_between:0,1",
            "img_id" => "not_regex:/[^0-9]/",
            "position" => "digits_between:0,5"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "ERR-001<br>画像リストの取得に失敗しました。"]);
        }

        $init_flg = (int)$request->input('init_flg');
        $img_id = (int)$request->input('img_id');
        $position = (int)$request->input('position');

        $result = session()->get('speaker_list');
        if(empty($result)){
            $client = new Client([
                'base_uri' => config('api_list.api_base_url')
            ]);
            $response = $client->get("get_speaker_list",[
                "connect_timeout" => self::API_TIMEOUT,
                "timeout" => self::API_TIMEOUT
            ]);
            if(200 != $response->getStatusCode()){
                throw new HttpException(404, 'ERR-002 データの取得に失敗しました。');
                //return response()->json(['speaker_list' => [], 'errors' => 'ERR-002 : <br>話者リストの取得に失敗しました。']);
            }
            $result = json_decode($response->getBody()->getContents());
            if(empty($result->responses)){
                throw new HttpException(404, 'ERR-003 データの取得に失敗しました。');
                //return response()->json(['speaker_list' => [], 'errors' => 'ERR-003 : <br>話者リストの取得に失敗しました。']);
            }
            $camera = new \stdClass();
            $camera->ai_id = 0;
            $camera->img_url = '/img/icon_camera.png';
            array_unshift($result->responses, $camera);
            session()->put('speaker_list', json_encode($result));
        }
        else{
            $result = json_decode($result);
        }
        $talk_list = json_decode(session()->get('talk_list'));
        if(1 != $init_flg && empty($talk_list)){
            return response()->json(['speaker_list' => [], 'errors' => 'ERR-004 : <br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。']);
        }
        $template = json_decode(session()->get('template'));
        $template_id = session()->get('template_id');
        $templaterender = null;
        if(!empty($template))
        {
            foreach($template as $tem)
            {
                if($tem->template_id==$template_id)
                {
                    $templaterender = $tem;
                }
            }
        }

        $view = view('common.speaker_list', [
            'list' => $result->responses,
            'img_id' => $img_id,
            'init_flg' => $init_flg,
            'talk_list' => $talk_list,
            'position' => $position,
            'template'=> $template_id ? $templaterender : $template[0]
        ])->render();
        return response()->json(['speaker_list' => $view, 'errors' => []]);
    }

    public function speakerCategories(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "category_id" => "not_regex:/[^0-9]/"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "ERR-006<br>カテゴリ取得のパラメータに不備があります。"]);
        }
        $result = json_decode(session()->get('speaker_list'));
        $category_id = (int)$request->input('category_id');
        $view = view('common.speaker_filter', [
            'categories' => $result->categories,
            'category_id' => $category_id
        ])->render();
        return response()->json(['categories' => $view, 'errors' => []]);
    }

    public function speakerFilter(Request $request)
    {
        $category_id = $request->input('category_id');
        $init_flg = $request->input('init_flg');
        $position = $request->input('position');
        $img_id = $request->input('img_id');
        $validator = \Validator::make($request->all(), [
            "category_id" => "not_regex:/[^0-9]/",
            "init_flg" => "digits_between:0,1",
            "img_id" => "not_regex:/[^0-9]/",
            "position" => "digits_between:0,5"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "ERR-007<br>カテゴリ取得のパラメータに不備があります。"]);
        }
        $list = json_decode(session()->get('speaker_list'));
        if(empty($list)){
            return response()->json(['speaker_list' => [], 'errors' => 'ERR-008 : <br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。']);
        }
        if(empty($category_id)){
            $list = $list->responses;
        }
        else{
            $first[] = $list->responses[0];
            unset($list->responses[0]);
            $speakers = [];
            foreach($list->responses as $speaker){
                $speakers[$speaker->category_id][] = $speaker;
            }
            $list = array_merge($first, $speakers[$category_id]);
        }
        $template = json_decode(session()->get('template'));
        $template_id = session()->get('template_id');
        $view = view('common.speakers', [
            'list' => $list,
            'img_id' => $img_id,
            'init_flg' => $init_flg,
            'position' => $position,
            'template' => $template,
            'template_id'=>$template_id
        ])->render();
        return response()->json(['speaker_list' => $view, 'errors' => []]);
    }

    function error($status_code)
    {
        return view('errors/common', ['status_code' => $status_code]);
    }
}
