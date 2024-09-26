<!DOCTYPE html>
<html>
    <head>
        <title>SCORM(LMS) </title>

        <!--
            <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('css/bootstrap.min.css')}}">
            <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('css/all.min.css')}}">-->

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
{{--        <link rel="stylesheet" href="{{ mix('css/app.css') }}">--}}
    </head>
    <body>

        <div class="container">
            @yield('content')
        </div>

    </body>
</html>
