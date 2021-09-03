<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Traits\CommonTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MovieController extends Controller
{
    use CommonTrait;

    public function index(Request $request)
    {
        $movie_info = session()->get('movie_info');
        // $sc_info = session()->get('screenshot_info');
        $mode = session()->get('mode');
        $color = session()->get('color');
        $sc_modes = session()->get('sc_mode');
        $cl_modes = session()->get('cl_mode');
        $title = session()->get('title');

        if(empty($movie_info) || empty($sc_modes) || empty($cl_modes)){
            \Session::flash('msg_error', "MV-001<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
            return redirect()->to('/');
        }
        $movie_info = json_decode($movie_info);
        if(empty($movie_info->movie_url) || empty($movie_info->message)){
            \Session::flash('msg_error', "MV-002<br>動画情報の取得に失敗しました。");
            return redirect()->to('/');
        }
        // $sc_info = json_decode($sc_info);
        // if(empty($sc_info->screen_shot_url) || empty($sc_info->message)){
        //     \Session::flash('msg_error', "MV-003<br>画像情報の取得に失敗しました。");
        //     return redirect()->to('/');
        // }
        $sc_modes = json_decode($sc_modes);
        $cl_modes = json_decode($cl_modes);

        if(empty($mode)){
            $mode = current($sc_modes->responses);
            session()->put('mode', json_encode($mode));
        }
        else{
            $mode = json_decode($mode);
        }
        if(empty($color)){
            $color = current($cl_modes->responses);
            session()->put('color', json_encode($color));
        }
        else{
            $color = json_decode($color);
        }

        return view('movie/index', [
                'mode' => $mode,
                'color' => $color,
                'movie_info' => $movie_info,
                // 'screenshot_info' => $sc_info,
                'img_path' => session()->get('img_path'),
                'title' => json_decode($title)
            ]
        );
    }

    public function create(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "mode_id" => "not_regex:/[^0-9]/",
            "color_id" => "not_regex:/[^0-9]/",
            "title" => "max:15"
        ]);
        if ($validator->fails()) {
            $this->error_message($request
                , "MV-004<br>画像作成のパラメータに不備があります。");
            return false;
        }

        $talk_list = session()->get('talk_list');
        if (empty($talk_list)) {
            $this->error_message($request
                , "MV-005<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
            return false;
        }
        $talk_list = json_decode($talk_list);
        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);
        $uuid = session()->get('uuid');
        $mode_id = $request->input('mode_id');
        $color_id = $request->input('color_id');
        $title = $request->input('title');
        $img_path = $request->input('img_path');

        // スクショモード取得
        $response = $client->get("get_screen_shot_modes",[
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if (200 != $response->getStatusCode()) {
            throw new HttpException(404, 'SM-001 データの取得に失敗しました。');
            /*
            $this->error_message($request
                , "SM-001<br>動画の作成に失敗しました。");
            */
            return false;
        }
        $sc_modes = json_decode($response->getBody()->getContents());
        if (empty($sc_modes->responses)) {
            throw new HttpException(404, 'SM-002 データの取得に失敗しました。');
            /*
            $this->error_message($request
                , "SM-002<br>動画の作成に失敗しました。");
            */
            return false;
        }

        // カラーモード取得
        $response = $client->get("get_screen_shot_colors", [
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if (200 != $response->getStatusCode()) {
            $this->error_message($request
                , "CM-001<br>動画の作成に失敗しました。");
            return false;
        }
        $cl_modes = json_decode($response->getBody()->getContents());
        if (empty($cl_modes->responses)) {
            $this->error_message($request
                , "CM-002<br>動画の作成に失敗しました。");
            return false;
        }

        if (empty($mode_id)) {
            $mode_id = current($sc_modes->responses)->mode_id;
        }
        if (empty($color_id)) {
            // $color_id = current($cl_modes->responses)->color_id;
            $color_id = 99;
        }

        $mode = current($sc_modes);
        foreach ($sc_modes->responses as $mv) {
            if ($mode_id == $mv->mode_id) {
                $mode = $mv;
                break;
            }
        }
        if (99 != $color_id){
            $color = current($cl_modes);
            foreach ($cl_modes->responses as $cv) {
                if ($color_id == $cv->color_id) {
                    $color = $cv;
                    break;
                }
            }
        }else{
            $color = new \stdClass();
            $color->color_code = "#D3D3D3";
            $color->color_id = 99;
            $color->name = "写真を選択";
        }

        // 共通パラメータ
        if($color_id != 99){
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
                    'name' => 'mode_id',
                    'contents' => (int)$mode_id
                ],
                [
                    'name' => 'color_id',
                    'contents' => (int)$color_id
                ],
                [
                    'name' => 'title',
                    'contents' => $title
                ],
            ];
        }
        else {
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
                    'name' => 'mode_id',
                    'contents' => (int)$mode_id
                ],
                [
                    'name' => 'color_id',
                    'contents' => 99
                ],
                [
                    'name' => 'title',
                    'contents' => $title
                ],
            ];
        }

        
        // 画像アップロードか否か
        if(!empty($request->file('upload_photo')) && 99 == $color_id){
            $movie_info = session()->get('movie_info');
            // $screenshot_info = session()->get('screenshot_info');
            if(empty($movie_info)){
                $this->error_message($request
                    , "MV-001<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
                return false;
            }
            $image = \Image::make($request->file('upload_photo'));
            $image->orientate();
            $image->resize(
                ($image->width() > $image->height()) ? 1024 : null,
                ($image->width() > $image->height()) ? null : 1024,
                function ($constraint) {
                    $constraint->aspectRatio();
                }
            )->stream('jpeg', 50);
            $multipart = array_merge(
                $multipart,
                [[
                    'name' => 'img_content',
                    'contents' => $image,
                    'filename' => 'photo_'.$uuid.date('YmdHis').'.png'
                ]]);
        }
        else {
            if($mode_id == 1 && 99 == $color_id && !empty($img_path)){
                $multipart = array_merge(
                    $multipart,
                    [[
                        'name' => 'img_path',
                        'contents' => $img_path,
                    ]]);
            }
        }

        $response = $client->post("create_media_files", [
            'multipart' => $multipart
        ]);
        if(200 != $response->getStatusCode()){
            throw new HttpException(404, 'MV-011 データの取得に失敗しました。');
            /*
            $this->error_message($request
                , "MV-011<br>動画の作成に失敗しました。");
            */
            return false;
        }
        $result = json_decode($response->getBody()->getContents());
        if(empty($result->movie_url)){
            throw new HttpException(404, 'MV-012 データの取得に失敗しました。');
            //$this->error_message($request, "MV-012<br>動画の作成に失敗しました。");
            return false;
        }
        // 動画
        $movie_info = new \stdClass();
        $movie_info->movie_url = $result->movie_url;
        $movie_info->message = $result->message;
        // SS
        // $screenshot_info = new \stdClass();
        // $screenshot_info->screen_shot_url = $result->screen_shot_url;
        // $screenshot_info->screen_shot_height = $result->screen_shot_height;
        // $screenshot_info->screen_shot_width = $result->screen_shot_width;
        // $screenshot_info->message = $result->message;
        if(!empty($result->img_url)) {
            session()->put('img_path', $result->img_url);
        }
        session()->put('movie_info', json_encode($movie_info));
        // session()->put('screenshot_info', json_encode($screenshot_info));
        session()->put('sc_mode', json_encode($sc_modes));
        session()->put('cl_mode', json_encode($cl_modes));
        session()->put('mode', json_encode($mode));
        session()->put('color', json_encode($color));
        session()->put('title', !empty($title) ? json_encode($title) : '');
        if($request->ajax()){
            return response()->json([]);
        }
        return redirect()->to('/movie');
    }

    public function modeList(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            "mode_id" => "required|not_regex:/[^0-9]/",
            "color_id" => "required|not_regex:/[^0-9]/",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => "MD-001<br>テーマ取得のパラメータに不備があります。"]);
        }
        $select_mode_id = $request->input('mode_id');
        $select_color_id = $request->input('color_id');

        $client = new Client([
            'base_uri' => config('api_list.api_base_url')
        ]);

        // スクショモード取得
        $response = $client->get("get_screen_shot_modes", [
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if (200 != $response->getStatusCode()) {
            throw new HttpException(404, 'SM-001 データの取得に失敗しました。');
            //return response()->json(['errors' => "SM-001<br>動画の作成に失敗しました。"]);
        }
        $sc_modes = json_decode($response->getBody()->getContents());
        if (empty($sc_modes->responses)) {
            throw new HttpException(404, 'SM-002 データの取得に失敗しました。');
            //return response()->json(['errors' => "SM-002<br>動画の作成に失敗しました。"]);
        }

        // カラーモード取得
        $response = $client->get("get_screen_shot_colors", [
            "connect_timeout" => self::API_TIMEOUT,
            "timeout" => self::API_TIMEOUT
        ]);
        if (200 != $response->getStatusCode()) {
            throw new HttpException(404, 'CM-001 データの取得に失敗しました。');
            //return response()->json(['errors' => "CM-001<br>動画の作成に失敗しました。"]);
        }
        $cl_modes = json_decode($response->getBody()->getContents());
        if (empty($cl_modes->responses)) {
            throw new HttpException(404, 'CM-002 データの取得に失敗しました。');
            //return response()->json(['errors' => "CM-002<br>動画の作成に失敗しました。"]);
        }
        $obj = new \stdClass();
        $obj->color_code = "#e6e6e6";
        $obj->color_id = 99;
        $obj->name = "写真を選択";
        array_unshift($cl_modes->responses, $obj);

        $view = view('common.mode_list',
            [
                'mode_list' => $sc_modes->responses,
                'color_list' => $cl_modes->responses,
                'select_mode_id' => $select_mode_id,
                'select_color_id' => $select_color_id
            ])->render();
        return response()->json(['result' => $view]);
    }

    public function reset() {
        // 初期化
        $this->session_clear();
        return response()->json(['result' => true]);
    }

    public function title() {
        return json_encode(['result' => view('movie/title')->render()]);
    }

    public function downloadMovie(Request $request)
    {
        $movie_info = session()->get('movie_info');
        if(empty($movie_info)){
            \Session::flash('msg_error', "MV-006<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
            return redirect()->to('/');
        }
        $movie_info = json_decode($movie_info);
        $header = get_headers($movie_info->movie_url, 1);
        $uuid = session()->get('uuid');
        $fileName = $uuid.".mp4";
        $headers = [
            'Content-type' => 'video/mp4',
            'Content-Length: '.$header['Content-Length'],
            'Content-Disposition' => 'attachment; filename="'.$fileName .'"'
        ];
        return response()->make(file_get_contents($movie_info->movie_url), 200, $headers);
    }

    // public function downloadScreenshot(Request $request)
    // {
    //     $screenshot_info = session()->get('screenshot_info');
    //     if(empty($screenshot_info)){
    //         \Session::flash('msg_error', "SC-004<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
    //         return redirect()->to('/');
    //     }
    //     $screenshot_info = json_decode($screenshot_info);
    //     $header = get_headers($screenshot_info->screen_shot_url, 1);
    //     $uuid = session()->get('uuid');
    //     $fileName = $uuid.".png";
    //     $headers = [
    //         'Content-type' => 'image/png',
    //         'Content-Length: '.$header['Content-Length'],
    //         'Content-Disposition' => 'attachment; filename="'.$fileName .'"'
    //     ];
    //     return response()->make(file_get_contents($screenshot_info->screen_shot_url), 200, $headers);
    // }

    public function sendTwitter(Request $request)
    {
        $movie_info = session()->get('movie_info');
        $twitter_message = $request->input('content');
        $send_type = $request->input('send_type');
        if(empty($movie_info) || empty($send_type)){
            \Session::flash('msg_error', "MV-007<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
            return redirect()->to('/');
        }
        if(!empty($twitter_message)){
            session()->put('twitter_message', $twitter_message);
        }
        session()->put('send_type', $send_type);

        $api_key = config('twitter.twitter_api_key');
        $api_secret = config('twitter.twitter_api_secret');

        $access_token = session()->get('access_token');
        if(!empty($access_token)){
            $access_token = json_decode($access_token);
            $this->upload($api_key, $api_secret, $access_token);
            return redirect()->to('/movie');
        }
        $this->uploadTwitter($request, false);

        $protocol = "http://";
        if(config('app.env') === 'production'){
            $protocol = "https://";
        }
        $callback_url = $protocol . $_SERVER["HTTP_HOST"]  . "/movie/upload_twitter";
        $connection = new TwitterOAuth($api_key, $api_secret);
        $params = [
            "oauth_callback" => $callback_url,
            "x_auth_access_type" => "write"
        ];
        $request_token = $connection->oauth('oauth/request_token', $params);
        session()->put('oauth_token_secret', $request_token["oauth_token_secret"]);
        return redirect("https://api.twitter.com/oauth/authorize?oauth_token=" . $request_token["oauth_token"]);
    }

    public function uploadTwitter(Request $request, $redirect=true)
    {
        $send_type = session()->get('send_type');
        if(empty($send_type)){
            \Session::flash('msg_error', "MV-007<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
            return redirect()->to('/');
        }
        if('movie' == $send_type) {
            $movie_info = session()->get('movie_info');
            if (empty($movie_info)) {
                \Session::flash('msg_error', "MV-007<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
                return redirect()->to('/');
            }
        }
        else{
            // $screenshot_info = session()->get('screenshot_info');
            // if (empty($screenshot_info)) {
            //     \Session::flash('msg_error', "MV-007<br>一定時間が経過した為、有効期限が切れています。<br>始めからやり直してください。");
            //     return redirect()->to('/');
            // }
        }
        $denied= $request->input('denied');
        if(!empty($denied)){
            \Session::flash('msg_info', "投稿をキャンセルしました。");
            return redirect()->to('/movie');
        }

        $api_key = config('twitter.twitter_api_key');
        $api_secret = config('twitter.twitter_api_secret');

        $oauth_token= $request->input('oauth_token');
        if (!empty($oauth_token)) {
            $oauth_secret = session()->get('oauth_token_secret');
            $twitter = new TwitterOAuth($api_key, $api_secret, $oauth_token, $oauth_secret);
            $params = [
                'oauth_verifier' => $request->input("oauth_verifier"),
            ];
            $access_token = $twitter->oauth('oauth/access_token', $params);
            $access_token = json_encode($access_token);
            session()->put('access_token', $access_token);
            $this->upload($api_key, $api_secret, json_decode($access_token));
            return redirect()->to('/movie');
        }
        if($redirect){
            \Session::flash('msg_error', "MV-011<br>twitterの認証に失敗しました。<br>時間をおいてから再度お試しください。");
            return redirect()->to('/movie');
        }
        return true;
    }

    public function upload($api_key, $api_secret, $access_token)
    {
        if(empty($api_key) || empty($api_secret) ||
            empty($access_token->oauth_token) || empty($access_token->oauth_token_secret)) {
            \Session::flash('msg_error', "MV-008<br>動画の投稿に失敗しました。");
            return false;
        }
        try {
            $twitter = new TwitterOAuth($api_key, $api_secret, $access_token->oauth_token, $access_token->oauth_token_secret);
            $send_type = session()->get('send_type');
            if('movie' == $send_type){
                $movie_info = session()->get('movie_info');
                $movie_info = json_decode($movie_info);
                $media = array("media" => $movie_info->movie_url, 'media_type' => 'video/mp4');
            }
            else{
                // $screenshot_info = session()->get('screenshot_info');
                // $screenshot_info = json_decode($screenshot_info);
                // $media = array("media" => $screenshot_info->screen_shot_url, 'media_type' => 'image/png');
            }
            $twitter_message = session()->get('twitter_message');
            $media_id = $twitter->upload("media/upload", $media, true);
            $parameters = array(
                'status' => $twitter_message,
                'media_ids' => $media_id->media_id_string,
            );
            $result = $twitter->post('statuses/update', $parameters);
            if(!empty($result->errors)){
                \Session::flash('msg_error', "MV-010<br>動画の投稿に失敗しました。");
                return false;
            }
        }
        catch(\Exception $e){
            \Session::flash('msg_error', "MV-009<br>動画の投稿に失敗しました。");
            return false;
        }

        if('movie' == $send_type) {
            \Session::flash('msg_info', "動画を投稿しました。");
        }
        else{
            \Session::flash('msg_info', "画像を投稿しました。");
        }

        return true;
    }

    private function error_message($request, $message){

        if($request->ajax()){
            return response()->json(['errors' => $message]);
        }
        else{
            \Session::flash('msg_error', $message);
            return redirect()->to('/');
        }
    }
}
