@include('v1.backend.layouts.includes._start')

    @include('v1.backend.layouts.includes._header')

    @include('v1.backend.layouts.includes._sidebar')

    <div class="page-content">

        <div class="container-fluid">

            @include('sweetalert-message.backend-messages')

            @yield('content')

        </div>

        @include('v1.backend.layouts.includes._footer')

    </div>

    @include('sweetalert-message.backend-flash-message')

@include('v1.backend.layouts.includes._end')