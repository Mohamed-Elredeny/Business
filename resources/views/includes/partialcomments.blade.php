
@section('home_comments')
    @foreach($comments as $comment)
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
                                    <span class="like-details" id="like-details-{{$comment->id}}">You @if($comment->likes->count-1 != 0) and {{$comment->likes->count-1}} @if($comment->likes->count-1 > 1000) k @endif others @endif</span>
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
                                                                                    <span>
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
                                    <span class="like-details" id="like-details-{{$comment->id}}">@if($comment->likes->count-1 > 0) and {{$comment->likes->count-1}} @if($comment->likes->count-1 > 1000) k @endif others @endif</span>
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
                                                                    <span class="like-details" id="like-details-{{$reply->id}}">You @if($reply->likes->count-1 != 0) and {{$reply->likes->count-1}} @if($reply->likes->count-1 > 1000) k @endif others @endif</span>
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
                                                                                                                <span>
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
                                                                    <span class="like-details" id="like-details-{{$reply->id}}">@if($reply->likes->count-1 > 0) and {{$reply->likes->count-1}} @if($reply->likes->count-1 > 1000) k @endif others @endif</span>
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
            addReplySubmit({{$comment->id}})" hidden></button>
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
    @endforeach
    @if(!isset($another_comments))
        <p id="stop-load-comments-message-{{$post->id}}" style="display: none">end of comments</p>
    @endif
@endsection
