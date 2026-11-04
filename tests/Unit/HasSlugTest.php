<?php

declare(strict_types=1);

use App\Traits\HasSlug;

class HasSlugTestStub
{
    use HasSlug;
}

it('makeSlug slugifie les valeurs textuelles', function () {
    expect(HasSlugTestStub::makeSlug('Bonjour Le Monde'))->toBe('bonjour-le-monde');
    expect(HasSlugTestStub::makeSlug('  Tabs   and spaces '))->toBe('tabs-and-spaces');
    expect(HasSlugTestStub::makeSlug('LÉON'))->toBe('leon');
});

it('makeSlug bascule sur un slug de secours pour les valeurs non utilisables', function () {
    expect(HasSlugTestStub::makeSlug('😀😀😀'))->toMatch('/^n-[a-z0-9]{8}$/');
});
