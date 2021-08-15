@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <div class="col-12">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Reports</h3>
                            </div>
                            <div class="card-body">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                    @if(count($reports) > 0)
                                        <thead>
                                            <tr>
                                                <th>body</th>
                                                <th>State</th>
                                                <th>Source</th>
                                                <th>user</th>
                                                <th>show info</th>
                                                <th>controls</th>
                                            </tr>
                                        </thead>
                                        @foreach($reports as $report)
                                            <tbody>
                                                <tr>
                                                    <td>{{$report->body}}</td>
                                                    <th>
                                                        {{$report->state}}
                                                    </th>
                                                    <th>
                                                        {{ $report->model_type }}
                                                    </th>
                                                    <th>
                                                        {{ $report->user_name }}
                                                    </th>
                                                    <td>
                                                        <a type="button" class="btn btn-info"href="{{route('reports.showinfo',$report->id)}}">{{ __('Show') }}</a>
                                                    </td>
                                                    <td>

                                                        <form action="{{ route('reports.update', $report->id) }}" method="post" style="display: inline; margin-right: 5px">
                                                            @csrf
                                                            @method('put')

                                                            <input type="hidden" name="state" value="accepted">
                                                            <button type="button" class="btn btn-success" onclick="confirm('{{ __("Are you sure you want to accept this report?") }}') ? this.parentElement.submit() : ''">{{ __('Accept') }}</button>

                                                        </form>

                                                        <form action="{{ route('reports.update', $report->id) }}" method="post" style="display: inline; margin-right: 5px">
                                                            @csrf
                                                            @method('put')

                                                            <input type="hidden" name="state" value="refused">
                                                            <button type="button" class="btn btn-primary" onclick="confirm('{{ __("Are you sure you want to refuse this report?") }}') ? this.parentElement.submit() : ''">{{ __('Refuse') }}</button>

                                                        </form>
                                                    </td>
                                                </tr>
                                                </tbody>
                                        @endforeach
                                    @else
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <center>
                                                        <h3>There is no reports  yet!</h3>
                                                    </center>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endif
                                </table>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->



            </div> <!-- container-fluid -->
        </div>

@endsection
