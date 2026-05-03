@php
    $links = [
        [
            'title' => 'Users',
            'route' => '',
            'active' => true,
        ],
    ];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Users
@endsection
@section('page-style')
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/web-assets/backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection
@section('page-content')
    <x-admin-page-header title="Users" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __tr('User') }}</h3>
                            <button class="btn btn-success btn-sm float-right text-white" data-toggle="modal"
                                data-target="#user-create-modal">{{ __tr('Add New User') }}</button>
                        </div>
                        <div class="card-body">
                            <table id="userTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __tr('ID') }}</th>
                                        <th>{{ __tr('Image') }}</th>
                                        <th>{{ __tr('Name') }}</th>
                                        <th>{{ __tr('Email') }}</th>
                                        <th>{{ __tr('Role') }}</th>
                                        <th>{{ __tr('Status') }}</th>
                                        <th class="text-right">{{ __tr('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <img src="{{ asset(getFilePath($user->image)) }}"
                                                    alt="{{ $user->name }}" class="img-circle img-md" />
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @foreach ($user->getRoleNames() as $role)
                                                    {{ $role }}
                                                @endforeach
                                            </td>
                                            <td>
                                                @if ($user->status == config('settings.general_status.active'))
                                                    <p class=" badge badge-success">{{ __tr('Active') }}</p>
                                                @else
                                                    <p class=" badge badge-danger">{{ __tr('Inactive') }}</p>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default">{{ __tr('Action') }}
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-default dropdown-toggle dropdown-hover dropdown-icon"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <button class="dropdown-item edit-user"
                                                            data-id="{{ $user->id }}">
                                                            {{ __tr('Edit') }}
                                                        </button>
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item delete-user"
                                                            data-id="{{ $user->id }}">
                                                            {{ __tr('Delete') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--New User Modal-->
        <div class="modal fade" id="user-create-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('New User') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="new-user-form">
                            @csrf
                            <div class="form-group">
                                <label>{{ __tr('Name') }}</label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="{{ __tr('Enter Name') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Email') }}</label>
                                <input type="email" class="form-control" name="email"
                                    placeholder="{{ __tr('Enter Email') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Password') }}</label>
                                <input type="password" class="form-control" name="password"
                                    placeholder="{{ __tr('Enter Password') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Confirm Password') }}</label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    placeholder="{{ __tr('Re Enter Password') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Image') }}</label>
                                <x-media name="image" value=""></x-media>
                            </div>
                            <div class="form-group">
                                <label>{{ __tr('Role') }}</label>
                                <select name="role" class="form-control">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        <div class="d-flex justify-content-between">
                            <button type="button"
                                class="btn btn-primary create-new-user-btn">{{ __tr('Save User') }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--End New User Modal-->
        <!--User Edit Modal-->
        <div class="modal fade" id="user-edit-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __tr('User Information') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body user-edit-content">

                    </div>
                </div>
            </div>
        </div>
        <!--End User Edit Modal-->
        <!--User Delete Modal-->
        <div class="modal fade" id="user-delete-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title h6">{{ __tr('Delete Confirmation') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h4 class="mt-1 h6 my-2">{{ __tr('Are you sure to delete user ?') }}</h4>
                        <form method="POST" action="{{ route('admin.users.delete') }}">
                            @csrf
                            <input type="hidden" id="delete-user-id" name="id">
                            <button type="button" class="btn mt-2 btn-danger"
                                data-dismiss="modal">{{ __tr('Cancel') }}</button>
                            <button type="submit" class="btn btn-success mt-2">{{ __tr('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End User Delete Modal-->
    </section>
@endsection
@section('page-script')
    <script src="{{ asset('public/web-assets/backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/web-assets/backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/web-assets/backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script>
        (function($) {
            "use strict";
            initMediaManager();
            //data table
            $('#userTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            //Create new user 
            $('.create-new-user-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-user-form').serialize(),
                    url: '{{ route('admin.users.store') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('New user created successfully', 'Success');
                            $('#user-create-modal').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(response.message, 'Error')
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').after(
                                    '<div class="error text-danger mb-0 invalid-input">' +
                                    error + '</div>');
                            })
                        } else {
                            toastr.error('User create failed', 'Error');
                        }
                    }
                });
            });

            //Visible user delete modal
            $('.delete-user').on('click', function(e) {
                e.preventDefault();
                let user_id = $(this).data('id');
                $('#delete-user-id').val(user_id);
                $('#user-delete-modal').modal('show');
            });
            //Edit user form
            $('.edit-user').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        id: id
                    },
                    url: '{{ route('admin.users.edit') }}',
                    success: function(response) {
                        if (response.success) {
                            $('.user-edit-content').html(response.data);
                            $("#user-edit-modal").modal('show');
                        } else {
                            toastr.error('User not found', 'Error');
                        }
                    },
                    error: function(response) {
                        toastr.error('User not found', 'Error');
                    }
                });
            })
        })(jQuery);
    </script>
@endsection
