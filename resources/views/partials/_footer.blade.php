{{--<footer class="main-footer sticky footer-type-2" style="overflow: hidden; margin-top: 0">--}}
<div style="clear: both; height: 50px;"></div>
<footer id="footerMain" class="main-footer sticky footer-type-2" style="overflow:hidden;">

    <style media="screen">
       
        .table tr:nth-child(even) {
            background-color: white !important;
        }

        #footerMain {
           /* position: fixed;*/
            bottom: 0px;
            /*width: 100%;*/
            /*bottom: 0px !important;*/

        }


    </style>

    <div class="footer-inner">
        <div class="footer-text animated slideInUp">
            <p class="text-center" style="margin: 0 30px 9px;">Copyright &copy; 2016 - 2017
                <strong>Ambala IT. All Rights Reserved.</strong>
                Developed by
                <a href="https://www.ambalait.com" target="_blank">Ambala IT.</a>
            </p>
        </div>
        <div class="go-up">
            <a href="#" rel="go-top">
                <i class="fa-angle-up"></i>
            </a>
        </div>
    </div>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    {{-- <script src="{{asset('hr_asset/js/bootstrap.min.js')}}"></script> --}}
	{{-- <script src="{{asset('hr_asset/js/TweenMax.min.js')}}"></script>
	<script src="{{asset('hr_asset/js/resizeable.js')}}"></script>
	<script src="{{asset('hr_asset/js/joinable.js')}}"></script>
	<script src="{{asset('hr_asset/js/xenon-api.js')}}"></script>
	<script src="{{asset('hr_asset/js/xenon-toggles.js')}}"></script>

	<script type="text/javascript" src="{{asset('hr_asset/js/timepicker/bootstrap-timepicker.min.js')}}"></script>

	<script src="{{asset('hr_asset/js/daterangepicker/daterangepicker.js')}}"></script>
	<script src="{{asset('hr_asset/js/datepicker/bootstrap-datepicker.js')}}"></script>
	<script src="{{asset('hr_asset/js/timepicker/bootstrap-timepicker.min.js')}}"></script>

	<script src="{{asset('hr_asset/js/select2/select2.min.js')}}"></script>

	<script src="{{asset('hr_asset/js/jquery-ui/jquery-ui.min.js')}}"></script>

	<!-- Datatables -->
	<script src="{{asset('hr_asset/js/datatables-new/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('hr_asset/js/datatables-new/dataTables.colReorder.min.js')}}"></script>

	{{ Html::style('hr_asset/js/datatables-new/jquery.dataTables.min.css') }}
	{{ Html::style('hr_asset/js/datatables-new/colReorder.dataTables.min.css') }}
	<!-- Datatables -->

	<!-- JavaScripts initializations and stuff -->
	<script src="{{ asset('hr_asset/js/xenon-custom.js') }}"></script>

	<script src="{{ asset('hr_asset/js/custom.js') }}"></script> --}}
    <script type="text/javascript">
        $(function () {
            Highcharts.setOptions({

                lang: {
                    thousandsSep: ','
                },

                exporting: {
                    buttons: {
                        contextButton: {
                            // symbol: 'download',
                            menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF']
                        }
                    }
                }

            });
        });
    </script>
    <script type="text/javascript">
        var opts = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $(document).on('click', '#noBtn, .close, .modal-backdrop', function () {
            $('.navbar-minimal').removeAttr('style');
        });
    </script>

    <style type="text/css">
        .navbar.horizontal-menu.navbar-fixed-top {
            /* top:-15px !important;*/
            height: 105px !important;
        }


        .navbar.horizontal-menu.navbar-minimal.navbar-fixed-top + .page-container {

           /* margin-top: 120px;*/
        }
    </style>
{{--</footer>--}}
