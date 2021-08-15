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
                                      <img class="img-fluid" alt="{{ $company->name }}" src="{{ asset("public/assets/images/companies/" . $company->image) }}">
                                  </div>
                                  <div class="col-md-8">
                                      <p>
                                          <strong>{{ __("company_name") }}: </strong>
                                          <span>{{ $company->name }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("company_details") }}: </strong>
                                          <span>{{ $company->details }}</span>
                                      </p>

                                      <p>
                                          <strong>{{ __("company_phones") }}: </strong>
                                          <span>
                                              @foreach($company->phone as $phone)
                                                  {{ $phone->phoneNumber }},
                                              @endforeach
                                          </span>
                                      </p>

                                      <p>
                                          <strong>{{ __("company_state") }}: </strong>
                                          <span>{{ $company->state }}</span>
                                      </p>

                                      <p>
                                          <a class="btn btn-primary" href="{{ route("companies.edit", $company->id) }}">Edit</a>
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

