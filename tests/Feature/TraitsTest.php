<?php

declare(strict_types=1);

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HasUuidFeatureTestModel extends Model
{
    use HasUuid;

    protected $table = 'has_uuid_feature_stubs';

    protected $guarded = [];

    public $timestamps = false;
}

beforeEach(function () {
    Schema::dropIfExists('has_uuid_feature_stubs');

    Schema::create('has_uuid_feature_stubs', function (Blueprint $table) {
        $table->id();
        $table->string('uuid')->nullable();
    });
});

it('HasUuid génère un uuid ordonné au format standard à la création', function () {
    $model = HasUuidFeatureTestModel::create();

    expect($model->uuid)->not->toBeNull();
    expect($model->uuid)->toMatch('/^[0-9]{8}-[0-9]{4}-[0-9]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('HasUuid ne produit jamais deux uuid identiques', function () {
    $uuids = collect(range(1, 150))->map(fn () => HasUuidFeatureTestModel::create()->uuid);

    expect($uuids->unique()->count())->toBe(150);
});

it('HasUuid est ordonné chronologiquement (tri ascendant == tri par création)', function () {
    $uuids = collect(range(1, 30))->map(fn () => HasUuidFeatureTestModel::create()->uuid)->all();

    $uuidsAsc = collect($uuids)->sort()->values()->all();

    expect(HasUuidFeatureTestModel::query()->orderBy('uuid', 'asc')->pluck('uuid')->all())->toBe($uuidsAsc);
    expect(HasUuidFeatureTestModel::count())->toBe(30);
});