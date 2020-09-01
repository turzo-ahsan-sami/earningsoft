<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="Ambala ERP"/>
    <meta name="author" content="Ambala ERP"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} @yield('title')</title>

    {{--Links--}}
    <link rel="stylesheet" href="{{ asset('https://fonts.googleapis.com/css?family=Arimo:400,700,400italic') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800"
          rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css') }}">


    <link href="{{ asset('software/js/select2/select2.min.css') }}" rel="stylesheet"/>


    {{--     {{ Html::style('cdn/css/css.css') }} --}}
    {{--     {{ Html::style('cdn/css/jquery-ui.css') }} --}}
    {{--     {{ Html::style('cdn/css/animate.min.css') }} --}}

    {{--    {{ Html::style('css/fonts/linecons/css/linecons.css') }}--}}
    {{--    {{ Html::style('css/fonts/fontawesome/css/font-awesome.min.css') }}--}}
    {{--    {{ Html::style('css/bootstrap.css') }}--}}
    {{--    {{ Html::style('css/xenon-core.css') }}--}}
    {{--    {{ Html::style('css/xenon-forms.css') }}--}}
    {{--    {{ Html::style('css/xenon-components.css') }}--}}
    {{--    {{ Html::style('css/xenon-skins.css') }}--}}
    {{--    {{ Html::style('css/style.css') }}--}}
    {{--    {{ Html::style('css/vert.css') }} --}}


    <link rel="stylesheet" href="{{ asset('software/css/fonts/linecons/css/linecons.css') }}">
    <link rel="stylesheet" href="{{ asset('software/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('software/dashboard/css/custom.css') }}">
     
    {{--    <link rel="stylesheet" href="{{ asset('software/css/fonts/fontawesome/css/font-awesome.min.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/bootstrap.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/js/daterangepicker/daterangepicker-bs3.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/homepage_customanimation.css')}}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/xenon-core.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/xenon-forms.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/xenon-components.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/xenon-skins.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/style.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/css/vert.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/js/select2/select2.min.css')}}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('software/js/dropzone/css/dropzone.css')}}">--}}
    @yield('stylesheets')


    {{-- ################################# Scripts ###################################### --}}
    {{--  <script rel="text/javascript" src="{{ asset('software/dashboard/js/modernizr.custom.js') }}"></script> --}}
     <script rel="stylesheet" src="{{ asset('software/dashboard/js/Chart.js') }}"></script>
   
     <script src="{{ asset('software/js/jquery-3.1.1.min.js') }}"></script>
     <script src="{{ asset('software/js/select2/select2.min.js') }}"></script>
    {{--    <script src="{{ asset('software/js/custom/accessError/createAccessDeniedModal.js') }}"></script>--}}
    {{--    <script src="{{ asset('software/js/custom/accessCheck/checkAccessPermission.js') }}"></script>--}}
    <script src="{{ asset('software/js/app.js') }}"></script>
    <script src="{{ asset('software/libs/FileSaver/FileSaver.min.js') }}"></script>
    <script src="{{ asset('software/libs/js-xlsx/xlsx.core.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
    {{--  <script type="text/javascribootstpt" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script> --}}
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
    {{-- <script src="{{ asset('software/libs/jsPDF/jspdf.min.js') }}"></script>--}}
    {{-- <script src="{{ asset('software/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js') }}"></script> --}}
    {{-- <script src="{{ asset('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.debug.js') }}"></script> --}}
    {{-- <script src="{{ asset('software/js/jspdf.debug.js') }}"></script> --}}
    <script src="{{ asset('software/libs/es6-promise/es6-promise.auto.min.js') }}"></script>
    {{-- <script src="{{ asset('software/libs/html2canvas/html2canvas.min.js') }}"></script> --}}
    <script src="{{ asset('software/libs/tableExport.min.js') }}"></script>
    <script src="{{ asset('software/libs/tableExport.js') }}"></script>

    {{--     <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>--}}
    {{--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>--}}
    {{--    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>--}}
    {{--    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script> --}}


    {{-- {{ Html::script('cdn/js/jquery-1.12.4.js') }} --}}
    {{-- {{ Html::script('cdn/js/jquery-ui.js') }} --}}

    {{-- <script src="{{ asset('https://code.jquery.com/jquery-1.10.2.js') }}"></script> --}}
    {{-- <script src="{{ asset('https://code.jquery.com/ui/1.11.2/jquery-ui.js') }}"></script> --}}
    {{--  <script src="{{ asset('software/js/inputmask/jquery.inputmask.bundle.js') }}"></script>--}}
    {{--  <script src="{{ asset('software/js/jquery-validate/jquery.validate.min.js') }}"></script>--}}
    {{--  <script src="{{ asset('software/js/formwizard/jquery.bootstrap.wizard.min.js') }}"></script>--}}
    {{-- <script src="{{ asset('software/js/toastr/toastr.min.js') }}"></script>--}}
    {{-- <script src="{{ asset('software/js/datepicker/bootstrap-datepicker.js') }}"></script> --}}
    {{-- <script src="{{ asset('software/js/timepicker/bootstrap-timepicker.min.js') }}"></script> --}}



    {{--    <script src="{{asset('software/js/bootstrap.min.js')}}"></script>--}}
    {{--    <script src="{{asset('software/js/TweenMax.min.js')}}"></script>--}}
    {{--    <script src="{{asset('software/js/resizeable.js')}}"></script>--}}
    {{--    <script src="{{asset('software/js/joinable.js')}}"></script>--}}
    {{--    <script src="{{asset('software/js/xenon-api.js')}}"></script>--}}
    {{--    <script src="{{asset('software/js/xenon-toggles.js')}}"></script>--}}
    {{--<script src="{{ asset('software/js/xenon-custom.js') }}"></script>--}}
    {{-- ################################# Scripts ###################################### --}}


    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>


    <script type="text/javascript">
        $(document).on("click", "#btnExportPdf", function () {
            Popup($("#printDiv").html());
        });

        function Popup(data) {
            var currentdate = new Date();
            var datetime = "File: " + currentdate.getDate() + ""
                + (currentdate.getMonth() + 1) + ""
                + currentdate.getFullYear() + ""
                + currentdate.getHours() + ""
                + currentdate.getMinutes() + ""
                + currentdate.getSeconds();

            var mywindow = window.open('', datetime, 'height=800,width=1024');
            mywindow.document.write('<html><head><title>' + datetime + '</title>');
            //mywindow.document.write('<html><head><title>'+datetime+'</title>');
            /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
            mywindow.document.write('</head><body >');
            mywindow.document.write(data);
            mywindow.document.write('</body></html>');
            mywindow.print();
            mywindow.close();

            return true;
        }

    </script>

    

<style>

     html,body{
        
        width: 100%;
        height:100%;
        padding: 0;
        margin:0;
        overflow-x:hidden;
    }

/*html,body{
    overflow-x: hidden;
}*/
#chartdiv {
  width: 100%;
  height: 295px;
}
  
</style>





    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
