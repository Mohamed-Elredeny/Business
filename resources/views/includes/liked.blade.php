
@section('liked')
    <span class="reaction-btn">
        <!-- Default like button -->
        <span class="reaction-btn-emo like-btn-{{$model->user_react[0]->name}}" id="reaction-btn-emo-{{$model->id}}"></span>
    <!-- Default like button emotion-->
        <span class="reaction-btn-text reaction-btn-text-{{$model->user_react[0]->name}} active" onclick="unlikeModelSubmit({{$model->id}},{{$model->user_react[0]->id}})" id="reaction-btn-text-{{$model->id}}">
            {{$model->user_react[0]->name}}
                <form id="unlike-form-{{$model->id}}-{{$model->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="hidden" name="model_id" value="{{$model->id}}">
                    <input type="hidden" name="model_type" value="{{$model_type}}">
                    <input type="hidden" name="reactId" value="{{$model->user_react[0]->id}}">
                   <input type="hidden" name="requestType" id="like-request-type-{{$model->id}}" value="delete">
                </form>
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
                   <input type="hidden" name="requestType" id="like-request-type-{{$model->id}}" value="update">
                </form>
                @endforeach
        </ul>
  </span>
    <div class="like-stat">
        <!-- Like statistic container-->
        <span class="like-emo" id="like-emo-{{$model->id}}">
                                              <!-- like emotions container -->
                                              <span class="like-btn-like"></span>
                                                @if($model->user_react[0]->name != "like")
                <span class="like-btn-{{$model->user_react[0]->name}}"></span>
        @endif
        <!-- given emotions like, wow, sad (default:Like) -->
                                            </span>
        <span class="like-details" id="like-details-{{$model->id}}" data-toggle="modal" data-target="#likes-modal-{{$model->id}}">You @if($model->likes->count-1 != 0) and {{$model->likes->count-1}} @if($model->likes->count-1 > 1000) k @endif others @endif</span>
    </div>
@endsection
