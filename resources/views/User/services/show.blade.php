@extends('layouts.app')

@section('content')

    <div class="modal" id="success-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 22vh">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span></span>
                    <h5 class="modal-title" id="exampleModalLabel">Success Modal</h5>
                    <button type="button" id="success-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="success-modal-message" >
                    post created successfully
                </div>
            </div>
        </div>
    </div>

    <section id="ez-body__center-content" class="col-lg-10 mt-3">
        <div class="search-bar d-flex justify-content-center">
            <input class="w-75" type="text" placeholder="Search" />
            <a data-toggle="modal" data-target="#add-service-modal" class="btn btn-warning text-white">Add service</a>
        </div>
        <div class="modal" id="success-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" style="margin-top: 22vh">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <span></span>
                        <h5 class="modal-title" id="exampleModalLabel">Success Modal</h5>
                        <button type="button" id="success-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="success-modal-message" >
                        post created successfully
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="add-service-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" style="margin-top: 22vh">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <span></span>
                        <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                        <button type="button" id="add-post-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12" id="error-message" style="display: none">
                            <div class="alert alert-danger alert-dismissible fade show" id="error-status" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <form action="{{route('services.store')}}" method="POST" class="container" id="add-service-form" enctype="multipart/form-data">

                            @csrf

                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                <label for="cars">Choose A Category:</label>
                                <select id="post-category" name="category_id">
                                    @foreach($categories as $category)
                                        @if(App::getlocale() == 'en')
                                            <option value="{{$category->id}}">{{$category->name_en}}</option>
                                        @else
                                            <option value="{{$category->id}}">{{$category->name_ar}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                <label for="exampleInputEmail1">Target Country:</label>
                                <select name="country">
                                    @foreach($countries as $country)
                                        <option value="{{$country->name}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group d-flex justify-content-center mt-2">
                                    <textarea class="form-control w-75 p-2" name="body"  id="post-text" cols="200" rows="4"
                                              placeholder="Post Description..."></textarea>
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                <label for="exampleInputEmail1">price</label>
                                <input name="price" class="form-control w-75 mt-2" type="number"/>
                            </div>
                            <!-- Post Images -->
                            <div class="form-group d-flex justify-content-center mt-2">
                                <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs"
                                       multiple />
                            </div>
                            <!-- Add Post Btn -->
                            <div class="post-add-btn d-flex justify-content-center mt-4">
                                <button type="button" onclick="addServiceSubmit()" class="btn btn-warning btn-block w-75">
                                    post
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="services-controller m-3 text-center">
            <a class="btn btn-light rounded-5 filter-button @if($category_id == null) ez-active @endif" href="{{route('services')}}"> All </a>
            @foreach($categories as $category)
                    <a class="btn btn-light rounded-5 filter-button @if($category_id == $category->id) ez-active @endif" href="{{route('services',$category->id)}}">
                        @if(App::getlocale() == 'en')
                            {{$category->name_en }}
                        @else
                            {{$category->name_ar }}
                        @endif
                    </a>
            @endforeach
        </div>
        <div class="services-container d-flex flex-wrap mt-3" id="addedservice">
            @if(count($services) > 0)
                @foreach($services as $service)
                    <div class="service card m-2 service-id-{{$service->id}}" data-toggle="modal"
                         data-target="#service-modal-{{$service->id}}" id="service-{{$service->id}}">

                        @if(count($service->media) > 0)
                            @if($service->media[0]->mediaType == 'image')
                                <img src="{{asset('media')}}/{{$service->media[0]->filename}}" width="100%">
                            @else
                                <video class="pt-3" controls>
                                    <source src="{{asset('media')}}/{{$service->media[0]->filename}}" type="video/mp4" width="100%">
                                </video>
                            @endif
                        @else
                            <img src="{{asset('media')}}/services.jpg" width="100%">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{$service->body}}</h5>
                            <p class="card-text">{{$service->price}} $</p>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade"
                        id="service-modal-{{ $service->id }}"
                        tabindex="-1"
                        aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header pb-0">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Service
                                    </h5>
                                    <button
                                        type="button"
                                        class="close"
                                        data-dismiss="modal"
                                        aria-label="Close"
                                    >
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body row">
                                    <div
                                        class="
                                        col-4
                                        p-3
                                        d-flex
                                        flex-column
                                        justify-content-between
                                      "
                                    >
                                        <div class="upper-side">
                                            <p class="text-warning">{{$service->price}}</p>
                                            <h6><b>Product Description</b></h6>
                                            <p class="text-muted">
                                                {{$service->body}}
                                            </p>
                                        </div>
                                        <div class="down-side">
                                            <h6><b>Created By</b></h6>
                                            <div class="d-flex justify-content-between">
                                                <div class="people mt-2 w-100">
                                                    <div class="people-info d-flex">
                                                        @if($service->publisher->personal_image == null)
                                                            <img
                                                                class="profile-figure rounded-circle"
                                                                src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                                alt="User Profile Pic"
                                                            />
                                                        @else
                                                            <img
                                                                class="profile-figure rounded-circle"
                                                                src="{{asset('media')}}/{{$service->publisher->personal_image}}"
                                                                alt="User Profile Pic"
                                                            />
                                                        @endif
                                                                <div
                                                                    class="
                                                                d-flex
                                                              flex-column
                                                              align-items-center
                                                              justify-content-center
                                                              pl-2
                                                            "
                                                                >
                                                            <p><b>{{$service->publisher->name}}</b></p>
                                                        </div>
                                                    </div>
                                                    @if($service->publisher->id != auth()->user()->id)
                                                        <button class="btn btn-warning text-white">
                                                            @if($service->follow)
                                                                Follow
                                                            @else
                                                                following
                                                            @endif
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                                @if($service->publisher->id != auth()->user()->id)
                                                    <button class="btn btn-warning text-white mt-3 w-100">
                                                        Contact The Creator
                                                    </button>
                                                @else
                                                    <button onclick="confirm('{{ __("Are you sure you want to delete this service ?") }}') ? deleteServiceSubmit({{$service->id}}) : ''" class="btn btn-warning text-white mt-3 w-100">
                                                        Delete
                                                    </button>
                                                    <button onclick="$('#service-modal-{{ $service->id }}').modal('hide');$('#edit-service-modal-{{ $service->id }}').modal('show');" class="btn btn-warning text-white mt-3 w-100">
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('services.destroy', $service->id) }}" id="delete-service-form-{{$service->id}}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                    </form>
                                                @endif
                                        </div>
                                    </div>
                                        <div class="col-8 img-container">
                                            @if(count($service->media) > 0)
                                                @if(count($service->media) == 1)
                                                    @if($service->media[0]->mediaType == 'image')
                                                        <img src="{{asset('media')}}/{{$service->media[0]->filename}}" width="100%">
                                                    @else
                                                        <video class="pt-3" controls>
                                                            <source src="{{asset('media')}}/{{$service->media[0]->filename}}" type="video/mp4">
                                                        </video>
                                                    @endif

                                                @else
                                                    <div class="more-media w-50" data-toggle="modal" data-target="#more-media-modal-{{$service->id}}">
                                                        <p>+{{count($service->media) - 1}}</p>
                                                        <div class="overlay"></div>
                                                        @if($service->media[0]->mediaType == 'image')
                                                            <img src="{{asset('media')}}/{{$service->media[0]->filename}}" width="100%">
                                                        @else
                                                            <video class="pt-3" controls>
                                                                <source src="{{asset('media')}}/{{$service->media[0]->filename}}" type="video/mp4">
                                                            </video>
                                                        @endif
                                                    </div>
                                                @endif

                                            @else
                                                <img src="{{asset('media')}}/services.jpg" width="100%">
                                            @endif
                                        </div>
                                    <div class="post-more-media-modal">
                                        <div class="modal fade" id="more-media-modal-{{$service->id}}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog" style="margin-top: 10vh">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <div id="media-carousel-{{$service->id}}" class="carousel slide" data-ride="carousel">
                                                            <div class="carousel-inner">
                                                                @foreach($service->media as $media)
                                                                    <div class="carousel-item @if ($loop->first == true) active @endif">
                                                                        <img src="{{asset('media')}}/{{$media->filename}}" class="d-block w-100" alt="...">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <a class="carousel-control-prev" href="#media-carousel-{{$service->id}}" role="button"
                                                               data-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Previous</span>
                                                            </a>
                                                            <a class="carousel-control-next" href="#media-carousel-{{$service->id}}" role="button"
                                                               data-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Next</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="post-edit-modal">
                        <div class="modal fade" id="edit-service-modal-{{$service->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" style="margin-top: 22vh">
                                <div class="modal-content">
                                    <div class="modal-header d-flex justify-content-between">
                                        <span></span>
                                        <h5 class="modal-title" id="exampleModalLabel">Edit Service</h5>
                                        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-12" id="error-message-{{$service->id}}" style="display: none">
                                            <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$service->id}}" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        </div>
                                        <form action="{{route('services.update',$service->id)}}" id="edit-service-form-{{$service->id}}" method="POST" class="container" enctype="multipart/form-data">

                                            @csrf
                                            @method('put')


                                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                                <label for="cars">Choose A Category:</label>
                                                <select id="post-category" name="category_id">
                                                    @foreach($categories as $category)
                                                        @if(App::getlocale() == 'en')
                                                            <option value="{{$category->id}}" @if($category->id == $service->categoryId) selected @endif>{{$category->name_en}}</option>
                                                        @else
                                                            <option value="{{$category->id}}" @if($category->id == $service->categoryId) selected @endif>{{$category->name_ar}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                                <label for="exampleInputEmail1">Target Country:</label>
                                                <select name="country">
                                                    @foreach($countries as $country)
                                                        <option value="{{$country->name}}" @if($country->id == $service->country_id) selected @endif>{{$country->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Post Desc -->
                                            <div class="form-group d-flex justify-content-center mt-2">
                                              <textarea class="form-control w-75 mt-2" name="body" id="post-text" cols="200" rows="4"
                                                        placeholder="Start Typing..." >{{$service->body}}</textarea>
                                            </div>

                                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                                <label for="exampleInputEmail1">price</label>
                                                <input name="price" value="{{$service->price}}" class="form-control w-75 mt-2" type="number"/>
                                            </div>
                                            <!-- Post Images -->
                                            <div class="post-desc d-flex justify-content-center mt-2">
                                                <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs" accept="image/*"
                                                       multiple />
                                            </div>

                                            @if(count($service->media) > 0)
                                                <p>Media</p>
                                                <div class="imgsContainer d-flex flex-wrap">
                                                @foreach($service->media as $media)
                                                    @if($media->mediaType == 'image')
                                                        <!-- if media img and imgs=1 -->
                                                            <div class="p-3" style="width: 33%;">
                                                                <img src="{{asset('media')}}/{{$media->filename}}" alt="" width="100%">
                                                                <div class="w-100 text-center">
                                                                    <input checked type="checkbox" name="checkedimages[]">
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="p-3" style="width: 33%;">
                                                                <video class="p-1" controls width="100%">
                                                                    <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                                                    Your browser does not support HTML video.
                                                                </video>
                                                                <div class="w-100 text-center">
                                                                    <input checked type="checkbox" value="{{$media->filename}}" name="checkedimages[]">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                        @endif
                                            <!-- Add Post Btn -->
                                            <div class="post-add-btn d-flex justify-content-center mt-4">
                                                <button type="button" onclick="editServiceSubmit({{$service->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                    Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-md-8" id="no-service">
                    <div class="card">
                        <div class="card-body">
                            {{ __('no services found!') }}
                        </div>
                    </div>
                </div>
            @endif
    </section>
@endsection
