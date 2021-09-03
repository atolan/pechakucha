
@extends('errors.minimal')

@section('title', 'PechaKucha')

@if(500 == $status_code || 400 == $status_code)
    @section('message')
        ただいまアクセスが集中しております。<br/>時間をおいてお試しください。
    @endsection
@else
    @section('message')
        ただいまメンテナンス中です。<br/>時間をおいてお試しください。
    @endsection
@endif
