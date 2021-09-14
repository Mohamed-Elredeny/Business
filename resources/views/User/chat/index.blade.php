@extends('layouts.app')

@section('content')
    <div class="col-lg-3">
        <div class="notifications-side-bar py-5 px-3">
            <h3 class="heading-tertiary">Chats</h3>
            <ul class="notification-list list-unstyled my-4">
                <li class="notification-item">
                    <a href="#" class="notification-link">
                        <img src="https://cdn.pixabay.com/photo/2015/09/16/08/55/online-942406_1280.jpg" alt="" class="user-img img-fluid">
                        <span class="notification-text">Mohamed Elredeny</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="one-notification-show">
            <div class="container" style="height:80%">
                <div class="row">
                    <div class="col-sm-12 text-left bg-danger">
                        SAD SAD SAD SAD SAD SAD SAD SAD SAD SAD SAD SAD
                        SAD SAD SAD
                        SAD SAD SAD
                        SAD SAD SAD
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-right bg-success">
                        SAD SAD SAD SAD SAD SAD SAD SAD SAD SAD SAD SAD
                        SAD SAD SAD
                        SAD SAD SAD
                        SAD SAD SAD
                    </div>
                </div>
            </div>

            <div class="row">

                <input class="btn col-sm-12" type="text">
                <input class="col-sm-12" type="button" value="Send">

            </div>
        </div>
    </div>

@endsection
