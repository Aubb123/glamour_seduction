<?php

declare(strict_types=1);

use App\Models\ActivityLog;
use App\Models\Audit;
use App\Models\GeoCountry;
use App\Models\User;
use App\Services\V1\ActivityLogger;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 14 — Audits (`audits`) & ActivityLogs (`activity-logs`).
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function auditTestAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function auditTestFixtures(User $admin): array
{
    $audit = Audit::create([
        'auditable_type' => GeoCountry::class,
        'auditable_id' => 1,
        'action' => 'created',
        'old_values' => null,
        'new_values' => ['name' => 'test'],
        'ip_address' => '127.0.0.1',
    ]);

    app(ActivityLogger::class)->log(
        type: 'info',
        action: 'test.action',
        context: ['test' => true],
        message: 'Message de test',
        activity: 'test',
        isSuccess: true,
    );

    $log = ActivityLog::latest('id')->first();

    return [$audit, $log];
}

it('liste les audits avec DataTables et statistiques', function () {
    $admin = auditTestAdmin();
    auditTestFixtures($admin);

    $html = $this->actingAs($admin)
        ->get(route('backend.audits.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('audits-table');
    expect($html)->toContain("$('#audits-table').DataTable");
});

it('affiche le détail d’un audit', function () {
    $admin = auditTestAdmin();
    [$audit] = auditTestFixtures($admin);

    $this->actingAs($admin)
        ->get(route('backend.audits.show', ['audit' => $audit->uuid]))
        ->assertOk();
});

it('refuse la suppression d’un audit (fonction désactivée dans base)', function () {
    $admin = auditTestAdmin();
    [$audit] = auditTestFixtures($admin);

    // Dans base, backend_audits_destroy est volontairement désactivé :
    // le code de suppression est commenté (traçabilité) → redirect back + flash 'info'.
    $this->actingAs($admin)
        ->from(route('backend.audits.index'))
        ->delete(route('backend.audits.destroy', ['audit' => $audit->uuid]))
        ->assertRedirect()
        ->assertSessionHas('info');

    expect(Audit::find($audit->id))->not->toBeNull();
});

it('désactive également le nettoyage des audits (clear)', function () {
    $admin = auditTestAdmin();
    [$audit] = auditTestFixtures($admin);

    $audit->forceFill(['created_at' => now()->subDays(4)])->save();

    // Dans base, backend_audits_clear est aussi désactivé (code commenté)
    // → redirect vers la liste + flash 'info', aucun audit supprimé.
    $this->actingAs($admin)
        ->from(route('backend.audits.index'))
        ->delete(route('backend.audits.clear'))
        ->assertRedirect(route('backend.audits.index'))
        ->assertSessionHas('info');

    expect(Audit::find($audit->id))->not->toBeNull();
});

it('liste les journaux d’activité avec DataTables et statistiques', function () {
    $admin = auditTestAdmin();
    auditTestFixtures($admin);

    $html = $this->actingAs($admin)
        ->get(route('backend.activity-logs.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('activity-logs-table');
    expect($html)->toContain("$('#activity-logs-table').DataTable");
});

it('affiche le détail d’un journal d’activité', function () {
    $admin = auditTestAdmin();
    [, $log] = auditTestFixtures($admin);

    $this->actingAs($admin)
        ->get(route('backend.activity-logs.show', ['activityLog' => $log->uuid]))
        ->assertOk()
        ->assertSee('test.action');
});

it('refuse la suppression d’un journal d’activité (fonction désactivée dans base)', function () {
    $admin = auditTestAdmin();
    [, $log] = auditTestFixtures($admin);

    // Dans base, backend_activity_logs_destroy est volontairement désactivé
    // (code commenté) → redirect vers la liste + flash 'info'.
    $this->actingAs($admin)
        ->from(route('backend.activity-logs.index'))
        ->delete(route('backend.activity-logs.destroy', ['activityLog' => $log->uuid]))
        ->assertRedirect(route('backend.activity-logs.index'))
        ->assertSessionHas('info');

    expect(ActivityLog::find($log->id))->not->toBeNull();
});

it('désactive également le nettoyage des journaux (clear)', function () {
    $admin = auditTestAdmin();
    [, $log] = auditTestFixtures($admin);

    $log->forceFill(['created_at' => now()->subDays(4)])->save();

    // Dans base, backend_activity_logs_clear est aussi désactivé (code commenté)
    // → redirect vers la liste + flash 'info', aucun journal supprimé.
    $this->actingAs($admin)
        ->from(route('backend.activity-logs.index'))
        ->delete(route('backend.activity-logs.clear'))
        ->assertRedirect(route('backend.activity-logs.index'))
        ->assertSessionHas('info');

    expect(ActivityLog::find($log->id))->not->toBeNull();
});

it('protege l’accès aux audits et journaux (non connecté → login)', function () {
    $this->get('/backend/audits/index')->assertRedirect(route('login'));
    $this->get('/backend/activity-logs/index')->assertRedirect(route('login'));
});
