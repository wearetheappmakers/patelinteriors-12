<?php

namespace Tests\Feature\Upgrade;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Type;
use App\Lead;
use App\Client;
use App\Exports\GalleryExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpgradeParityTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test public root route renders login view with 200 OK.
     */
    public function test_public_root_route_returns_login_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Patel Interior');
    }

    /**
     * Test admin dashboard returns 200 for authenticated user.
     */
    public function test_admin_dashboard_authenticated(): void
    {
        $user = User::find(1);
        $this->assertNotNull($user);

        $response = $this->actingAs($user, 'web')
                         ->actingAs($user, 'user')
                         ->get('/user/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test DataTables AJAX endpoint returns valid schema for leads and clients.
     */
    public function test_datatables_ajax_endpoints(): void
    {
        $user = User::find(1);

        $response = $this->actingAs($user, 'user')
                         ->withHeaders([
                             'X-Requested-With' => 'XMLHttpRequest',
                             'Accept' => 'application/json',
                         ])
                         ->get('/user/lead?draw=1&start=0&length=10');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $this->assertGreaterThan(0, $response->json('recordsTotal'));

        $clientResponse = $this->actingAs($user, 'user')
                               ->withHeaders([
                                   'X-Requested-With' => 'XMLHttpRequest',
                                   'Accept' => 'application/json',
                               ])
                               ->get('/user/client?draw=1&start=0&length=10');

        $clientResponse->assertStatus(200);
        $clientResponse->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $this->assertGreaterThan(0, $clientResponse->json('recordsTotal'));
    }

    /**
     * Test Module CRUD with database transactions (rollback automatically).
     */
    public function test_module_crud_lifecycle_with_rollback(): void
    {
        $user = User::find(1);

        // 1. CREATE
        $createRes = $this->actingAs($user, 'user')
                          ->postJson('/user/type', [
                              'name' => 'L12_Upgrade_Test_Type',
                              'branch_id' => 1,
                          ]);

        $createRes->assertStatus(200);
        $createRes->assertJson(['status' => 'success']);

        $record = Type::where('name', 'L12_Upgrade_Test_Type')->first();
        $this->assertNotNull($record);

        // 2. READ / EDIT
        $editRes = $this->actingAs($user, 'user')
                        ->get("/user/type/{$record->id}/edit");
        $editRes->assertStatus(200);

        // 3. UPDATE
        $updateRes = $this->actingAs($user, 'user')
                          ->putJson("/user/type/{$record->id}", [
                              'name' => 'L12_Upgrade_Test_Type_Renamed',
                              'branch_id' => 1,
                          ]);
        $updateRes->assertStatus(200);
        $updateRes->assertJson(['status' => 'success']);
        $this->assertEquals('L12_Upgrade_Test_Type_Renamed', $record->fresh()->name);

        // 4. DELETE
        $deleteRes = $this->actingAs($user, 'user')
                          ->get(route('user.deletestype.clones', $record->id));
        $deleteRes->assertStatus(302);
        $this->assertNull(Type::find($record->id));
    }

    /**
     * Test batch status change endpoint.
     */
    public function test_multiple_status_change(): void
    {
        $user = User::find(1);

        $temp = new Type();
        $temp->name = 'Status_Test_Type';
        $temp->branch_id = 1;
        $temp->status = 0;
        $temp->save();

        $response = $this->actingAs($user, 'user')
                         ->get("/user/change-multiple-status?table_name=types&param=1&id={$temp->id}&field=status");

        $response->assertStatus(200);
        $this->assertEquals(1, $temp->fresh()->status);
    }

    /**
     * Test Excel export structure.
     */
    public function test_excel_export_generation(): void
    {
        $export = new GalleryExport();
        $headings = $export->headings();
        $this->assertIsArray($headings);
        $this->assertContains('gallery', $headings);
    }

    /**
     * Test PDF generation view rendering.
     */
    public function test_pdf_view_generation(): void
    {
        $user = User::find(1);

        $response = $this->actingAs($user, 'user')
                         ->get('/user/appointment-letter/pdf/1');

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Test API JWT authentication and user endpoint.
     */
    public function test_api_jwt_endpoint(): void
    {
        $user = User::find(1);
        $token = JWTAuth::fromUser($user);
        $this->assertNotEmpty($token);

        $response = $this->withHeaders([
            'Accept' => 'application/x.patelinterior.v1+json',
            'Authorization' => "Bearer {$token}",
        ])->get('/api/v1/get-user');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => 1,
            'data' => [
                'id' => 1,
            ],
        ]);
    }
}
