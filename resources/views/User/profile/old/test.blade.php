<div class="modal-body myULxx" >
    <section class="group-section " style="min-height:auto">
        <div class="group-members my-3">
            <ul class="members-list list-unstyled" >
                @if(count($followings)  > 0)
                    @foreach($followings as $following)
                        <li class="members-item" id="">
                            <div class="group-member d-flex justify-content-between">
                                <a href="#" class="group-member-link d-flex align-items-center">
                                    <img src="{{asset('assets/images/users/'.$following->following->personal_image)}}" alt="#" class="member-img img-fluid">
                                    <span class="d-inline-block group-member-link_span">
                                              <p class="user-name">{{$following->following->name}}</p>
                                            </span>
                                </a>
                                <div>
                                    @if(count(\App\Models\Following::where('followerId',$profileId->id)->where('followingId',$following->following->id)->get()) > 0)
                                        <button class="button-4 totyAdmin" id="">Un Follow</button>
                                    @endif
                                    @if(CheckUserFriendshipState($profileId->id,$following->following->id) == 'guest' )
                                        <button class="button-4 totyAdmin" id="add{{$following->id}}">Add Friend </button>
                                        <input type="hidden" id="receiver{{$following->id}}" value="{{$following->following->id}}">
                                    @elseif(CheckUserFriendshipState($profileId->id,$following->following->id) == 'pending' )
                                        <input type="hidden" id="friendshipId{{$following->id}}" value="{{friendshipId($profileId->id,$following->following->id)}}">
                                        <button class="button-4 totyAdmin" id="cancel{{$following->id}}">Cancel Request </button>
                                    @elseif(CheckUserFriendshipState($profileId->id,$following->following->id) == 'cancel' )
                                        <input type="hidden" id="friendshipId{{$following->id}}" value="{{friendshipId($profileId->id,$following->following->id)}}">
                                        <button class="button-4 totyAdmin" id="refuse{{$following->id}}">Refuse Request </button>
                                        <button class="button-4 totyAdmin" id="accept{{$following->id}}">Accept Request </button>
                                    @elseif(CheckUserFriendshipState($profileId->id,$following->following->id) == 'accepted' )
                                        <input type="hidden" id="friendshipId{{$following->id}}" value="{{friendshipId($profileId->id,$following->following->id)}}">
                                        <button class="button-4 totyAdmin" id="refuse{{$following->id}}">Remove Friend </button>
                                    @endif
                                    <button class="button-4 totyAdmin" id="">Add Friend</button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                @else
                    <center>
                        <h2>There is no followings yet!</h2>

                    </center>
                @endif
            </ul>
        </div>
    </section>
</div>
