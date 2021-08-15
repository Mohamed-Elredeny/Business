@extends('layouts.app')
@section('content')

        <section
            id="ez-body__center-content"
            class="col-lg-10 row profile-center-content"
        >
            <div class="col-lg-12 profile-center-content row">
                <div class="col-sm-3 pt-2">
                    <center>
                        <img style="height:200px;width: 80%;border-radius: 20%" src="{{$profileId->personal_image}}" alt="">
                        <br>
                        <h3>{{$profileId->name}}</h3>
                        <h4>
                            <i class="fa fa-city"></i>
                            {{$profileId->country}}
                        </h4>
                        <br>
                       <div class="row text-left" style="background: #fcbc22;color: black;font-weight: bolder;padding: 10px;border-radius: 10%">
                           <div class="col-sm-7">
                               <i class="fa fa-city"></i>
                               Job Title
                           </div>
                           <div class="col-sm-3">
                               {{$profileId->jobTitle}}
                           </div>
                           <div class="col-sm-7">
                               <i class="fa fa-city"></i>
                               Specialization
                           </div>
                           <div class="col-sm-3">
                               {{$profileId->jobTitle}}
                           </div>
                           <div class="col-sm-7">
                               <i class="fa fa-city"></i>
                               Business
                           </div>
                           <div class="col-sm-3">
                               {{$profileId->jobTitle}}
                           </div>
                           <div class="col-sm-7">
                               <i class="fa fa-city"></i>
                               Friends
                           </div>
                           <div class="col-sm-3">
                               <a href="">  {{count($friends)}}</a><br>
                           </div>

                           <div class="col-sm-7 ">
                               <i class="fas fa-headphones pr-2"></i>Musics<br>
                               <i class="fas fa-film pr-2"></i>Films<br>
                               <i class="fas fa-camera-retro pr-2"></i>Images<br>
                               <i class="fas fa-dumbbell pr-2"></i>Sports<br>
                               <i class="far fa-lightbulb pr-2"></i>Interests<br>
                               <i class="fas fa-gamepad pr-2"></i>Hippies<br>
                           </div>
                           <div class="col-sm-3 ">
                               <a href="">2</a><br>
                               <a href="">2</a><br>
                               <a href="">2</a><br>
                               <a href="">2</a><br>
                               <a href="">2</a><br>
                               <a href="">2</a><br>
                           </div>

                           <div class="col-sm-4 text-center">
                               <a href="" class="btn btn-dark">
                                   View Friends
                               </a>
                           </div>
                           {{--Following--}}
                           @if($following_state == 1)
                           <div class="col-sm-4 text-center">
                               <a href="" class="btn btn-dark">
                                   Un Follow
                               </a>
                           </div>
                           @else
                           <div class="col-sm-4 text-center">
                               <a href="" class="btn btn-dark">
                                   Follow
                               </a>
                           </div>
                           @endif
                           {{--End of Following--}}

                           {{--Friendship--}}
                           @if($firendship_state == 1)
                           <div class="col-sm-4 text-center">
                               <a href="" class="btn btn-dark">
                                   Add Friend
                               </a>
                           </div>
                           @endif
                           {{--End of Firendship--}}
                           @if($myProfile == 1)
                               <div class="col-sm-4 text-center">
                                   <a href="" class="btn btn-dark">
                                       Edit Profile
                                   </a>
                               </div>
                           @endif
                           <br><br>



                       </div>



                    </center>
                </div>
                <div class="col-sm-9">
                    <div class="profile-cover">
                        <img
                            src="{{Auth::user()->cover_image}}"
                            alt=""
                            width="100%"
                            height="400px"
                        />
                    </div>
                    <div
                        class="add-post-container d-flex justify-content-between align-items-center"
                    >
                        <div class="add-post mt-3" style="width: 85%">
                            <!-- Add Post Modal -->
                            <div
                                class="modal fade"
                                id="add-post-modal"
                                tabindex="-1"
                                aria-labelledby="exampleModalLabel"
                                aria-hidden="true"
                            >
                                <div class="modal-dialog" style="margin-top: 22vh">
                                    <div class="modal-content">
                                        <div class="modal-header d-flex justify-content-between">
                                            <span></span>
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                Add Post
                                            </h5>
                                            <button
                                                type="button"
                                                class="close ml-0"
                                                data-dismiss="modal"
                                                aria-label="Close"
                                            >
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form
                                                action=""
                                                class="container"
                                                enctype="multipart/form-data"
                                            >
                                                <!-- Select Post Type -->
                                                <div
                                                    class="post-type d-flex justify-content-between align-items-center m-auto w-75"
                                                >
                                                    <div>Post As:</div>
                                                    <div class="d-flex align-items-center">
                                                        <input
                                                            type="radio"
                                                            name="post-type"
                                                            value="post"
                                                            id="post"
                                                        />
                                                        <span class="pl-2">Post</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <input
                                                            class="m-0"
                                                            type="radio"
                                                            name="post-type"
                                                            value="service"
                                                            id="service"
                                                        />
                                                        <span class="pl-2">Service</span>
                                                    </div>
                                                </div>
                                                <!-- Select post Privacy -->
                                                <div
                                                    class="post-privacy d-flex justify-content-between align-items-center m-auto w-75"
                                                >
                                                    <label for="cars">Choose Post Privacy:</label>
                                                    <select id="post-privacy" name="privacy">
                                                        <option value="volvo">Volvo</option>
                                                        <option value="saab">Saab</option>
                                                        <option value="fiat">Fiat</option>
                                                        <option value="audi">Audi</option>
                                                    </select>
                                                </div>
                                                <!-- Select post Category -->
                                                <div
                                                    class="post-category d-flex justify-content-between align-items-center m-auto w-75"
                                                >
                                                    <label for="cars">Choose A Category:</label>
                                                    <select id="post-category" name="category">
                                                        <option value="volvo">Volvo</option>
                                                        <option value="saab">Saab</option>
                                                        <option value="fiat">Fiat</option>
                                                        <option value="audi">Audi</option>
                                                    </select>
                                                </div>
                                                <!-- Post Desc -->
                                                <div
                                                    class="post-desc d-flex justify-content-center mt-2"
                                                >
                            <textarea
                                name="post-text"
                                id="post-text"
                                cols="200"
                                rows="4"
                                placeholder="Start Typing..."
                            ></textarea>
                                                </div>
                                                <!-- Post Images -->
                                                <div
                                                    class="post-desc d-flex justify-content-center mt-2"
                                                >
                                                    <input
                                                        class="form-control w-75 mt-2"
                                                        type="file"
                                                        name="imgs"
                                                        id="imgs"
                                                        accept="image/*"
                                                        multiple
                                                    />
                                                </div>
                                                <!-- Add Post Btn -->
                                                <div
                                                    class="post-add-btn d-flex justify-content-center mt-4"
                                                >
                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary btn-block w-75"
                                                        data-dismiss="modal"
                                                    >
                                                        Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input
                                type="text"
                                placeholder="Add New Post"
                                class="w-100"
                                data-toggle="modal"
                                data-target="#add-post-modal"
                            />
                        </div>
                        <div
                            class="settings d-flex justify-content-center align-items-center mt-3"
                            data-toggle="modal"
                            data-target="#settings-modal"
                        >
                            <i class="fas fa-cogs"></i>
                            <div
                                class="modal fade"
                                id="settings-modal"
                                tabindex="-1"
                                aria-labelledby="exampleModalLabel"
                                aria-hidden="true"
                            >
                                <div class="modal-dialog" style="margin-top: 22vh">
                                    <div class="modal-content">
                                        <div class="modal-header d-flex justify-content-between">
                                            <span></span>
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                Settings
                                            </h5>
                                            <button
                                                type="button"
                                                class="close ml-0"
                                                data-dismiss="modal"
                                                aria-label="Close"
                                            >
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form
                                                action=""
                                                class="container d-flex flex-wrap"
                                                enctype="multipart/form-data"
                                            >
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"
                                                    >Email address</label
                                                    >
                                                    <input
                                                        type="email"
                                                        class="form-control w-100"
                                                        id="exampleInputEmail1"
                                                        aria-describedby="emailHelp"
                                                    />
                                                    >
                                                </div>
                                                <input class="w-100" type="number" />
                                                <input class="w-100" type="password" />
                                                <input class="w-100" type="password" />
                                                <input class="w-100" type="text" />
                                                <!-- Add Post Btn -->
                                                <div
                                                    class="post-add-btn d-flex justify-content-center mt-4"
                                                >
                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary btn-block w-75"
                                                        data-dismiss="modal"
                                                    >
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
                    <div class="section-navigator d-flex justify-content-between mt-3">
                        <button class="btn btn-warning">Follow</button>
                        <button class="btn btn-light">UnFollow</button>
                    </div>
                    <div class="section-navigator d-flex justify-content-between mt-3">
                        <button class="btn btn-warning">posts</button>
                        <button class="btn btn-light">Services</button>
                    </div>
                    <div class="people p-2 mt-2">
                        <div class="people-info d-flex">
                            <img
                                class="profile-figure rounded-circle"
                                src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                alt="User Profile Pic"
                            />
                            <div class="d-flex flex-column align-items-center pl-3">
                                <p><b>Eslam Magdy</b></p>
                                <p>3k Follower</p>
                            </div>
                        </div>
                        <button class="btn btn-warning text-white">Follow</button>
                    </div>
                    <div id="app">
                        <post-component
                            v-for="post in posts"
                            :data="post"
                        ></post-component>
                    </div>
                </div>
            </div>
        </section>

@endsection
