<?php

namespace Tests\Feature\Upgrade;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Type;
use App\Company;
use App\Warehouse;
use App\Holiday;
use App\Vendor;
use App\Task;
use App\ProductCategory;

class AdminModulesFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::find(1);
    }

    public function test_crm_leads_and_clients(): void
    {
        foreach (['/user/lead', '/user/client'] as $uri) {
            $this->actingAs($this->user, 'user')->get($uri)->assertStatus(200);
            $this->actingAs($this->user, 'user')
                 ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                 ->get("$uri?draw=1&start=0&length=10")
                 ->assertStatus(200);
        }
    }

    public function test_products_and_categories(): void
    {
        foreach (['/user/product/index', '/user/productcategory', '/user/company'] as $uri) {
            $this->actingAs($this->user, 'user')->get($uri)->assertStatus(200);
            $this->actingAs($this->user, 'user')
                 ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                 ->get("$uri?draw=1&start=0&length=10")
                 ->assertStatus(200);
        }
    }

    public function test_orders_and_invoices(): void
    {
        foreach (['/user/salesorder', '/user/purchaseorder', '/user/purchase/invoice/index'] as $uri) {
            $this->actingAs($this->user, 'user')->get($uri)->assertStatus(200);
            $this->actingAs($this->user, 'user')
                 ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                 ->get("$uri?draw=1&start=0&length=10")
                 ->assertStatus(200);
        }
    }

    public function test_warehouse_and_visits(): void
    {
        foreach (['/user/warehouse', '/user/visits', '/user/service'] as $uri) {
            $this->actingAs($this->user, 'user')->get($uri)->assertStatus(200);
            $this->actingAs($this->user, 'user')
                 ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                 ->get("$uri?draw=1&start=0&length=10")
                 ->assertStatus(200);
        }
    }

    public function test_hr_and_system_settings(): void
    {
        foreach (['/user/attendance/index', '/user/leave', '/user/appointment_letter', '/user/company_policy', '/user/expense', '/user/holiday', '/user/role', '/user/user/index', '/user/vendor', '/user/task'] as $uri) {
            $this->actingAs($this->user, 'user')->get($uri)->assertStatus(200);
            $this->actingAs($this->user, 'user')
                 ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                 ->get("$uri?draw=1&start=0&length=10")
                 ->assertStatus(200);
        }
    }

    public function test_type_crud_lifecycle(): void
    {
        $res = $this->actingAs($this->user, 'user')
                    ->postJson('/user/type', ['name' => 'QA_Type_Test', 'branch_id' => 1]);
        $res->assertStatus(200)->assertJson(['status' => 'success']);
        $item = Type::where('name', 'QA_Type_Test')->first();
        $this->assertNotNull($item);

        $this->actingAs($this->user, 'user')->get("/user/type/{$item->id}/edit")->assertStatus(200);

        $this->actingAs($this->user, 'user')
             ->putJson("/user/type/{$item->id}", ['name' => 'QA_Type_Updated', 'branch_id' => 1])
             ->assertJson(['status' => 'success']);
        $this->assertEquals('QA_Type_Updated', $item->fresh()->name);

        $delRes = $this->actingAs($this->user, 'user')->get(route('user.deletestype.clones', $item->id));
        $delRes->assertStatus(302);
        $this->assertNull(Type::find($item->id));
    }

    public function test_productcategory_status_change(): void
    {
        $res = $this->actingAs($this->user, 'user')
                    ->postJson('/user/productcategory', ['name' => 'QA_Cat_Test', 'branch_id' => 1]);
        $res->assertStatus(200);
        $cat = ProductCategory::where('name', 'QA_Cat_Test')->first();
        $this->assertNotNull($cat);

        $statusRes = $this->actingAs($this->user, 'user')
                          ->get("/user/change-multiple-status?table_name=product_category&param=1&id={$cat->id}&field=status");
        $statusRes->assertStatus(200);
        $this->assertEquals(1, $cat->fresh()->status);
    }

    public function test_warehouse_crud(): void
    {
        $res = $this->actingAs($this->user, 'user')
                    ->postJson('/user/warehouse', [
                        'name' => 'QA_WH_Test',
                        'branch_id' => 1,
                        'address' => 'Rajkot Test',
                    ]);
        $res->assertStatus(200);
        $wh = Warehouse::where('name', 'QA_WH_Test')->first();
        $this->assertNotNull($wh);

        $this->actingAs($this->user, 'user')->get("/user/warehouse/{$wh->id}/edit")->assertStatus(200);
    }

    public function test_holiday_crud(): void
    {
        $res = $this->actingAs($this->user, 'user')
                    ->postJson('/user/holiday', [
                        'name' => 'QA_Holiday_Test',
                        'date' => '2026-12-25',
                        'branch_id' => 1,
                    ]);
        $res->assertStatus(200);
        $h = Holiday::where('name', 'QA_Holiday_Test')->first();
        $this->assertNotNull($h);
    }

    public function test_vendor_crud(): void
    {
        $res = $this->actingAs($this->user, 'user')
                    ->post('/user/vendor', [
                        'name' => 'QA_Vendor_Test',
                        'email' => 'vendor_qa@test.com',
                        'branch_id' => 1,
                    ]);
        $v = Vendor::where('name', 'QA_Vendor_Test')->first();
        $this->assertNotNull($v);

        $this->actingAs($this->user, 'user')->get("/user/vendor/{$v->id}/edit")->assertStatus(200);
    }
}
