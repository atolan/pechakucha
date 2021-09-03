<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        // レスポンスをJSON形式に変更
        if ($request->is('ajax/*') || $request->is('api/*') || $request->ajax()) {
            $status = 400;
            if ($this->isHttpException($exception)) {
                $status = $exception->getStatusCode();
            }
            if(strpos($exception->getMessage(), 'Connection refused')){
                $status = 404;
            }
            return response()->json([
                'status' => $status,
                'errors' => $exception->getMessage()
            ], $status);
        }
        return parent::render($request, $exception);
    }

    /**
     * オリジナルデザインのエラー画面をレンダリングする
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException $e
     * @return \Illuminate\Http\Response
     */
    protected function renderHttpException(HttpExceptionInterface $e)
    {
        $status = $e->getStatusCode();
        $error = [
            // VIEWに与える変数
            'title'       => 'ERROR',
            'exception'   => $e,
            'message'     => $e->getMessage(),
            'status_code' => $status,
        ];
        if(strpos($e->getMessage(), 'Connection refused')){
            $error['status_code'] = 404;
        }
        return response()->view("errors.common",
            $error,
            $status, // レスポンス自体のステータスコード
            $e->getHeaders()
        );
    }
}
