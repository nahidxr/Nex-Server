@extends('backend.layouts.master')


@section('title')
    SCORM
@endsection

@section('styles')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .report-table th, .report-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .report-table th {
            background-color: #f8f8f8;
            color: #333;
        }
        .report-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .report-table tr:hover {
            background-color: #e0e0e0;
        }
        .score {
            font-weight: bold;
        }
        .incomplete {
            color: #e74c3c;
        }
        .completed {
            color: #2ecc71;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
    </style>
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
@endsection


@section('admin-content')


    <div class="main-content-inner">
        @php
            function millisecondsToTime($milliseconds) {
                // Convert milliseconds to total seconds
                $totalSeconds = floor($milliseconds / 1000);
                $remainingMilliseconds = $milliseconds % 1000;

                // Calculate hours, minutes, and seconds
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $seconds = $totalSeconds % 60;

                // Convert milliseconds to centiseconds (two decimal places)
                $centiseconds = round($remainingMilliseconds / 10);

                // Format the time as HH:MM:SS.CC (where CC is centiseconds)
                return sprintf('%02d:%02d.%02d', $hours, $minutes, $seconds, $centiseconds);
            }
        @endphp
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
                                <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Progression</th>
                                    <th>Score</th>
                                    <th>Lesson Status</th>
                                    <th>Completion Status</th>
                                    <th>Completion Time</th>
                                    <th>Last Session Time</th>
                                    <th>Last Update</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($scorm['scostrack'] as $track)

                                    <tr>
                                        <td>{{ $track['user']['name'] }}</td>
                                        <td>{{ $track['user']['email'] }}</td>
                                        <td>{{ $track['progression'] }} %</td>
                                        <td class="score">{{ $track['score_raw'] }} / {{ $track['score_max'] }}</td>
                                        <td class="{{ $track['lesson_status'] === 'completed' ? 'completed' : 'incomplete' }}">
                                            {{ ucfirst($track['lesson_status']) }}
                                        </td>
                                        <td class="{{ $track['completion_status'] === 'completed' ? 'completed' : 'incomplete' }}">
                                            {{ ucfirst($track['completion_status']) }}
                                        </td>
                                        <td>{{ millisecondsToTime($track['total_time_int']) }}</td>
                                        <td>{{ millisecondsToTime($track['session_time']) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($track['latest_date'])->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

{{--                 --}}
{{--                            {!! $scorm['scostrack']->links() !!}--}}
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

