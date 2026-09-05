            
            </div>

            <div class="card-footer text-muted">
                <div class="row">
                    <div class="col-6">
                        <small>{{ __('Créé le') }} : @formatDate($file->created_at, 'D MMMM YYYY - HH:mm:ss')</small>
                    </div>
                    <div class="col-6 text-end">
                        <small>{{ __('Modifié le') }} : @formatDate($file->updated_at, 'D MMMM YYYY - HH:mm:ss')</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>