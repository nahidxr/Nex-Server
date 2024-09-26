@extends('backend.layouts.master')


@section('title')
   SCORM
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
@endsection


@section('admin-content')

    <!-- page title area start -->
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">SCORM</h4>
                    <ul class="breadcrumbs pull-left">
                        <li><a href="{{ route('admin.scorm.index') }}">SCORM</a></li>
                        <li><span>All SCORMS</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-6 clearfix">
                @include('backend.layouts.partials.logout')
            </div>
        </div>
    </div>
    <!-- page title area end -->
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title float-left">SCORM List</h4>
                        <p class="float-right mb-2">
                            @if (Auth::guard('admin')->user()->can('admin.edit'))
                                <a class="btn btn-primary text-white" href="{{ route('admin.scorm.create') }}">Upload New</a>
                            @endif
                        </p>
                        <div class="clearfix"></div>
                        <div class="data-tables">
                            @include('backend.layouts.partials.messages')
                            <table id="dataTable" class="text-center">
                                <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="60px">No</th>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Origin File</th>
                                    <th>Entry URL</th>
                                    <th>Version</th>
                                    <th width="200px">UploadDateTime</th>
                                    <th width="150px">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->title }}</td>
                                        <td>{{ $product->origin_file }}</td>
                                        <td>{{ $product->entry_url }}</td>
                                        <td>{{ $product->version }}</td>
                                        <td>{{ $product->created_at }}</td>
                                        <td>
                                            <a class="btn btn-info btn-sm" target="_blank" href="{{ route('admin.scorm.show',$product->id) }}">
                                                Play
                                            </a>
                                            <a class="btn btn-info btn-sm" href="{{ route('scorm.report',$product->id) }}">
                                                Report
                                            </a>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.scorm.destroy',$product->id) }}" method="POST">

                                                <!--<a class="btn btn-info btn-sm" href="{{ route('admin.scorm.show',$product->uuid) }}"><i class="fa-solid fa-list"></i> Show</a>-->

                                                <!--<a class="btn btn-primary btn-sm" href="{{ route('admin.scorm.edit',$product->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>-->

                                                @csrf
                                                @method('DELETE')

                                                <!--<button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>-->
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">There are no data.</td>
                                    </tr>
                                @endforelse
                                </tbody>

                            </table>
                            {!! $products->links() !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- data table end -->

        </div>
    </div>


@endsection
@section('scripts')
    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

    <script>
        /*================================
       datatable active
       ==================================*/
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                responsive: true
            });
        }

    </script>
@endsection

