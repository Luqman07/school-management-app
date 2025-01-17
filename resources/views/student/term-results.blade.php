<x-app-layout>
    <x-slot name="styles">

        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('TAssets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('TAssets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('TAssets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    </x-slot>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $academicSession->name . ' ' . $term->name }} Results</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Default box -->
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="btn-group">
                                        <a
                                            href="{{ route('result.show.performance', ['student' => $student, 'periodSlug' => $period->slug]) }}">
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm btn-flat">Performance
                                                Report</button>
                                        </a>
                                        @auth('web')
                                            <button type="button"
                                                onclick="emailConfirmation('{{ route('result.mail.performance', ['student' => $student, 'periodSlug' => $period->slug]) }}')"
                                                class="btn btn-outline-secondary btn-sm btn-flat"
                                                title="Email Performance Report">Email to Guardian</button>
                                        @endauth
                                        @if ($period->isActive())
                                            <a href="{{ route('pd.create', ['student' => $student]) }}">
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn-flat"
                                                    title="Create or update Pychomotor domain for the result's academic session and term">Create/Update
                                                    PD</button>
                                            </a>
                                            <a href="{{ route('ad.create', ['student' => $student]) }}">
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn-flat"
                                                    title="Create or update Affective domain for the result's academic session and term">Create/Update
                                                    AD</button>
                                            </a>
                                            <a href="{{ route('attendance.create', ['student' => $student]) }}">
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn-flat"
                                                    title="Create or update attendance record">Create/Update
                                                    Attendance</button>
                                            </a>
                                            @auth('teacher')
                                                @if ($student->mainTeacher()->id == auth('teacher')->id())
                                                    <a
                                                        href="{{ route('remark.teacher.create', ['student' => $student]) }}">
                                                        <button type="button"
                                                            class="btn btn-outline-secondary btn-sm btn-flat"
                                                            title="Create or update teacher's remark">Create/Update
                                                            Teacher's Remark</button>
                                                    </a>
                                                @endif
                                            @endauth
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="results" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>C.A.<span class="text-red-500 pl-1">40</span></th>
                                            <th>Exam<span class="text-red-500 pl-1">60</span></th>
                                            <th>Total<span class="text-red-500 pl-1">100</span></th>
                                            <th>Highest Score</th>
                                            <th>Lowest Score</th>
                                            <th>Class Average</th>
                                            <th>Grade</th>
                                            @if ($period->isActive())
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!$results->isEmpty())
                                            @foreach ($results as $key => $result)
                                                <tr>
                                                    <td>{{ $result->subject->name }}</td>
                                                    <td>{{ $result->ca }}</td>
                                                    <td>{{ $result->exam }}</td>
                                                    <td>{{ $result->total }}</td>
                                                    <td>{{ $maxScores[$result->subject->name] }}
                                                    <td>{{ $minScores[$result->subject->name] }}
                                                    </td>
                                                    <td>{{ round($averageScores[$result->subject->name], 2) }}
                                                    </td>
                                                    <td>
                                                        @if (round($result->total) <= 39)
                                                            F
                                                        @elseif(round($result->total) > 39 && round($result->total)
                                                        <= 49) D @elseif(round($result->total) > 49 &&
                                                            round($result->total) <= 59) C @elseif(round($result->
                                                                    total) > 59 && round($result->total) <= 69) B
                                                                    @elseif(round($result->total) > 69 &&
                                                                    round($result->total) <= 100) A @else
                                                                            @endif
                                                    </td>
                                                    @if ($period->isActive())

                                                        <td>

                                                            <div class="btn-group">
                                                                <a
                                                                    href="{{ route('result.edit', ['result' => $result]) }}">
                                                                    <button type="button" id=""
                                                                        class="btn btn-default btn-flat" title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                </a>
                                                                <button type="submit" class="btn btn-default btn-flat"
                                                                    title="Delete"
                                                                    onclick="deleteConfirmationModal('{{ route('result.destroy', ['result' => $result]) }}', '{{ $result->subject->name }}')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>

                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            No results for this Term 😢
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Subject</th>
                                            <th>C.A.<span class="text-red-500 pl-1">40</span></th>
                                            <th>Exam<span class="text-red-500 pl-1">60</span></th>
                                            <th>Total<span class="text-red-500 pl-1">100</span></th>
                                            <th>Highest Score</th>
                                            <th>Lowest Score</th>
                                            <th>Class Average</th>
                                            <th>Grade</th>
                                            @if ($period->isActive())
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
    {{-- Delete confirmation modal --}}
    <div class="modal fade" id="deleteConfirmationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <span id="deleteItemName" class="font-bold"></span> result?
                </div>
                <div class="modal-footer justify-content-between">
                    <form action="" method="POST" id="yesDeleteConfirmation">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-danger">Yes</button>
                    </form>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{-- Email confirmation modal --}}
    <div class="modal fade" id="emailConfirmation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to send performance report to the guardian
                </div>
                <div class="modal-footer justify-content-between">
                    <a href="" id="confirmed">
                        <button type="button" class="btn btn-success">Yes</button>
                    </a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <x-slot name="scripts">

        <!-- DataTables  & Plugins -->
        <script src="{{ asset('TAssets/plugins/datatables/jquery.dataTables.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('TAssets/plugins/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('TAssets/plugins/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ asset('TAssets/plugins/datatables-buttons/js/buttons.html5.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/datatables-buttons/js/buttons.print.min.js') }}">
        </script>
        <script src="{{ asset('TAssets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}">
        </script>
        <!-- AdminLTE App -->
        <script>
            function deleteConfirmationModal(url, name) {
                $('#yesDeleteConfirmation').attr("action", url)
                $('#deleteItemName').html(name)
                $('#deleteConfirmationModal').modal('show')
            }

            function emailConfirmation(url) {
                $('#confirmed').attr("href", url)
                $('#emailConfirmation').modal('show')
            }


            //datatables
            $(function() {
                $("#results").DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print"]
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            });
        </script>
    </x-slot>
</x-app-layout>
