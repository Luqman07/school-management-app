<x-app-layout>
    <x-slot name="styles">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('TAssets/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('TAssets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    </x-slot>
    <div class=" content-wrapper">
        <!-- Content Header (Page header) -->
         
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Teacher</h1>
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
                                <h3 class="card-title">Edit
                                    <a href="{{ route('teacher.show', ['teacher' => $teacher]) }}"
                                        class="text-blue-500">
                                        {{ $teacher->first_name . ' ' . $teacher->last_name }}
                                    </a>
                                </h3>
                            </div>
                            <form method="POST" action="@auth('web')
                                            {{ route('teacher.user.update', ['teacher' => $teacher]) }}
                                    @else
                                            {{ route('teacher.update', ['teacher' => $teacher]) }}
                            @endauth">
                                @csrf
                                @method('PATCH')
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>First name</label>
                                        <input type=" text" name="first_name"
                                            class="form-control @error('first_name') is-invalid @enderror"
                                            value="{{ old('first_name', $teacher->first_name) }}">
                                        @error('first_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Last name</label>
                                        <input type="text" name="last_name"
                                            class="form-control @error('last_name') is-invalid @enderror"
                                            value="{{ old('last_name', $teacher->last_name) }}">
                                        @error('last_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Sex</label>
                                        <select class="custom-select @error('sex') is-invalid @enderror" name="sex">
                                            <option @if (old('sex', $teacher->sex) == 'M') SELECTED @endif>M</option>
                                            <option @if (old('sex', $teacher->sex) == 'F') SELECTED @endif>F</option>
                                        </select>
                                        @error('sex')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @auth('web')
                                        <div class="form-group">
                                            <label>Date of birth</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text"
                                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                                    data-inputmask-alias="datetime" name="date_of_birth"
                                                    data-inputmask-inputformat="yyyy-mm-dd" data-mask
                                                    value="{{ old('date_of_birth', $teacher->date_of_birth) }}">
                                                @error('date_of_birth')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <div class="form-group">
                                            <label>Email address</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $teacher->email) }}">
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Phone number</label>
                                            <input type="tel" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $teacher->phone) }}">
                                            @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endauth
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
    <x-slot name="scripts">
        <!-- Select2 -->
        <script src="{{ asset('TAssets/plugins/select2/js/select2.full.min.js') }}"></script>
        <!-- InputMask -->
        <script src="{{ asset('TAssets/plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('TAssets/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <script>
            $(function() {
                //Initialize Select2 Elements
                $('.select2').select2()
                $('#datemask').inputmask('yyyy-mm-dd', {
                    'placeholder': 'yyyy-mm-dd'
                })
                //Money Euro
                $('[data-mask]').inputmask()

            })

        </script>
    </x-slot>
</x-app-layout>
