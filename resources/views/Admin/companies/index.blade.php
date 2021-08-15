@extends('layouts.admin_layout')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Companies</h4>
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Company Name</th>
                                        <th>Details</th>
                                        <th>Phones</th>
                                        <th>State</th>
                                        <th>Manage</th>
                                    </tr>
                                    </thead>

                                    @foreach($companies as $i => $company)
                                        <tbody>
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>
                                                <img class="img-fluid avatar-md rounded" alt="{{ $company->name }}" src="{{ asset("public/assets/images/companies/" . $company->image) }}">
                                            </td>
                                            <td><a href="{{ route("companies.show", $company->id) }}" class="text-dark">{{ $company->name }}</a></td>
                                            <td>{{ $company->details }}</td>
                                            <td>
                                                @foreach( $company->phone as $phone )
                                                    <p>{{ $phone->phoneNumber  }}</p>
                                                @endforeach
                                            </td>
                                            <td>{{$company->state}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="drop-down-button">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                        <form action="{{ route('companies.destroy', $company->id) }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to delete this vendor?") }}') ? this.parentElement.submit() : ''">{{ __('delete') }}</button>
                                                        </form>
                                                        <a class="dropdown-item" href="{{ route('companies.edit', $company->id) }}">{{ __('edit') }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                                <a class="btn btn-success" href="{{ route('companies.create') }}">Add</a>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
    </div>
@endsection
