@extends('v1.backend.layouts.app') 

@section('title') 
    Créer un produit
@endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Créer un produit'])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Créer un produit</h5>
                    <a href="{{ route('backend.products.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @include('v1.backend.pages.products.products.partials._form', [
                        'langs' => $langs,
                        'categories' => $categories,
                        'brands' => $brands,
                        'tags' => $tags,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection