    <!-- App js -->
    <script src="{{ asset('dashboard/js/vendor.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/all.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/summernote-bs4.js') }}"></script>


    <!-- third party js -->
    <script src="{{ asset('dashboard/js/vendor/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('dashboard/js/vendor/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('dashboard/js/vendor/switchery.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/vendor/toastr.min.js') }}"></script>
    <!-- third party js ends -->


    <!-- Toastr message display -->
    @toastr_render

    <script type="text/javascript">
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr["error"]("{{ $error }}");
            @endforeach
        @endif
    </script>

    <script src="{{ asset('dashboard/js/app.js') }}"></script>

    <!-- page js -->
    @yield('page_js')