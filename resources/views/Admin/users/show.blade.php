@extends('layouts.admin_layout')

@section('content')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                              <div class="row">
                                  <div class="col-md-4">
                                      <img class="img-fluid" alt="{{ $user->name }}" src="{{ asset("public/assets/images/users/" . $user->image) }}">
                                  </div>
                                  <div class="col-md-8">
                                      <p>
                                          <strong>{{ __("user_name") }}: </strong>
                                          <span>{{ $user->name }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_email") }}: </strong>
                                          <span>{{ $user->email }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_birthdate") }}: </strong>
                                          <span>{{ $user->birthDate }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_type") }}: </strong>
                                          <span>{{ $user->type }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_phone") }}: </strong>
                                          <span>{{ $user->name }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_jobTitle") }}: </strong>
                                          <span>{{ $user->jobTitle }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_country") }}: </strong>
                                          <span>{{ $user->country }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_city") }}: </strong>
                                          <span>{{ $user->city }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_gender") }}: </strong>
                                          <span>{{ $user->gender }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("user_state") }}: </strong>
                                          <span>{{ $user->state }}</span>
                                      </p>

                                      <p>
                                          <a class="btn btn-primary" href="{{ route("users.edit", $user->id) }}">Edit</a>
                                      </p>

                                  </div>
                              </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
@endsection

