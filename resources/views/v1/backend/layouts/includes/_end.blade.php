</div>
    <!-- END Wrapper -->

    @stack('summernote-scripts')

    <!-- Theme Settings offcanvas -->
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas" style="max-width: 350px; width: 100%;">
        <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
            <h5 class="text-white m-0 fw-semibold">Theme Customizer</h5>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            <div data-simplebar class="h-100">
                <h5 class="fs-16 fw-bold mb-3">Layout</h5>
                <div class="mb-3">
                    <label class="form-label">Menu size</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-default" value="default" checked>
                            <label class="form-check-label" for="leftbar-size-default">Default</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small-hover-active" value="sm-hover-active">
                            <label class="form-check-label" for="leftbar-size-small-hover-active">Small Hover Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3 text-center">
            <button type="button" class="btn btn-danger w-100" id="reset-layout">Reset</button>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="{{ asset(getEnvFolder().'others/all/assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="{{ asset(getEnvFolder().'backend/assets/js/vendor.js') }}"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="{{ asset(getEnvFolder().'backend/assets/js/app.js') }}"></script>

    <!-- Custom JS Fait par moi -dev aubanel- -->
    <script src="{{ asset(getEnvFolder() . 'others/all/assets/js/global-form-loader.js')}}"> </script>


    @stack('scripts')
</body>

</html>