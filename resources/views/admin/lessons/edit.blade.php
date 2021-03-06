@extends('admin.layouts.default')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{__('layouts/header.lesson')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                    href="{{route('admin.home')}}">{{__('layouts/header.home')}}</a></li>
                            <li class="breadcrumb-item active">{{__('layouts/header.lesson')}}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <form action="{{route('admin.lessons.update', $lesson->id)}}" method="post"
                      enctype="multipart/form-data">
                    {{--                <form action="{{route('admin.lesson.store')}}" method="post" class="needs-validation" novalidate>--}}
                    {{csrf_field()}}
                    @method('PUT')
                    <div class="form-group">
                        <label for="name" class="required">{{__('lesson.name')}}</label>
                        <input type="text" class="form-control" id="name" name="name"
                               placeholder="{{__('lesson.enterName')}}" value="{{$lesson->name}}">
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="abstract">{{__('lesson.abstract')}}</label>
                        <textarea name="abstract" id="abstract" rows="5" class="form-control"
                                  placeholder="{{__('lesson.enterAbstract')}}">{{$lesson->abstract}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">{{__('lesson.description')}}</label>
                        <textarea name="description" id="description" rows="5" class="form-control"
                                  placeholder="{{__('lesson.enterDescription')}}">{{$lesson->description}}</textarea>
                    </div>
                    {{--                <div class="custom-file mb-3">--}}
                    {{--                    <input type="file" class="custom-file-input" id="customFile" name="file">--}}
                    {{--                    <label id="fileLabel" class="custom-file-label" for="customFile">{{__('lesson.chooseFile')}}</label>--}}
                    {{--                </div>--}}
                    <button id="createBtn" class="btn btn-primary" type="submit">{{__('lesson.update')}}</button>
                    <a href="{{route('admin.lessons.index')}}" class="btn btn-secondary">{{__('lesson.back')}}</a>
                </form>
            </div>
        </div>
        @endsection

        @section('js')
            <script type="text/javascript">
                // Example starter JavaScript for disabling form submissions if there are invalid fields
                (function () {
                    'use strict';
                    window.addEventListener('load', function () {
                        // Fetch all the forms we want to apply custom Bootstrap validation styles to
                        var forms = document.getElementsByClassName('needs-validation');
                        // Loop over them and prevent submission
                        var validation = Array.prototype.filter.call(forms, function (form) {
                            form.addEventListener('submit', function (event) {
                                if (form.checkValidity() === false) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                }
                                form.classList.add('was-validated');
                            }, false);
                        });
                    }, false);
                })();

                function showAlert(message, header, status) {
                    toastr[status](message, header);
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    }
                }

                @if (session('success'))
                    this.showAlert("{{session('success')}}", "Thành công", "success");
                @elseif (session()->get('errors'))
                    this.showAlert("{{ session()->get('errors')->first() }}", "Lỗi", "error");
                @endif

                $("#createBtn").click(function () {
                    $(this).prop('disable', true);
                    $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>\n' +
                        '  Loading...');
                });

                $("input[name='file']").on('change', function () {
                    $("#fileLabel").html($("input[name='file']")[0].files[0].name);
                });
            </script>
@endsection
