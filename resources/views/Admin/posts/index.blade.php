@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                {{-- <h4 class="card-title">Default Datatable</h4>
                                <p class="card-title-desc">DataTables has most features enabled by
                                    default, so all you need to do to use it with your own tables is to call
                                    the construction function: <code>$().DataTable();</code>.
                                </p> --}}
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    @if(count($posts) != 0)

                                    <thead>
                                    <tr>
                                        <th>number</th>
                                        <th>category</th>
                                        <th>type</th>
                                        <th>privacy</th>
                                        <th>state</th>
                                        <th>pubisher</th>
                                        <th colspan="3">Controllers</th>


                                    </tr>
                                    </thead>
                                    @endif
                                    <?php $i=0?>
                                    @foreach($posts as $post)
                                        <tbody>
                                            <tr>
                                                <td>{{$i+1}}</td>
                                                <td>{{ $idsToNames[$i]['categoryId'] }}</td>
                                                <td>{{ $idsToNames[$i]['postType'] }}</td>
                                                <td>{{ $idsToNames[$i]['privacyId'] }}</td>
                                                <td>{{ $idsToNames[$i]['stateId'] }}</td>
                                                <td>{{ $idsToNames[$i]['publisherId'] }}</td>
                                                <th colspan="3">
                                                    <a href="{{ route('posts.show', $post->id) }}                                                    ">
                                                    <input type="button" class="btn btn-primary" value="Show">
                                                    </a>
                                                    <a href="{{ route('posts.edit', $post->id) }}                                                    ">
                                                        <input type="button" class="btn btn-dark" value="Edit">
                                                    </a>

                                                    <form action="{{ route('posts.destroy', $post->id) }}" method="post" style="display: inline-block">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="button" class="btn btn-danger" onclick="confirm('{{ __("Are you sure you want to delete this vendor?") }}') ? this.parentElement.submit() : ''">{{ __('delete') }}</button>
                                                    </form>


                                                </th>
                                            </tr>
                                            </tbody>
                                            <?php $i+=1 ?>
                                    @endforeach
                                    @if(count($posts) == 0)
                                    <tbody>
                                        <tr>
                                            <td colspan="3">
                                                <center>
                                                    <h3>There is no Posts yet!</h3>
                                                    <a class="btn btn-danger" href="{{ route('posts.create')}}">{{ __('add') }}</a>
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
