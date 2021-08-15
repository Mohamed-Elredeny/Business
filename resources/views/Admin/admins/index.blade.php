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
                                        <th>{{ __("admin_name") }}</th>
                                        <th>{{ __("admin_email") }}</th>
                                        <th>{{ __("state") }}</th>
                                        <th>{{ __("manage") }}</th>
                                    </tr>
                                    </thead>

                                    @foreach($admins as $i => $admin)
                                        <tbody>
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td><a href="{{ route("admins.show", $admin->id) }}" class="text-dark">{{ $admin->name }}</a></td>
                                            <td>{{ $admin->email }}</td>
                                            <td>{{ $admin->state }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="drop-down-button">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                        <form action="{{ route('admins.destroy', $admin->id) }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to delete this vendor?") }}') ? this.parentElement.submit() : ''">{{ __('delete') }}</button>
                                                        </form>
                                                        <a class="dropdown-item" href="{{ route('admins.edit', $admin->id) }}">{{ __('edit') }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                                <a class="btn btn-success" href="{{ route('admins.create') }}">Add</a>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
    </div>
@endsection
