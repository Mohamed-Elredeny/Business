
@section('unliked')
    <span class="reaction-btn">
        <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$model->id}}" style="display: none"></span>
        <!-- Default like button emotion-->
        <span class="reaction-btn-text" id="reaction-btn-text-{{$model->id}}">
            <div><i class="far fa-thumbs-up"></i>
                @if($model->likes->count > 0)
                    <span data-toggle="modal" data-target="#likes-modal-{{$model->id}}">
                        {{$model->likes->count}}
                    </span>
                @endif
            </div>
        </span>
        <!-- Default like button text,(Like, wow, sad..) default:Like  -->
        <ul class="emojies-box">
            @foreach($reacts as $react)
                <!-- Reaction buttons container-->
                    <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$model->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                    <form id="like-form-{{$model->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="hidden" name="model_id" value="{{$model->id}}">
                <input type="hidden" name="model_type" value="{{$model_type}}">
                <input type="hidden" name="reactId" value="{{$react->id}}">
            </form>
                @endforeach
        </ul>
    </span>
    <div class="like-stat" id="like-stat-{{$model->id}}" style="display: none">
        <!-- Like statistic container-->
        <span class="like-emo" id="like-emo-{{$model->id}}">
                                              <!-- like emotions container -->
                                              <span class="like-btn-like"></span>
            <!-- given emotions like, wow, sad (default:Like) -->
                                            </span>
        <span class="like-details" id="like-details-{{$model->id}}" data-toggle="modal" data-target="#likes-modal-{{$model->id}}">@if($model->likes->count-1 > 0) and {{$model->likes->count-1}} @if($model->likes->count-1 > 1000) k @endif others @endif</span>
    </div>
@endsection
