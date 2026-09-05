{{-- Statistics Section --}}
@if(!empty($statistics))
    <div class="row g-3 mb-4">
        @foreach ($statistics as $stat)
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm bg-gradient-{{ $stat['color'] }} h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-soft-{{ $stat['color'] }} p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="{{ $stat['icon'] }} fs-4 text-{{ $stat['color'] }}"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold fs-4">{{ $stat['value'] }}</h6>
                            <small class="text-muted">{{ $stat['label'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif