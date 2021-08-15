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
									<div class="col-md-8">
										<p>
											<strong>{{ __("admin_name") }}: </strong>
											<span>{{ $admin->name }}</span>
										</p>

										<p>
											<strong>{{ __("admin_email") }}: </strong>
											<span>{{ $admin->email }}</span>
										</p>

										<p>
											<strong>{{ __("admin_phone") }}: </strong>
											<span>{{ $admin->name }}</span>
										</p>

										<p>
											<strong>{{ __("admin_state") }}: </strong>
											<span>{{ $admin->state }}</span>
										</p>

										<p>
											<a class="btn btn-primary" href="{{ route("admins.edit", $admin->id) }}">Edit</a>
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

