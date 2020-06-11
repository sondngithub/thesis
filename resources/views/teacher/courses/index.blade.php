@extends('layouts.default')

@section('css')
    <!-- Bootstrap CSS -->
@endsection

@section('content')
    <div class="custom-breadcrumns border-bottom">
        <div class="container">
            <a href="{{route('index')}}">{{__('layouts/header.home')}}</a>
            <span class="mx-3 icon-keyboard_arrow_right"></span>
            <span class="current">{{__('layouts/header.uploadLesson')}}</span>
        </div>
    </div>

    <div class="site-section">
        <div class="container">
            <a href="{{route('teacher.courses.create')}}" class="btn btn-primary mb-3">{{__('course.new')}}</a>
            <div class="table-responsive small">
                <table class="table table-bordered table-hover" id="coursesTable">
                    <thead>
                    <tr>
                        <th>{{__('course.no')}}</th>
                        <th>{{__('course.name')}}</th>
                        <th>{{__('course.category')}}</th>
                        <th>{{__('course.numberLessons')}}</th>
                        <th>{{__('course.created_at')}}</th>
                        <th width="18%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="courseDetail" class="modal fade small" role="dialog">
        <div class="modal-dialog mw-75">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('course.detail')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4 label">{{__('course.name')}}</div>
                        <div class="col-md-8" id="name"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 label">{{__('course.category')}}</div>
                        <div class="col-md-8" id="course-name"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 label">{{__('course.description')}}</div>
                        <div class="col-md-8" id="description"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 label">{{__('course.link')}}</div>
                        <div class="col-md-8" id="link"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 label">{{__('course.created_at')}}</div>
                        <div class="col-md-8" id="created-at"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 label">{{__('course.lessonList')}}</div>
                        <div class="col-md-8"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"></div>
                        <div class="col-md-8">
                            <div class="table-responsive small w-75">
                                <table class="table table-bordered table-hover" id="lessonsTable">
                                    <thead>
                                    <tr>
                                        <th>{{__('course.no')}}</th>
                                        <th>{{__('lesson.name')}}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="adviseModal" class="modal fade small" role="dialog">
        <div class="modal-dialog mw-75">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('lesson.advise')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="adviseBody">
                    <div class="row mb-3">
                        <div class="col-md-12 label" id="numberEvaluation"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 label" id="adviseLable">{{__('lesson.adviseFromSystem')}}</div>
                        <div class="col-md-9" id="advise-list"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="adviseCloseBtn">Close</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    <script>
        // $(document).ready(function() {
        var table = $('#coursesTable').DataTable({
            stateSave: true,
            searching: true,
            serverSide: true,
            lengthChange: true,
            language: {
                "lengthMenu": "Hiện thị _MENU_",
                "zeroRecords": "Nothing found - sorry",
                "info": "Hiện thị trang _PAGE_ trong _PAGES_",
                "infoEmpty": "Không có bản ghi nào",
                "infoFiltered": "(Lọc trong _MAX_ bản ghi)",
                "search": "Tìm kiếm:",
                "loadingRecords": "Tải dữ liệu...",
                "processing": "Đang xử lý...",
                "zeroRecords": "Không tìm thấy kết quả",
                "paginate": {
                    "first": "Trang đầu",
                    "last": "Trang cuối",
                    "next": "Trang sau",
                    "previous": "Trang trước"
                },
            },
            order: [[ 1, 'asc' ]],
            ajax: {
                url: '{{route('teacher.datatables.courses')}}',
            },
            columns: [
                {data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'name'},
                {data: 'category.name'},
                {data: 'number_lessons'},
                {data: 'created_at'},
                {data: 'action', "searchable": false,
                    render: function (data, type, row, meta) {
                        return '<a href="/teacher/courses/' + row.id + '" class="btn btn-info btn-info-lesson" data-id="' + row.id + '" data-toggle="modal" data-target="#courseDetail" onclick="showInfoModal(this)"><i class="fa fa-info-circle"></i></a>'
                            + '<a href="/teacher/courses/' + row.id + '/edit" class="btn btn-warning margin-r-5"><i class="fa fa-edit"></i></a>'
                            + '<a href="#" class="btn btn-success margin-r-5" data-id="' + row.id + '" data-toggle="modal" data-target="#adviseModal" onclick="showAdviseModel(this)"><i class="fa fa-commenting"></i></a>'
                            + '<a href="javascript:void(0)" data-id="' + row.id + '" class="btn btn-danger btn-delete"><i class="fa fa-trash"></i></a>';
                    }
                },
            ],

        });

        function showInfoModal(btn) {
            var id = $(btn).attr("data-id");
            var url = $(btn).attr("href");
            $.ajax({
                type: "GET",
                url: url,
                success: function (data) {
                    $("#name").html(data.name);
                    $("#course-name").html(data.category.name);
                    $("#description").html(data.description);
                    $("#link").html(data.link);
                    $("#created-at").html(data.created_at);
                    $.each(data.lesson, function (index, value) {
                        index = index + 1;
                        $('#courseDetail tbody').append('<tr><td>' + index + '</td><td>' + value.name + '</td><td><a href="javascript:void(0)" data-id="' + value.id + '" class="btn btn-xs btn-danger btn-delete"><i class="fa fa-trash"></i></a></td></tr>');
                    })
                },
                error: function (data) {

                }
            });
        }

        $('#lessonsTable').on("click", '.btn-delete', function() {
            var btn = $(this);
            var id = btn.attr("data-id");
            $.confirm({
                title: '{{__('lesson.confirm')}}!',
                content: '{{__('lesson.confirmMessage')}}',
                buttons: {
                    '{{__('lesson.confirm')}}': function () {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "DELETE",
                            url: "/teacher/lessons/" + id,
                            dataType: "json",
                            success: function (data) {
                                btn.parent().parent().hide();
                                $.alert(data.success);
                            },
                            error: function (data) {
                                $.alert(data.error);
                            }
                        });
                    },
                    '{{__('lesson.cancel')}}': function () {

                    }
                }
            });
        });

        $('#coursesTable').on("click", '.btn-delete', function() {
            var btn = $(this);
            var id = btn.attr("data-id");
            $.confirm({
                title: '{{__('lesson.confirm')}}!',
                content: '{{__('lesson.confirmMessage')}}',
                buttons: {
                    '{{__('lesson.confirm')}}': function () {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "DELETE",
                            url: "/teacher/courses/" + id,
                            dataType: "json",
                            success: function (data) {
                                table.ajax.reload();
                                $.alert(data.success);
                            },
                            error: function (data) {
                                $.alert(data.error);
                            }
                        });
                    },
                    '{{__('lesson.cancel')}}': function () {

                    }
                }
            });
        });

        function showAdviseModel(btn){
            var id = $(btn).attr("data-id");
            // var url = $(btn).attr("href");
            $.ajax({
                type: "GET",
                url: '{{route('teacher.courses.advises')}}',
                data: {
                    id : id
                },
                success: function (data) {
                    $('#numberEvaluation').html('Hiện tại khoá học nhận được <span class="text-primary">' + data.count + '</span> lượt đánh giá.');
                    var advises = '<ol>';
                    $.each(data.advises, function (index, value) {
                        advises += '<li>' + value.description +'</li>';
                    });
                    advises += '</ol>';
                    $('#advise-list').html(advises);
                },
                error: function (data) {
                    $('#numberEvaluation').html(data.responseJSON);
                    $('#adviseLable').hide();
                }
            });
        }

        $('#adviseCloseBtn').click(function () {
            $('#adviseLable').show();
            $('#numberEvaluation').html('');
            $('#advise-list').html('');
        })
    </script>
@endsection
