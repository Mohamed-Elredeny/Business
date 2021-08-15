

@section('post')
@foreach($posts as $post)
    <div class="post-container bg-white mt-3 p-3" id="post-{{$post->id}}">
        <div class="post-owner d-flex align-items-center">
            @if($post->source == "page")
                <div class="owner-img">
                    <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->page->profile_image}}" class="rounded-circle" /></a>
                </div>
            @else
                @if($post->publisher->personal_image)
                    <div class="owner-img">
                        <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->publisher->personal_image}}" class="rounded-circle" /></a>
                    </div>
                @else
                    <div class="owner-img">
                        <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                    </div>
                @endif
            @endif
            <div class="owner-name pl-3">
                @if($post->source == "page")
                    <a href="{{route('profile',$post->publisher->id)}}"><b>
                            {{$post->page->name}}
                        </b></a>
                @else
                    <a href="{{route('profile',$post->publisher->id)}}"><b>
                            @if($post->publisher->official == 1)
                                <i class="fas fa-user-check" style="color: #ffc107"></i>
                            @endif
                            {{$post->publisher->name}}
                        </b></a>
                    @if($post->sponsored)
                        <div style="font-size: small">
                            <span><i class="fas fa-ad"></i></span>
                            sponsored
                        </div>

                    @endif
                @endif

                @if($post->tags != null )
                    <a data-toggle="modal" data-target="#show-post-tags-modal-{{$post->id}}"><b>
                            with
                            @if($post->tagged == true)
                                you and {{count($post->tags_info) - 1}}
                            @else
                                {{count($post->tags_info)}}
                            @endif
                            others
                        </b></a>
                @endif

                @if($post->source == "group")
                    <Span><i class="fas fa-caret-right"></i></Span>
                    {{$post->group->name}}
                @endif

                <span style="display: block">{{date('d/m/Y',strtotime($post->created_at))}}</span>
            </div>
            <!-- Post options -->
            <div class="post-options post-options-{{$post->id}}">
                <ul class="options">
                    @if($post->sponsored == false && $post->type == "post")
                        <li data-toggle="modal" data-target="#advertise-post-modal-{{$post->id}}">Advertise</li>
                    @endif
                    @if(!$post->saved)
                    <!-- ajax -->
                        <li><a id="save-post-{{$post->id}}" onclick="savePostSubmit({{$post->id}})">Save Post</a></li>
                        <form id="save-post-form-{{$post->id}}" action="{{ route('savepost') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="hidden" name="post_id" value="{{$post->id}}">
                            <input type="hidden" id="save-post-flag-{{$post->id}}" name="flag" value="0">
                        </form>
                    @else
                        <li><a id="save-post-{{$post->id}}" onclick="savePostSubmit({{$post->id}})">Saved</a></li>
                        <form id="save-post-form-{{$post->id}}" action="{{ route('savepost') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="hidden" name="post_id" value="{{$post->id}}">
                            <input type="hidden" id="save-post-flag-{{$post->id}}" name="flag" value="1">
                        </form>
                    @endif
                    <li data-toggle="modal" onclick="textAreaChange({{$post->id}})" data-target="#edit-post-modal-{{$post->id}}">Edit</li>
                    <form action="{{ route('posts.destroy', $post->id) }}" id="delete-post-form-{{$post->id}}" method="post">
                    @csrf
                    @method('delete')
                    <!-- ajax-->
                        <li onclick="confirm('{{ __("Are you sure you want to delete this post ?") }}') ? deletePostSubmit({{$post->id}}) : ''">
                            Delete</li>
                    </form>
                    <li data-toggle="modal" data-target="#report-post-modal-{{$post->id}}" class="last-li">Report</li>
                </ul>
            </div>
            <div class="post-option ml-auto pr-3" onclick="toggleOptions({{$post->id}});applySelect2();">
                <i class="fas fa-ellipsis-v"></i>
            </div>
        </div>

        @if($post->tags != null)

            <div class="show-story-views-modal">
                <div class="modal fade" id="show-post-tags-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Post Tags
                                </h5>
                                <button type="button" class="close ml-0" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @foreach($post->tags_info as $tag)
                                    <div class="people-info d-flex align-items-center">
                                        @if($tag->personal_image != null)
                                            <img style="width: 45px; height: 45px" class="profile-figure rounded-circle"
                                                 src="{{asset('media')}}/{{$tag->personal_image}}"
                                                 alt="User Profile Pic">
                                        @else
                                            <img style="width: 45px; height: 45px" class="profile-figure rounded-circle"
                                                 src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                 alt="User Profile Pic">
                                        @endif
                                        <p class="mb-0 ml-3"><b>{{$tag->name}}</b></p>
                                    </div>
                                    @if($loop->last == false)
                                        <hr>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="post-edit-modal">
            <div class="modal fade" id="report-post-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">Report Post</h5>
                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="col-12" id="error-message-{{$post->id}}" style="display: none">
                                <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            <form action="{{route('userreports.store')}}" id="report-post-form-{{$post->id}}" method="POST" class="container" enctype="multipart/form-data">

                            @csrf

                            <!-- Post Desc -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                                          <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                    placeholder="Start Typing..." ></textarea>
                                </div>
                                <!-- Add Post Btn -->
                                <input type="hidden" name="model_id" value="{{$post->id}}">
                                <input type="hidden" name="model_type" value="post">

                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                    <button type="button" onclick="reportPostSubmit({{$post->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                        Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="post-desc mt-3">
            <!-- if lang arabic -->
            @if(App::getlocale() == 'ar')
                <pre style="text-align:right;"><?php echo $post->body ?></pre>
            @else
                <pre style="text-align:left;"><?php echo $post->body ?></pre>
            @endif
            @if(count($post->media) > 0 && $post->type == "post")
                <div class="media">
                @if(count($post->media) == 1)

                    @foreach($post->media as $media)
                        @if($media->mediaType == 'image')
                            <!-- if media img and imgs=1 -->
                                <div class="d-flex" style="width: 100%">
                                    <img src="{{asset('media')}}/{{$media->filename}}" alt="opel car" />
                                </div>
                            @else
                                <video class="p-1" controls>
                                    <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                    Your browser does not support HTML video.
                                </video>
                            @endif
                        @endforeach

                    @else
                        <div class="w-100">
                            <div class="d-flex w-100">
                            @if($post->media[0]->mediaType == 'image')
                                <!-- if media img and imgs=1 -->
                                    <div class="d-flex" style="width: 100%">
                                        <img src="{{asset('media')}}/{{$post->media[0]->filename}}" alt="opel car" />
                                    </div>
                                @else
                                    <video class="p-1" controls>
                                        <source src="{{asset('media')}}/{{$post->media[0]->filename}}" type="video/mp4">
                                        Your browser does not support HTML video.
                                    </video>
                                @endif


                                @if($post->media[1]->mediaType == 'image')
                                <!-- if media img and imgs=1 -->
                                    <div class="p-1 w-100" style="width: 100%">
                                        <img src="{{asset('media')}}/{{$post->media[1]->filename}}" alt="opel car" />
                                    </div>
                                @else
                                    <video class="p-1 w-100" controls>
                                        <source src="{{asset('media')}}/{{$post->media[1]->filename}}" type="video/mp4">
                                        Your browser does not support HTML video.
                                    </video>
                                @endif
                            </div>

                            @if(count($post->media) > 2)
                                <div class="d-flex w-100">
                                @if($post->media[2]->mediaType == 'image')
                                    <!-- if media img and imgs=1 -->
                                        <div class="p-1 w-100" style="width: 100%">
                                            <img src="{{asset('media')}}/{{$post->media[2]->filename}}" alt="opel car" />
                                        </div>
                                    @else
                                        <video class="p-1 w-100" controls>
                                            <source src="{{asset('media')}}/{{$post->media[2]->filename}}" type="video/mp4">
                                            Your browser does not support HTML video.
                                        </video>
                                    @endif

                                    @if(count($post->media) == 4)

                                        @if($post->media[3]->mediaType == 'image')
                                        <!-- if media img and imgs=1 -->
                                            <div class="p-1 w-100" style="width: 100%">
                                                <img src="{{asset('media')}}/{{$post->media[3]->filename}}" alt="opel car" />
                                            </div>
                                        @else
                                            <video class="p-1 w-100" controls>
                                                <source src="{{asset('media')}}/{{$post->media[3]->filename}}" type="video/mp4">
                                                Your browser does not support HTML video.
                                            </video>
                                        @endif
                                    @endif

                                    @if(count($post->media) > 4)
                                        <div class="more-media w-50" data-toggle="modal" data-target="#more-media-modal-{{$post->id}}">
                                            <p>+{{count($post->media) - 3}}</p>
                                            <div class="overlay"></div>

                                        @if($post->media[3]->mediaType == 'image')
                                            <!-- if media img and imgs=1 -->
                                                <div class="p-1 w-100" style="width: 100%">
                                                    <img src="{{asset('media')}}/{{$post->media[3]->filename}}" alt="opel car" />
                                                </div>
                                            @else
                                                <video class="p-1 w-100" controls>
                                                    <source src="{{asset('media')}}/{{$post->media[3]->filename}}" type="video/mp4">
                                                    Your browser does not support HTML video.
                                                </video>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="post-more-media-modal">
                    <div class="modal fade" id="more-media-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin-top: 10vh">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div id="media-carousel-{{$post->id}}" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach($post->media as $media)
                                                <div class="carousel-item @if ($loop->first == true) active @endif">
                                                    <img src="{{asset('media')}}/{{$media->filename}}" class="d-block w-100" alt="...">
                                                </div>
                                            @endforeach
                                        </div>
                                        <a class="carousel-control-prev" href="#media-carousel-{{$post->id}}" role="button"
                                           data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#media-carousel-{{$post->id}}" role="button"
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
            @endif

            @if($post->type == "share")
                <div class="post-container shared-post bg-white p-2">
                    <div class="post-owner d-flex align-items-center">
                        @if($post->shared_post->source == "page")
                            <div class="owner-img">
                                <a style="display: inline" href="{{route('profile',$post->shared_post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->shared_post->page->profile_image}}" class="rounded-circle" /></a>
                            </div>
                        @else
                            @if($post->shared_post->publisher->personal_image)
                                <div class="owner-img">
                                    <a style="display: inline" href="{{route('profile',$post->shared_post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->shared_post->publisher->personal_image}}" class="rounded-circle" /></a>
                                </div>
                            @else
                                <div class="owner-img">
                                    <a style="display: inline" href="{{route('profile',$post->shared_post->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                                </div>
                            @endif
                        @endif
                        <div class="owner-name pl-3">
                            @if($post->shared_post->source == "page")
                                <a href="{{route('profile',$post->shared_post->publisher->id)}}"><b>
                                        {{$post->shared_post->page->name}}
                                    </b></a>
                            @else
                                <a href="{{route('profile',$post->shared_post->publisher->id)}}"><b>
                                        @if($post->shared_post->publisher->official == 1)
                                            <i class="fas fa-user-check" style="color: #ffc107"></i>
                                        @endif
                                        {{$post->shared_post->publisher->name}}
                                    </b></a>
                                @if($post->shared_post->sponsored)
                                    <div style="font-size: small">
                                        <span><i class="fas fa-ad"></i></span>
                                        sponsored
                                    </div>

                                @endif
                            @endif

                            @if($post->shared_post->tags != null )
                                <a data-toggle="modal" data-target="#show-post-tags-modal-{{$post->shared_post->id}}"><b>
                                        with
                                        @if($post->shared_post->tagged == true)
                                            you and {{count($post->shared_post->tags_info) - 1}}
                                        @else
                                            {{count($post->shared_post->tags_info)}}
                                        @endif
                                        others
                                    </b></a>
                            @endif

                            @if($post->shared_post->source == "group")
                                <Span><i class="fas fa-caret-right"></i></Span>
                                {{$post->shared_post->group->name}}
                            @endif

                            <span style="display: block">{{date('d/m/Y',strtotime($post->shared_post->created_at))}}</span>
                        </div>
                    </div>

                    <div class="post-desc mt-3">
                        <!-- if lang arabic -->
                        @if(App::getlocale() == 'ar')
                            <pre style="text-align:right;"><?php echo $post->shared_post->body ?></pre>
                        @else
                            <pre style="text-align:left;"><?php echo $post->shared_post->body ?></pre>
                        @endif
                        @if(count($post->shared_post->media) > 0)
                            <div class="media">
                            @if(count($post->shared_post->media) == 1)

                                @foreach($post->shared_post->media as $media)
                                    @if($media->mediaType == 'image')
                                        <!-- if media img and imgs=1 -->
                                            <div class="d-flex" style="width: 100%">
                                                <img src="{{asset('media')}}/{{$media->filename}}" alt="opel car" />
                                            </div>
                                        @else
                                            <video class="p-1" controls>
                                                <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                                Your browser does not support HTML video.
                                            </video>
                                        @endif
                                    @endforeach

                                @else
                                    <div class="w-100">
                                        <div class="d-flex w-100">
                                        @if($post->shared_post->media[0]->mediaType == 'image')
                                            <!-- if media img and imgs=1 -->
                                                <div class="d-flex" style="width: 100%">
                                                    <img src="{{asset('media')}}/{{$post->shared_post->media[0]->filename}}" alt="opel car" />
                                                </div>
                                            @else
                                                <video class="p-1" controls>
                                                    <source src="{{asset('media')}}/{{$post->shared_post->media[0]->filename}}" type="video/mp4">
                                                    Your browser does not support HTML video.
                                                </video>
                                            @endif


                                            @if($post->shared_post->media[1]->mediaType == 'image')
                                            <!-- if media img and imgs=1 -->
                                                <div class="p-1 w-100" style="width: 100%">
                                                    <img src="{{asset('media')}}/{{$post->shared_post->media[1]->filename}}" alt="opel car" />
                                                </div>
                                            @else
                                                <video class="p-1 w-100" controls>
                                                    <source src="{{asset('media')}}/{{$post->shared_post->media[1]->filename}}" type="video/mp4">
                                                    Your browser does not support HTML video.
                                                </video>
                                            @endif
                                        </div>

                                        @if(count($post->shared_post->media) > 2)
                                            <div class="d-flex w-100">
                                            @if($post->shared_post->media[2]->mediaType == 'image')
                                                <!-- if media img and imgs=1 -->
                                                    <div class="p-1 w-100" style="width: 100%">
                                                        <img src="{{asset('media')}}/{{$post->shared_post->media[2]->filename}}" alt="opel car" />
                                                    </div>
                                                @else
                                                    <video class="p-1 w-100" controls>
                                                        <source src="{{asset('media')}}/{{$post->shared_post->media[2]->filename}}" type="video/mp4">
                                                        Your browser does not support HTML video.
                                                    </video>
                                                @endif

                                                @if(count($post->shared_post->media) == 4)

                                                    @if($post->shared_post->media[3]->mediaType == 'image')
                                                    <!-- if media img and imgs=1 -->
                                                        <div class="p-1 w-100" style="width: 100%">
                                                            <img src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" alt="opel car" />
                                                        </div>
                                                    @else
                                                        <video class="p-1 w-100" controls>
                                                            <source src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" type="video/mp4">
                                                            Your browser does not support HTML video.
                                                        </video>
                                                    @endif
                                                @endif

                                                @if(count($post->shared_post->media) > 4)
                                                    <div class="more-media w-50" data-toggle="modal" data-target="#more-media-modal-{{$post->id}}">
                                                        <p>+{{count($post->media) - 3}}</p>
                                                        <div class="overlay"></div>

                                                    @if($post->shared_post->media[3]->mediaType == 'image')
                                                        <!-- if media img and imgs=1 -->
                                                            <div class="p-1 w-100" style="width: 100%">
                                                                <img src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" alt="opel car" />
                                                            </div>
                                                        @else
                                                            <video class="p-1 w-100" controls>
                                                                <source src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" type="video/mp4">
                                                                Your browser does not support HTML video.
                                                            </video>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="post-more-media-modal">
                                <div class="modal fade" id="more-media-modal-{{$post->shared_post->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog" style="margin-top: 10vh">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div id="media-carousel-{{$post->shared_post->id}}" class="carousel slide" data-ride="carousel">
                                                    <div class="carousel-inner">
                                                        @foreach($post->shared_post->media as $media)
                                                            <div class="carousel-item @if ($loop->first == true) active @endif">
                                                                <img src="{{asset('media')}}/{{$media->filename}}" class="d-block w-100" alt="...">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <a class="carousel-control-prev" href="#media-carousel-{{$post->shared_post->id}}" role="button"
                                                       data-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="sr-only">Previous</span>
                                                    </a>
                                                    <a class="carousel-control-next" href="#media-carousel-{{$post->shared_post->id}}" role="button"
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
                        @endif
                    </div>
                </div>
            @endif
            <div class="post-statistics mt-3 d-flex">
                <div class="likes">
                    @if($post->liked)
                        <div class="reaction-container" id="reaction-container-{{$post->id}}">
                                    <span class="reaction-btn">
                                            <span class="reaction-btn-emo like-btn-{{$post->user_react[0]->name}}" id="reaction-btn-emo-{{$post->id}}"></span>
                                            <span class="reaction-btn-text reaction-btn-text-{{$post->user_react[0]->name}} active" onclick="unlikeModelSubmit({{$post->id}},{{$post->user_react[0]->id}})" id="reaction-btn-text-{{$post->id}}">
                                                {{$post->user_react[0]->name}}
                                                    <form id="unlike-form-{{$post->id}}-{{$post->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                        @csrf
                                                        <input type="hidden" name="model_id" value="{{$post->id}}">
                                                        <input type="hidden" name="model_type" value="post">
                                                        <input type="hidden" name="reactId" value="{{$post->user_react[0]->id}}">
                                                       <input type="hidden" name="requestType" id="like-request-type-{{$post->id}}" value="delete">
                                                    </form>
                                            </span>
                                            <ul class="emojies-box">
                                                @foreach($reacts as $react)
                                                    <!-- Reaction buttons container-->
                                                        <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$post->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                                                        <form id="like-form-{{$post->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                        @csrf
                                                        <input type="hidden" name="model_id" value="{{$post->id}}">
                                                        <input type="hidden" name="model_type" value="post">
                                                        <input type="hidden" name="reactId" value="{{$react->id}}">
                                                       <input type="hidden" name="requestType" id="like-request-type-{{$post->id}}" value="update">
                                                    </form>
                                                    @endforeach
                                            </ul>
                                          </span>
                            <div class="like-stat">
                                        <span class="like-emo" id="like-emo-{{$post->id}}">
                                              <!-- like emotions container -->
                                              <span class="like-btn-like"></span>
                                                @if($post->user_react[0]->name != "like")
                                                <span class="like-btn-{{$post->user_react[0]->name}}"></span>
                                            @endif
                                            </span>
                                <span class="like-details" id="like-details-{{$post->id}}" data-toggle="modal" data-target="#likes-modal-{{$post->id}}">You @if($post->likes->count-1 != 0) and {{$post->likes->count-1}} @if($post->likes->count-1 > 1000) k @endif others @endif</span>
                            </div>
                        </div>
                    @else
                        <div class="reaction-container" id="reaction-container-{{$post->id}}">
                                    <span class="reaction-btn">
                                        <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$post->id}}" style="display: none"></span>
                                        <span class="reaction-btn-text" id="reaction-btn-text-{{$post->id}}">
                                            <div><i class="far fa-thumbs-up"></i>
                                                @if($post->likes->count > 0)
                                                    <span>
                                                        {{$post->likes->count}}
                                                    </span>
                                                @endif
                                            </div>
                                        </span>
                                        <ul class="emojies-box">
                                            @foreach($reacts as $react)
                                                <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$post->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                                                <form id="like-form-{{$post->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="model_id" value="{{$post->id}}">
                                                <input type="hidden" name="model_type" value="post">
                                                <input type="hidden" name="reactId" value="{{$react->id}}">
                                            </form>
                                            @endforeach
                                        </ul>
                                    </span>
                            <div class="like-stat" id="like-stat-{{$post->id}}" style="display: none">
                                        <span class="like-emo" id="like-emo-{{$post->id}}">
                                          <span class="like-btn-like"></span>
                                        </span>
                                <span class="like-details" id="like-details-{{$post->id}}" data-toggle="modal" data-target="#likes-modal-{{$post->id}}">@if($post->likes->count-1 > 0) and {{$post->likes->count-1}} @if($post->likes->count-1 > 1000) k @endif others @endif</span>
                            </div>
                        </div>
                    @endif
                </div>
                @if($post->likes->count > 0)
                    <div class="likes-modal">
                        <div class="modal fade" id="likes-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" style="margin-top: 10vh;">
                                <div class="modal-content">
                                    <div class="modal-header d-flex justify-content-between">
                                        <span></span>
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            Reactions
                                        </h5>
                                        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="services-controller m-3 text-left">
                                            <button onclick="filterPostLikes({{$post->id}},'all-{{$post->id}}')" class="btn btn-light active-{{$post->id}} filter-all-{{$post->id}} ez-active" id="{{$post->id}}" data-filter="all-{{$post->id}}">
                                                All
                                            </button>
                                            <button class="btn btn-light active-{{$post->id}} filter-like-{{$post->id}}" onclick="filterPostLikes({{$post->id}},'like-{{$post->id}}')" id="{{$post->id}}" data-filter="like-{{$post->id}}">
                                                like
                                            </button>
                                            <button class="btn btn-light active-{{$post->id}} filter-love-{{$post->id}}" onclick="filterPostLikes({{$post->id}},'love-{{$post->id}}')" id="{{$post->id}}" data-filter="love-{{$post->id}}">
                                                love
                                            </button>
                                            <button class="btn btn-light active-{{$post->id}} filter-haha-{{$post->id}}" onclick="filterPostLikes({{$post->id}},'haha-{{$post->id}}')" id="{{$post->id}}" data-filter="haha-{{$post->id}}">
                                                haha
                                            </button>
                                            <button class="btn btn-light active-{{$post->id}} filter-sad-{{$post->id}}" onclick="filterPostLikes({{$post->id}},'sad-{{$post->id}}')" id="{{$post->id}}" data-filter="sad-{{$post->id}}">
                                                sad
                                            </button>
                                            <button class="btn btn-light active-{{$post->id}} filter-angry-{{$post->id}}" onclick="filterPostLikes({{$post->id}},'angry-{{$post->id}}')" id="{{$post->id}}" data-filter="angry-{{$post->id}}">
                                                angry
                                            </button>
                                        </div>
                                        <div class="likes-container mt-3">
                                            <div class="filter-{{$post->id}} like-{{$post->id}}">
                                                @foreach($post->like_stat as $like_emoji)
                                                    <div class="people-info d-flex align-items-center">
                                                        @if($like_emoji->publisher->personal_image != null)
                                                            <img class="profile-figure rounded-circle"
                                                                 src="{{asset('media')}}/{{$like_emoji->publisher->personal_image}}" />
                                                        @else
                                                            <img class="profile-figure rounded-circle"
                                                                 src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                        @endif
                                                        <p class="mb-0 ml-3"><b>{{$like_emoji->publisher->name}}</b></p>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="filter-{{$post->id}} love-{{$post->id}}">
                                                @foreach($post->love_stat as $love)
                                                    <div class="people-info d-flex align-items-center">
                                                        @if($love->publisher->personal_image != null)
                                                            <img class="profile-figure rounded-circle"
                                                                 src="{{asset('media')}}/{{$love->publisher->personal_image}}" />
                                                        @else
                                                            <img class="profile-figure rounded-circle"
                                                                 src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                        @endif
                                                        <p class="mb-0 ml-3"><b>{{$love->publisher->name}}</b></p>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="filter-{{$post->id}} haha-{{$post->id}}">
                                                @foreach($post->haha_stat as $haha)
                                                    <div class="people-info d-flex align-items-center">
                                                        @if($haha->publisher->personal_image != null)
                                                            <img class="profile-figure rounded-circle"
                                                                 src="{{asset('media')}}/{{$haha->publisher->personal_image}}" />
                                                        @else
                                                            <img class="profile-figure rounded-circle"
                                                                 src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                        @endif
                                                        <p class="mb-0 ml-3"><b>{{$haha->publisher->name}}</b></p>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="filter-{{$post->id}} sad-{{$post->id}}">
                                                @foreach($post->sad_stat as $sad)
                                                    <div class="people-info d-flex align-items-center">
                                                        @if($sad->publisher->personal_image != null)
                                                            <img class="profile-figure rounded-circle"
                                                                 src="{{asset('media')}}/{{$sad->publisher->personal_image}}" />
                                                        @else
                                                            <img class="profile-figure rounded-circle"
                                                                 src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                        @endif
                                                        <p class="mb-0 ml-3"><b>{{$sad->publisher->name}}</b></p>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="filter-{{$post->id}} angry-{{$post->id}}">
                                                @foreach($post->angry_stat as $angry)
                                                    <div class="people-info d-flex align-items-center">
                                                        @if($angry->publisher->personal_image != null)
                                                            <img class="profile-figure rounded-circle"
                                                                 src="{{asset('media')}}/{{$angry->publisher->personal_image}}" />
                                                        @else
                                                            <img class="profile-figure rounded-circle"
                                                                 src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                        @endif
                                                        <p class="mb-0 ml-3"><b>{{$angry->publisher->name}}</b></p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="comments" onclick="toggleComments({{$post->id}})"><i class="far fa-comment ml-3"></i>
                    <span id="comment-count-{{$post->id}}">
                                    @if($post->comments->count > 0)
                            {{$post->comments->count}}
                        @endif
                                </span>
                </div>
                <div class="modal" id="share-post-modal-{{$post->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">share Post</h5>
                                <button type="button" id="add-post-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12" id="error-message-{{$post->id}}" style="display: none">
                                    <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{route('posts.store')}}" method="POST" class="container" id="share-post-form-{{$post->id}}" enctype="multipart/form-data">

                                    @csrf
                                    <input type="hidden" name="post_id" value="@if($post->type == "post"){{$post->id}}@else{{$post->shared_post->id}}@endif">
                                    <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                        <label for="cars">Choose Post Privacy:</label>
                                        <select id="post-privacy" name="privacy_id">
                                            @foreach($privacy as $postprivacy)
                                                @if(App::getlocale() == 'en')
                                                    <option value="{{$postprivacy->id}}">{{$postprivacy->name_en}}</option>
                                                @else
                                                    <option value="{{$postprivacy->id}}">{{$postprivacy->name_ar}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                        <label for="cars">Choose A Category:</label>
                                        <select id="post-category" name="category_id">
                                            @foreach($categories as $category)
                                                @if(App::getlocale() == 'en')
                                                    <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_en}}</option>
                                                @else
                                                    <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_ar}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                                    <textarea class="w-75 p-2" name="body" id="textarea" cols="200" rows="4"
                                                              placeholder="Post Description..."></textarea>
                                        <div id="menu" class="menu" role="listbox"></div>
                                    </div>
                                    <!-- Add Post Btn -->
                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" onclick="sharePostSubmit({{$post->id}})" class="btn btn-warning btn-block w-75">
                                            share
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="shares">
                    <i data-toggle="modal" data-target="#share-post-modal-{{$post->id}}" class="fas fa-share ml-3"></i>
                    @if($post->shares > 0)
                        <span data-toggle="modal" data-target="#shares-modal-{{$post->id}}">
                                    {{$post->shares}}
                                </span>
                    @endif
                </div>
            </div>
            <div class="show-story-views-modal">
                <div class="modal fade" id="shares-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Story Viewers
                                </h5>
                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @foreach($post->share_details as $share)
                                    <div class="people-info d-flex align-items-center">
                                        @if($share->publisher->personal_image != null)
                                            <img class="rounded-circle" style="width: 50px;height: 50px"
                                                 src="{{asset('media')}}/{{$share->publisher->personal_image}}"
                                                 alt="User Profile Pic">
                                        @else
                                            <img class="rounded-circle" style="width: 50px;height: 50px"
                                                 src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                 alt="User Profile Pic">
                                        @endif
                                        <p class="mb-0 ml-3"><b>{{$share->publisher->name}}</b></p>
                                    </div>
                                    @if($loop->last == false)
                                        <hr>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="post-comment-list post-comment-list-{{$post->id}} mt-2">
                <div class="hide-commnet-list d-flex flex-row-reverse">
                    <span onclick="toggleComments({{$post->id}})"><i class="fas fa-chevron-up"></i> Hide</span>
                </div>
                @if(count($post->comments) > 0)
                    @foreach($post->comments as $comment)
                        @if($comment->type == "comment" && $comment->reported == false)
                            <div class="comment d-flex justify-content-between" id="comment-{{$comment->id}}">
                                <div class="comment-owner d-flex p-2 w-100">
                                    @if($comment->publisher->personal_image)
                                        <div class="owner-img">
                                            <a style="display: inline" href="{{route('profile',$comment->publisher->id)}}"><img src="{{asset('media')}}/{{$comment->publisher->personal_image}}" class="rounded-circle" /></a>
                                        </div>
                                    @else
                                        <div class="owner-img">
                                            <a style="display: inline" href="{{route('profile',$comment->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                                        </div>
                                    @endif
                                    <div class="owner-name pl-3 w-100">
                                        <a href="comment.userProfile" class="mb-0"><b>{{$comment->publisher->name}}</b></a>
                                        <p class="comment-content mb-0"><?php echo $comment->body ?></p>
                                        <!-- attatched img -->
                                        <!-- <img src="img.jpg" class="w-100 pt-3"> -->
                                        <!-- attatched vedio -->
                                        @if($comment->media != null)
                                            @if($comment->media->mediaType == 'image')
                                                <img src="{{asset('media')}}/{{$comment->media->filename}}" class="w-100 pt-3">
                                            @else
                                                <video class="pt-3" controls>
                                                    <source src="{{asset('media')}}/{{$comment->media->filename}}" type="video/mp4">
                                                </video>
                                            @endif
                                        @endif
                                        <ul class="comment-actions mt-1">
                                        @if($comment->liked)
                                            <!-- if post is liked by user -->
                                                <!-- <div><i class="fas fa-thumbs-up"></i><span> 20</span></div> -->
                                                <!-- if post isn't liked by user -->
                                                <div class="reaction-container" id="reaction-container-{{$comment->id}}">
                                                    <!-- container div for reaction system -->
                                                    <span class="reaction-btn">
                                                            <!-- Default like button -->
                                                            <span class="reaction-btn-emo like-btn-{{$comment->user_react[0]->name}}" id="reaction-btn-emo-{{$comment->id}}"></span>
                                                        <!-- Default like button emotion-->
                                                            <span class="reaction-btn-text reaction-btn-text-{{$comment->user_react[0]->name}} active" onclick="unlikeModelSubmit({{$comment->id}},{{$comment->user_react[0]->id}})" id="reaction-btn-text-{{$comment->id}}">
                                                                {{$comment->user_react[0]->name}}
                                                                    <form id="unlike-form-{{$comment->id}}-{{$comment->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                        @csrf
                                                                        <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                        <input type="hidden" name="model_type" value="comment">
                                                                        <input type="hidden" name="reactId" value="{{$comment->user_react[0]->id}}">
                                                                       <input type="hidden" name="requestType" id="like-request-type-{{$comment->id}}" value="delete">
                                                                    </form>
                                                            </span>
                                                        <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                            <ul class="emojies-box">
                                                                @foreach($reacts as $react)
                                                                    <!-- Reaction buttons container-->
                                                                        <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$comment->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                                                                        <form id="like-form-{{$comment->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                        @csrf
                                                                        <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                        <input type="hidden" name="model_type" value="comment">
                                                                        <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                       <input type="hidden" name="requestType" id="like-request-type-{{$comment->id}}" value="update">
                                                                    </form>
                                                                    @endforeach
                                                            </ul>
                                                          </span>
                                                    <div class="like-stat">
                                                        <!-- Like statistic container-->
                                                        <span class="like-emo" id="like-emo-{{$comment->id}}">
                                                                  <!-- like emotions container -->
                                                                  <span class="like-btn-like"></span>
                                                                    @if($comment->user_react[0]->name != "like")
                                                                <span class="like-btn-{{$comment->user_react[0]->name}}"></span>
                                                        @endif
                                                        <!-- given emotions like, wow, sad (default:Like) -->
                                                                </span>
                                                        <span class="like-details" id="like-details-{{$comment->id}}" data-toggle="modal" data-target="#likes-modal-{{$comment->id}}">You @if($comment->likes->count-1 != 0) and {{$comment->likes->count-1}} @if($comment->likes->count-1 > 1000) k @endif others @endif</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="reaction-container" id="reaction-container-{{$comment->id}}">
                                                    <!-- container div for reaction system -->
                                                    <span class="reaction-btn">
                                                                <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$comment->id}}" style="display: none"></span>
                                                        <!-- Default like button emotion-->
                                                                <span class="reaction-btn-text" id="reaction-btn-text-{{$comment->id}}">
                                                                    <div><i class="far fa-thumbs-up"></i>
                                                                        @if($comment->likes->count > 0)
                                                                            <span data-toggle="modal" data-target="#likes-modal-{{$comment->id}}">
                                                                                {{$comment->likes->count}}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </span>
                                                        <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                                <ul class="emojies-box">
                                                                    @foreach($reacts as $react)
                                                                        <!-- Reaction buttons container-->
                                                                            <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$comment->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                                                                            <form id="like-form-{{$comment->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                            @csrf
                                                                            <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                            <input type="hidden" name="model_type" value="comment">
                                                                            <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                        </form>
                                                                        @endforeach
                                                                </ul>
                                                            </span>
                                                    <div class="like-stat" id="like-stat-{{$comment->id}}" style="display: none">
                                                        <!-- Like statistic container-->
                                                        <span class="like-emo" id="like-emo-{{$comment->id}}">
                                                                  <!-- like emotions container -->
                                                                  <span class="like-btn-like"></span>
                                                            <!-- given emotions like, wow, sad (default:Like) -->
                                                                </span>
                                                        <span class="like-details" id="like-details-{{$comment->id}}" data-toggle="modal" data-target="#likes-modal-{{$comment->id}}">@if($comment->likes->count-1 > 0) and {{$comment->likes->count-1}} @if($comment->likes->count-1 > 1000) k @endif others @endif</span>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($comment->likes->count > 0)
                                                <div class="likes-modal">
                                                    <div class="modal fade" id="likes-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog" style="margin-top: 10vh;">
                                                            <div class="modal-content">
                                                                <div class="modal-header d-flex justify-content-between">
                                                                    <span></span>
                                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                                        Reactions
                                                                    </h5>
                                                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="services-controller m-3 text-left">
                                                                        <button onclick="filterCommentLikes({{$comment->id}},'all-{{$comment->id}}')" class="btn btn-light active-{{$comment->id}} filter-all-{{$comment->id}} ez-active" id="{{$comment->id}}">
                                                                            All
                                                                        </button>
                                                                        <button class="btn btn-light active-{{$comment->id}} filter-like-{{$comment->id}}" onclick="filterCommentLikes({{$comment->id}},'like-{{$comment->id}}')" id="{{$comment->id}}">
                                                                            like
                                                                        </button>
                                                                        <button class="btn btn-light active-{{$comment->id}} filter-love-{{$comment->id}}" onclick="filterCommentLikes({{$comment->id}},'love-{{$comment->id}}')" id="{{$comment->id}}">
                                                                            love
                                                                        </button>
                                                                        <button class="btn btn-light active-{{$comment->id}} filter-haha-{{$comment->id}}" onclick="filterCommentLikes({{$comment->id}},'haha-{{$comment->id}}')" id="{{$comment->id}}">
                                                                            haha
                                                                        </button>
                                                                        <button class="btn btn-light active-{{$comment->id}} filter-sad-{{$comment->id}}" onclick="filterCommentLikes({{$comment->id}},'sad-{{$comment->id}}')" id="{{$comment->id}}">
                                                                            sad
                                                                        </button>
                                                                        <button class="btn btn-light active-{{$comment->id}} filter-angry-{{$comment->id}}" onclick="filterCommentLikes({{$comment->id}},'angry-{{$comment->id}}')" id="{{$comment->id}}">
                                                                            angry
                                                                        </button>
                                                                    </div>
                                                                    <div class="likes-container mt-3">
                                                                        <div class="filter-{{$comment->id}} like-{{$comment->id}}">
                                                                            @foreach($comment->like_stat as $like_emoji)
                                                                                <div class="people-info d-flex align-items-center">
                                                                                    @if($like_emoji->publisher->personal_image != null)
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="{{asset('media')}}/{{$like_emoji->publisher->personal_image}}" />
                                                                                    @else
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                    @endif
                                                                                    <p class="mb-0 ml-3"><b>{{$like_emoji->publisher->name}}</b></p>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="filter-{{$comment->id}} love-{{$comment->id}}">
                                                                            @foreach($comment->love_stat as $love)
                                                                                <div class="people-info d-flex align-items-center">
                                                                                    @if($love->publisher->personal_image != null)
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="{{asset('media')}}/{{$love->publisher->personal_image}}" />
                                                                                    @else
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                    @endif
                                                                                    <p class="mb-0 ml-3"><b>{{$love->publisher->name}}</b></p>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="filter-{{$comment->id}} haha-{{$comment->id}}">
                                                                            @foreach($comment->haha_stat as $haha)
                                                                                <div class="people-info d-flex align-items-center">
                                                                                    @if($haha->publisher->personal_image != null)
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="{{asset('media')}}/{{$haha->publisher->personal_image}}" />
                                                                                    @else
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                    @endif
                                                                                    <p class="mb-0 ml-3"><b>{{$haha->publisher->name}}</b></p>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="filter-{{$comment->id}} sad-{{$comment->id}}">
                                                                            @foreach($comment->sad_stat as $sad)
                                                                                <div class="people-info d-flex align-items-center">
                                                                                    @if($sad->publisher->personal_image != null)
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="{{asset('media')}}/{{$sad->publisher->personal_image}}" />
                                                                                    @else
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                    @endif
                                                                                    <p class="mb-0 ml-3"><b>{{$sad->publisher->name}}</b></p>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="filter-{{$comment->id}} angry-{{$comment->id}}">
                                                                            @foreach($comment->angry_stat as $angry)
                                                                                <div class="people-info d-flex align-items-center">
                                                                                    @if($angry->publisher->personal_image != null)
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="{{asset('media')}}/{{$angry->publisher->personal_image}}" />
                                                                                    @else
                                                                                        <img class="profile-figure rounded-circle"
                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                    @endif
                                                                                    <p class="mb-0 ml-3"><b>{{$angry->publisher->name}}</b></p>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <li class="ml-3 text-primary" onclick="toggleReply({{$comment->id}},'{{$comment->publisher->name}}')">Reply</li>
                                        </ul>
                                        @if($comment->replies)
                                            <div class="replays comment-1-replays" id="comment-replies-{{$comment->id}}" style="display: none">
                                                <div class="mt-2">
                                                    @foreach($comment->replies as $reply)
                                                        @if($reply->reported == false)
                                                            <div class="comment d-flex justify-content-between" id="reply-{{$reply->id}}">
                                                                <div class="comment-owner d-flex p-2">
                                                                    @if($reply->publisher->personal_image)
                                                                        <div class="owner-img">
                                                                            <a style="display: inline" href="{{route('profile',$reply->publisher->id)}}"><img src="{{asset('media')}}/{{$reply->publisher->personal_image}}" class="rounded-circle" /></a>
                                                                        </div>
                                                                    @else
                                                                        <div class="owner-img">
                                                                            <a style="display: inline" href="{{route('profile',$reply->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                                                                        </div>
                                                                    @endif
                                                                    <div class="owner-name pl-3 w-100">
                                                                        <a href="comment.userProfile" class="mb-0"><b>{{$reply->publisher->name}}</b></a>
                                                                        <p class="comment-content mb-0"><?php echo $reply->body ?></p>
                                                                        <!-- attatched img -->
                                                                        <!-- <img src="img.jpg" class="w-100 pt-3"> -->
                                                                        <!-- attatched vedio -->
                                                                        @if($reply->media != null)
                                                                            @if($reply->media->mediaType == 'image')
                                                                                <img src="{{asset('media')}}/{{$reply->media->filename}}" class="w-100 pt-3">
                                                                            @else
                                                                                <video class="pt-3" controls>
                                                                                    <source src="{{asset('media')}}/{{$reply->media->filename}}" type="video/mp4">
                                                                                </video>
                                                                            @endif
                                                                        @endif
                                                                        <ul class="comment-actions mt-1">
                                                                        @if($reply->liked)
                                                                            <!-- if post is liked by user -->
                                                                                <!-- <div><i class="fas fa-thumbs-up"></i><span> 20</span></div> -->
                                                                                <!-- if post isn't liked by user -->
                                                                                <div class="reaction-container" id="reaction-container-{{$reply->id}}">
                                                                                    <!-- container div for reaction system -->
                                                                                    <span class="reaction-btn">
                                                                                                <!-- Default like button -->
                                                                                                <span class="reaction-btn-emo like-btn-{{$reply->user_react[0]->name}}" id="reaction-btn-emo-{{$reply->id}}"></span>
                                                                                        <!-- Default like button emotion-->
                                                                                                <span class="reaction-btn-text reaction-btn-text-{{$reply->user_react[0]->name}} active" onclick="unlikeModelSubmit({{$reply->id}},{{$reply->user_react[0]->id}})" id="reaction-btn-text-{{$reply->id}}">
                                                                                                    {{$reply->user_react[0]->name}}
                                                                                                    <form id="unlike-form-{{$reply->id}}-{{$reply->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                                        @csrf
                                                                                                        <input type="hidden" name="model_id" value="{{$reply->id}}">
                                                                                                        <input type="hidden" name="model_type" value="comment">
                                                                                                        <input type="hidden" name="reactId" value="{{$reply->user_react[0]->id}}">
                                                                                                       <input type="hidden" name="requestType" id="like-request-type-{{$reply->id}}" value="delete">
                                                                                                    </form>
                                                                                                </span>
                                                                                        <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                                                                <ul class="emojies-box">
                                                                                                    @foreach($reacts as $react)
                                                                                                        <!-- Reaction buttons container-->
                                                                                                            <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$reply->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                                                                                                            <form id="like-form-{{$reply->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                                            @csrf
                                                                                                            <input type="hidden" name="model_id" value="{{$reply->id}}">
                                                                                                            <input type="hidden" name="model_type" value="comment">
                                                                                                            <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                                                            <input type="hidden" name="requestType" id="like-request-type-{{$reply->id}}" value="update">
                                                                                                        </form>
                                                                                                        @endforeach
                                                                                                </ul>
                                                                                            </span>
                                                                                    <div class="like-stat">
                                                                                        <!-- Like statistic container-->
                                                                                        <span class="like-emo" id="like-emo-{{$reply->id}}">
                                                                                                  <!-- like emotions container -->
                                                                                                    <span class="like-btn-like"></span>
                                                                                                    @if($reply->user_react[0]->name != "like")
                                                                                                <span class="like-btn-{{$reply->user_react[0]->name}}"></span>
                                                                                        @endif
                                                                                        <!-- given emotions like, wow, sad (default:Like) -->
                                                                                                </span>
                                                                                        <span class="like-details" id="like-details-{{$reply->id}}" data-toggle="modal" data-target="#likes-modal-{{$reply->id}}">You @if($reply->likes->count-1 != 0) and {{$reply->likes->count-1}} @if($reply->likes->count-1 > 1000) k @endif others @endif</span>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="reaction-container" id="reaction-container-{{$reply->id}}">
                                                                                    <!-- container div for reaction system -->
                                                                                    <span class="reaction-btn">
                                                                                            <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$reply->id}}" style="display: none"></span>
                                                                                        <!-- Default like button emotion-->
                                                                                            <span class="reaction-btn-text" id="reaction-btn-text-{{$reply->id}}">
                                                                                                <div><i class="far fa-thumbs-up"></i>
                                                                                                    @if($reply->likes->count > 0)
                                                                                                        <span data-toggle="modal" data-target="#likes-modal-{{$reply->id}}">
                                                                                                            {{$reply->likes->count}}
                                                                                                        </span>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </span>
                                                                                        <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                                                            <ul class="emojies-box">
                                                                                                @foreach($reacts as $react)
                                                                                                    <!-- Reaction buttons container-->
                                                                                                        <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$reply->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                                                                                                        <form id="like-form-{{$reply->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                                        @csrf
                                                                                                        <input type="hidden" name="model_id" value="{{$reply->id}}">
                                                                                                        <input type="hidden" name="model_type" value="comment">
                                                                                                        <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                                                    </form>
                                                                                                    @endforeach
                                                                                            </ul>
                                                                                        </span>
                                                                                    <div class="like-stat" id="like-stat-{{$reply->id}}" style="display: none">
                                                                                        <!-- Like statistic container-->
                                                                                        <span class="like-emo" id="like-emo-{{$reply->id}}">
                                                                                                  <!-- like emotions container -->
                                                                                                  <span class="like-btn-like"></span>
                                                                                            <!-- given emotions like, wow, sad (default:Like) -->
                                                                                                </span>
                                                                                        <span class="like-details" id="like-details-{{$reply->id}}" data-toggle="modal" data-target="#likes-modal-{{$reply->id}}">@if($reply->likes->count-1 > 0) and {{$reply->likes->count-1}} @if($reply->likes->count-1 > 1000) k @endif others @endif</span>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if($reply->likes->count > 0)
                                                                                <div class="likes-modal">
                                                                                    <div class="modal fade" id="likes-modal-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                                                                                        <div class="modal-dialog" style="margin-top: 10vh;">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header d-flex justify-content-between">
                                                                                                    <span></span>
                                                                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                                                                        Reactions
                                                                                                    </h5>
                                                                                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <div class="modal-body">
                                                                                                    <div class="services-controller m-3 text-left">
                                                                                                        <button onclick="filterReplyLikes({{$reply->id}},'all-{{$reply->id}}')" class="btn btn-light active-{{$reply->id}} filter-all-{{$reply->id}} ez-active" id="{{$reply->id}}">
                                                                                                            All
                                                                                                        </button>
                                                                                                        <button class="btn btn-light active-{{$reply->id}} filter-like-{{$reply->id}}" onclick="filterReplyLikes({{$reply->id}},'like-{{$reply->id}}')" id="{{$reply->id}}">
                                                                                                            like
                                                                                                        </button>
                                                                                                        <button class="btn btn-light active-{{$reply->id}} filter-love-{{$reply->id}}" onclick="filterReplyLikes({{$reply->id}},'love-{{$reply->id}}')" id="{{$reply->id}}">
                                                                                                            love
                                                                                                        </button>
                                                                                                        <button class="btn btn-light active-{{$reply->id}} filter-haha-{{$reply->id}}" onclick="filterReplyLikes({{$reply->id}},'haha-{{$reply->id}}')" id="{{$reply->id}}">
                                                                                                            haha
                                                                                                        </button>
                                                                                                        <button class="btn btn-light active-{{$reply->id}} filter-sad-{{$reply->id}}" onclick="filterReplyLikes({{$reply->id}},'sad-{{$reply->id}}')" id="{{$reply->id}}">
                                                                                                            sad
                                                                                                        </button>
                                                                                                        <button class="btn btn-light active-{{$reply->id}} filter-angry-{{$reply->id}}" onclick="filterReplyLikes({{$reply->id}},'angry-{{$reply->id}}')" id="{{$reply->id}}">
                                                                                                            angry
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="likes-container mt-3">
                                                                                                        <div class="filter-{{$reply->id}} like-{{$reply->id}}">
                                                                                                            @foreach($reply->like_stat as $like_emoji)
                                                                                                                <div class="people-info d-flex align-items-center">
                                                                                                                    @if($like_emoji->publisher->personal_image != null)
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="{{asset('media')}}/{{$like_emoji->publisher->personal_image}}" />
                                                                                                                    @else
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                                                    @endif
                                                                                                                    <p class="mb-0 ml-3"><b>{{$like_emoji->publisher->name}}</b></p>
                                                                                                                </div>
                                                                                                            @endforeach
                                                                                                        </div>
                                                                                                        <div class="filter-{{$reply->id}} love-{{$reply->id}}">
                                                                                                            @foreach($reply->love_stat as $love)
                                                                                                                <div class="people-info d-flex align-items-center">
                                                                                                                    @if($love->publisher->personal_image != null)
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="{{asset('media')}}/{{$love->publisher->personal_image}}" />
                                                                                                                    @else
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                                                    @endif
                                                                                                                    <p class="mb-0 ml-3"><b>{{$love->publisher->name}}</b></p>
                                                                                                                </div>
                                                                                                            @endforeach
                                                                                                        </div>
                                                                                                        <div class="filter-{{$reply->id}} haha-{{$reply->id}}">
                                                                                                            @foreach($reply->haha_stat as $haha)
                                                                                                                <div class="people-info d-flex align-items-center">
                                                                                                                    @if($haha->publisher->personal_image != null)
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="{{asset('media')}}/{{$haha->publisher->personal_image}}" />
                                                                                                                    @else
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                                                    @endif
                                                                                                                    <p class="mb-0 ml-3"><b>{{$haha->publisher->name}}</b></p>
                                                                                                                </div>
                                                                                                            @endforeach
                                                                                                        </div>
                                                                                                        <div class="filter-{{$reply->id}} sad-{{$reply->id}}">
                                                                                                            @foreach($reply->sad_stat as $sad)
                                                                                                                <div class="people-info d-flex align-items-center">
                                                                                                                    @if($sad->publisher->personal_image != null)
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="{{asset('media')}}/{{$sad->publisher->personal_image}}" />
                                                                                                                    @else
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                                                    @endif
                                                                                                                    <p class="mb-0 ml-3"><b>{{$sad->publisher->name}}</b></p>
                                                                                                                </div>
                                                                                                            @endforeach
                                                                                                        </div>
                                                                                                        <div class="filter-{{$reply->id}} angry-{{$reply->id}}">
                                                                                                            @foreach($reply->angry_stat as $angry)
                                                                                                                <div class="people-info d-flex align-items-center">
                                                                                                                    @if($angry->publisher->personal_image != null)
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="{{asset('media')}}/{{$angry->publisher->personal_image}}" />
                                                                                                                    @else
                                                                                                                        <img class="profile-figure rounded-circle"
                                                                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                                                                    @endif
                                                                                                                    <p class="mb-0 ml-3"><b>{{$angry->publisher->name}}</b></p>
                                                                                                                </div>
                                                                                                            @endforeach
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            <li class="ml-3 text-primary" onclick="makeReply({{$comment->id}},'{{$reply->publisher->name}}')">Reply</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="comment-options comment-options-{{$reply->id}}">
                                                                    <ul class="options">
                                                                        <li data-toggle="modal" data-target="#report-comment-modal-{{$reply->id}}">
                                                                            Report this comment</li>
                                                                        @if($reply->publisher->id == auth()->user()->id)
                                                                            <li data-toggle="modal" data-target="#edit-reply-modal-{{$reply->id}}">Edit</li>
                                                                            <li onclick="confirm('{{ __("Are you sure you want to delete this reply ?") }}') ? deleteCommentSubmit({{$reply->id}},{{$post->id}}) : ''" >Delete</li>
                                                                            <form action="{{ route('comments.destroy', $reply->id) }}" id="delete-comment-form-{{$reply->id}}" method="POST">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <!-- ajax-->
                                                                            </form>
                                                                        @endif

                                                                        <div class="post-edit-modal">
                                                                            <div class="modal fade" id="edit-reply-modal-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                                                                                <div class="modal-dialog" style="margin-top: 22vh">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header d-flex justify-content-between">
                                                                                            <span></span>
                                                                                            <h5 class="modal-title" id="exampleModalLabel">Edit Reply</h5>
                                                                                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">&times;</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <div class="col-12" id="error-message-{{$reply->id}}" style="display: none">
                                                                                                <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                                                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <form action="{{route('comments.update',$reply->id)}}" id="edit-reply-form-{{$reply->id}}" method="POST" class="container" enctype="multipart/form-data">

                                                                                            @csrf
                                                                                            @method('put')

                                                                                            <!-- Post Desc -->
                                                                                                <div class="post-desc d-flex justify-content-center mt-2">
                                                                                                          <textarea onfocus="mentionAdd('text-edit-{{$reply->id}}','menu-edit-reply-{{$reply->id}}')" id="text-edit-{{$reply->id}}" class="w-75" name="body" cols="200" rows="4"
                                                                                                                    placeholder="Start Typing..." >
                                                                                                            @if($reply->mentions != null)
                                                                                                                  {{$reply->edit}}
                                                                                                              @else
                                                                                                                  {{$reply->body}}
                                                                                                              @endif
                                                                                                          </textarea>
                                                                                                    <div id="menu-edit-reply-{{$reply->id}}" class="menu" role="listbox"></div>
                                                                                                </div>

                                                                                                <input type="hidden" name="model_id" value="{{$post->id}}">

                                                                                                <div class="post-desc d-flex justify-content-center mt-2">
                                                                                                    <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs"/>
                                                                                                </div>
                                                                                                <!-- Add Post Btn -->
                                                                                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                                                                                    <button type="button" onclick="editReplySubmit({{$reply->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                                                                        Save
                                                                                                    </button>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="post-edit-modal">
                                                                            <div class="modal fade" id="report-comment-modal-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                                                                                <div class="modal-dialog" style="margin-top: 22vh">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header d-flex justify-content-between">
                                                                                            <span></span>
                                                                                            <h5 class="modal-title" id="exampleModalLabel">Report Comment</h5>
                                                                                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">&times;</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <div class="col-12" id="error-message-{{$reply->id}}" style="display: none">
                                                                                                <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$reply->id}}" role="alert">
                                                                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <form action="{{route('userreports.store')}}" id="report-comment-form-{{$reply->id}}" method="POST" class="container" enctype="multipart/form-data">

                                                                                            @csrf

                                                                                            <!-- Post Desc -->
                                                                                                <div class="post-desc d-flex justify-content-center mt-2">
                                                                                                          <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                                                                                    placeholder="Start Typing..." ></textarea>
                                                                                                </div>
                                                                                                <!-- Add Post Btn -->
                                                                                                <input type="hidden" name="model_id" value="{{$reply->id}}">
                                                                                                <input type="hidden" name="model_type" value="comment">

                                                                                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                                                                                    <button type="button" onclick="reportCommentSubmit({{$reply->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                                                                        Report
                                                                                                    </button>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </ul>
                                                                </div>
                                                                <div class="comment-option ml-auto pr-3 pt-2">
                                                                    <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions({{$reply->id}})"></i>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    <div id="added-reply-{{$comment->id}}">

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="comment-options comment-options-{{$comment->id}}">
                                    <ul class="options">
                                        <li data-toggle="modal" data-target="#report-comment-modal-{{$comment->id}}">
                                            Report this comment</li>
                                        @if($comment->publisher->id == auth()->user()->id)
                                            <li data-toggle="modal" data-target="#edit-comment-modal-{{$comment->id}}">Edit</li>
                                            <li onclick="confirm('{{ __("Are you sure you want to delete this comment ?") }}') ? deleteCommentSubmit({{$comment->id}},{{$post->id}}) : ''" >Delete</li>
                                            <form action="{{ route('comments.destroy', $comment->id) }}" id="delete-comment-form-{{$comment->id}}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <!-- ajax-->
                                            </form>
                                        @endif

                                        <div class="post-edit-modal">
                                            <div class="modal fade" id="edit-comment-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog" style="margin-top: 22vh">
                                                    <div class="modal-content">
                                                        <div class="modal-header d-flex justify-content-between">
                                                            <span></span>
                                                            <h5 class="modal-title" id="exampleModalLabel">Edit Comment</h5>
                                                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="col-12" id="error-message-{{$comment->id}}" style="display: none">
                                                                <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <form action="{{route('comments.update',$comment->id)}}" id="edit-comment-form-{{$comment->id}}" method="POST" class="container" enctype="multipart/form-data">

                                                            @csrf
                                                            @method('put')

                                                            <!-- Post Desc -->
                                                                <div class="post-desc d-flex justify-content-center mt-2">
                                                                              <textarea onfocus="mentionAdd('text-edit-{{$comment->id}}','menu-edit-comment-{{$comment->id}}')" id="text-edit-{{$comment->id}}" class="w-75" name="body" cols="200" rows="4"
                                                                                        placeholder="Start Typing..." >
                                                                                  @if($comment->mentions != null)
                                                                                      {{$comment->edit}}
                                                                                  @else
                                                                                      {{$comment->body}}
                                                                                  @endif
                                                                              </textarea>
                                                                    <div id="menu-edit-comment-{{$comment->id}}" class="menu" role="listbox"></div>
                                                                </div>

                                                                <input type="hidden" name="model_id" value="{{$post->id}}">

                                                                <div class="post-desc d-flex justify-content-center mt-2">
                                                                    <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs"/>
                                                                </div>
                                                                <!-- Add Post Btn -->
                                                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                                                    <button type="button" onclick="editCommentSubmit({{$comment->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                                        Save
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="post-edit-modal">
                                            <div class="modal fade" id="report-comment-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog" style="margin-top: 22vh">
                                                    <div class="modal-content">
                                                        <div class="modal-header d-flex justify-content-between">
                                                            <span></span>
                                                            <h5 class="modal-title" id="exampleModalLabel">Report Comment</h5>
                                                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="col-12" id="error-message-{{$comment->id}}" style="display: none">
                                                                <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$comment->id}}" role="alert">
                                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <form action="{{route('userreports.store')}}" id="report-comment-form-{{$comment->id}}" method="POST" class="container" enctype="multipart/form-data">

                                                            @csrf

                                                            <!-- Post Desc -->
                                                                <div class="post-desc d-flex justify-content-center mt-2">
                                                                              <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                                                        placeholder="Start Typing..." ></textarea>
                                                                </div>
                                                                <!-- Add Post Btn -->
                                                                <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                <input type="hidden" name="model_type" value="comment">

                                                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                                                    <button type="button" onclick="reportCommentSubmit({{$comment->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                                        Report
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </ul>
                                </div>
                                <div class="comment-option ml-auto pr-3 pt-2">
                                    <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions({{$comment->id}})"></i>
                                </div>
                            </div>
                            <button type="button" id="reply-submit-btn-{{$comment->id}}" onclick="event.preventDefault();
                                addReplySubmit({{$comment->id}},{{$post->id}})" hidden></button>
                            <div style="display: none" id="add-reply-div-{{$comment->id}}">
                                <form class="add-commnet mt-2 d-flex align-items-center" id="add-reply-form-{{$comment->id}}" onkeypress="if (event.keyCode === 13) { event.preventDefault(); $('#reply-submit-btn-{{$comment->id}}').click();}" action="{{route('comments.store')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{$post->id}}" />
                                    <input type="hidden" name="comment_id" value="{{$comment->id}}" />
                                    <input onfocus="mentionAdd('reply-text-{{$comment->id}}','menu-{{$comment->id}}')" id="reply-text-{{$comment->id}}" class="w-100 pl-2" type="text" name="body" placeholder="Add Reply" />
                                    <div id="menu-{{$comment->id}}" class="menu" role="listbox"></div>
                                    <div class="d-flex align-items-center pr-3">
                                        <i class="fas fa-paperclip" onclick="commentAttachClick({{$post->id}})"></i>
                                        <input type="file" id="comment-attach-{{$comment->id}}" name="img" accept="image/*" />
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endforeach
                @endif
                <div id="load-comments-{{$post->id}}" data-value="5">

                </div>
                <div id="added-comment-{{$post->id}}">

                </div>
                @if(isset($another_comments) && $post->comments_count > 5)
                    <p id="load-comments-message-{{$post->id}}" style="cursor: pointer" onclick="loadComments({{$post->id}})">load more comments</p>
                @endif
            </div>
            <button type="button" id="comment-submit-btn-{{$post->id}}" onclick="event.preventDefault();
                addCommentSubmit({{$post->id}})" hidden></button>
            <form class="add-commnet mt-2 d-flex align-items-center" onkeypress="if (event.keyCode === 13) { event.preventDefault(); $('#comment-submit-btn-{{$post->id}}').click();}" id="add-comment-form-{{$post->id}}" action="{{route('comments.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="post_id" value="{{$post->id}}" />
                <input onfocus="mentionAdd('reply-text-{{$post->id}}','menu-{{$post->id}}')" id="reply-text-{{$post->id}}" class="w-100 pl-2" type="text" name="body" placeholder="Add Your Comment" />
                <div id="menu-{{$post->id}}" class="menu" role="listbox"></div>
                <div class="d-flex align-items-center pr-3">
                    <i class="fas fa-paperclip" onclick="commentAttachClick({{$post->id}})"></i>
                    <input type="file" id="comment-attach-{{$post->id}}" name="img" accept="image/*" />
                </div>
            </form>
            <div class="post-advertise-modal">
                <div class="modal fade" id="advertise-post-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 10vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Advertise Post
                                </h5>
                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body pl-5 pr-5">
                                <form action="{{route('sponsor')}}" id="sponsor-post-form-{{$post->id}}" class="container" method="POST">
                                    @csrf
                                    <p>Select Duration:</p>
                                    @foreach($times as $time)
                                        <div class="form-group form-check mb-1">
                                            <input id="time-{{$post->id}}" data-value="{{$time->price}}" type="radio" name="timeId" onclick="getPrice({{$post->id}})" value="{{$time->duration}}" class="form-check-input"/>
                                            <label class="form-check-label" for="exampleCheck1">{{$time->duration}} days</label>
                                        </div>
                                        <hr class="m-1">
                                    @endforeach

                                    <p>Select Audience:</p>
                                    @foreach($reaches as $reach)
                                        <div class="form-group form-check mb-1">
                                            <input id="reach-{{$post->id}}" type="radio" data-value="{{$reach->price}}" onclick="getPrice({{$post->id}})" name="reachId" value="{{$reach->id}}" class="form-check-input"/>
                                            <label class="form-check-label" for="exampleCheck1">{{$reach->reach}} persons</label>
                                        </div>
                                        <hr class="m-1">
                                    @endforeach
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="exampleInputEmail1">Target Audience:</label>
                                        <select name="gender">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="exampleInputEmail1">Target Age:</label>
                                        <select name="age_id">
                                            @foreach($ages as $age)
                                                <option value="{{$age->id}}">From {{$age->from}} To {{$age->to}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="exampleInputEmail1">Target City:</label>
                                        <select name="city_id">
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="exampleInputEmail1">Target Country:</label>
                                        <select name="country_id">
                                            @foreach($countries as $country)
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Select Service Price -->
                                    <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                        <input name="price" id="sponsored-post-price-{{$post->id}}" class="w-100 border" type="number" placeholder="Price $" readonly/>
                                    </div>
                                    <input type="hidden" name="postId" value="{{$post->id}}">
                                    <button onclick="sponsorPost({{$post->id}})" class="btn btn-warning btn-block" data-dismiss="modal">
                                        Sponsor
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal" id="payment-modal-{{$post->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">Payment Modal</h5>
                            <button type="button" id="success-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="col-12" id="error-message-{{$post->id}}" style="display: none">
                                <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            let's pay
                            <form action="{{route('sponsor.payment')}}" id="sponsor-payment-form-{{$post->id}}" method="POST" class="container" enctype="multipart/form-data">

                            @csrf
                            <!-- Post Desc -->
                                <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                    <input id="payment-text-box-{{$post->id}}" class="w-100 border" type="number" placeholder="Price $" readonly/>
                                </div>

                                <!-- Add Post Btn -->
                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                    <button type="button" onclick="sponsorPaymentSubmit({{$post->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                        Pay Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="post-edit-modal">
                <div class="modal fade" id="edit-post-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">Edit Post</h5>
                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12" id="error-message-{{$post->id}}" style="display: none">
                                    <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{route('posts.update',$post->id)}}" id="edit-post-form-{{$post->id}}" method="POST" class="container" enctype="multipart/form-data">

                                @csrf
                                @method('put')

                                <!-- Select post Privacy -->
                                    <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                        <label for="cars">Choose Post Privacy:</label>
                                        <select id="post-privacy" name="privacy_id">
                                            @foreach($privacy as $postprivacy)
                                                @if(App::getlocale() == 'en')
                                                    <option value="{{$postprivacy->id}}" @if($postprivacy->id == $post->privacyId) selected @endif>{{$postprivacy->name_en}}</option>
                                                @else
                                                    <option value="{{$postprivacy->id}}" @if($postprivacy->id == $post->privacyId) selected @endif>{{$postprivacy->name_ar}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Select Service Category -->
                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                        <label for="cars">Choose A Category:</label>
                                        <select id="post-category" name="category_id">
                                            @foreach($categories as $category)
                                                @if(App::getlocale() == 'en')
                                                    <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_en}}</option>
                                                @else
                                                    <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_ar}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                        <label>Tag friends:</label>
                                        <select style="width: 200px" class="js-example-basic-multiple" name="tags[]" multiple="multiple">
                                            @foreach($friends_info as $friend)
                                                <option value="{{$friend->id}}" @if($post->tags != null) @if(in_array($friend->id,$post->tags_ids)) selected @endif @endif>{{$friend->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Post Desc -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                              <textarea onfocus="mentionAdd('textarea-edit-{{$post->id}}','menu-edit-{{$post->id}}')" class="w-75" name="body" id="textarea-edit-{{$post->id}}" cols="200" rows="4" placeholder="Start Typing..."
                                              >@if($post->mentions != null) {{$post->edit}}@else{{$post->body}}@endif</textarea>
                                        <div id="menu-edit-{{$post->id}}" class="menu" role="listbox"></div>
                                    </div>

                                @if($post->type == "post")
                                    <!-- Post Images -->
                                        <div class="post-desc d-flex justify-content-center mt-2">
                                            <input class="form-control w-75 mt-2" type="file" accept=".mpeg,.ogg,.mp4,.webm,.3gp,.mov,.flv,.avi,.wmv,.ts,.jpg,.jpeg,.png,.svg,.gif" name="media[]" id="imgs"
                                                   multiple />
                                        </div>

                                        @if(count($post->media) > 0)
                                            <p>Media</p>
                                            <div class="imgsContainer d-flex flex-wrap">
                                            @foreach($post->media as $media)
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

                                @endif
                                <!-- Add Post Btn -->
                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" onclick="editPostSubmit({{$post->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endforeach


@endsection
