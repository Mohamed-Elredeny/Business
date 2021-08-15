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
                                        <th>{{ __("image") }}</th>
                                        <th>{{ __("user_name") }}</th>
                                        <th>{{ __("user_email") }}</th>
                                        <th>{{ __("birthData") }}</th>
                                        <th>{{ __("gender") }}</th>
                                        <th>{{ __("phone") }}</th>
                                        <th>{{ __("job_title") }}</th>
                                        <th>{{ __("city") }}</th>
                                        <th>{{ __("country") }}</th>
                                        <th>{{ __("state") }}</th>
                                        <th>{{ __("manage") }}</th>
                                    </tr>
                                    </thead>

                                    @foreach($users as $i => $user)
                                        <tbody>
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>
                                                <img class="img-fluid avatar-md rounded" alt="{{ $user->name }}" src="{{ asset("public/assets/images/users/" . $user->image) }}">
                                            </td>
                                            <td><a href="{{ route("users.show", $user->id) }}" class="text-dark">{{ $user->name }}</a></td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->birthDate }}</td>
                                            <td>{{ $user->gender }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->jobTitle }}</td>
                                            <td>{{ $user->city }}</td>
                                            <td>{{ $user->country }}</td>
                                            <td>{{ $user->state }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="drop-down-button">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                        <form action="{{ route('users.destroy', $user->id) }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to delete this vendor?") }}') ? this.parentElement.submit() : ''">{{ __('delete') }}</button>
                                                        </form>
                                                        <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">{{ __('edit') }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                                <a class="btn btn-success" href="{{ route('users.create') }}">Add</a>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
    </div>
@endsection
