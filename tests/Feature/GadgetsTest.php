<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Gadgets;
use App\Models\Categories;
use App\Models\Brands;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GadgetsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->category = Categories::factory()->create();
        $this->brand = Brands::factory()->create();
    }

    /** @test */
    public function it_can_list_gadgets()
    {
        Gadgets::factory()->count(5)->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $response = $this->get('/gadgets');

        $response->assertStatus(200);
        $response->assertViewIs('gadgets.index');
    }

    /** @test */
    public function it_can_show_a_gadget()
    {
        $gadget = Gadgets::factory()->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $response = $this->get("/gadgets/{$gadget->GadgetID}");

        $response->assertStatus(200);
        $response->assertViewIs('gadgets.show');
    }

    /** @test */
    public function it_can_create_a_gadget()
    {
        $gadgetData = [
            'GadgetName' => 'Test Gadget',
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
            'ReorderPoint' => 10,
        ];

        $response = $this->post('/gadgets', $gadgetData);

        $this->assertDatabaseHas('gadgets', [
            'GadgetName' => 'Test Gadget',
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);
    }

    /** @test */
    public function it_validates_gadget_creation()
    {
        $response = $this->post('/gadgets', []);

        $response->assertSessionHasErrors(['GadgetName', 'CategoryID', 'BrandID']);
    }

    /** @test */
    public function it_can_update_a_gadget()
    {
        $gadget = Gadgets::factory()->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $response = $this->put("/gadgets/{$gadget->GadgetID}", [
            'GadgetName' => 'Updated Gadget',
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $this->assertDatabaseHas('gadgets', [
            'GadgetID' => $gadget->GadgetID,
            'GadgetName' => 'Updated Gadget',
        ]);
    }

    /** @test */
    public function it_can_soft_delete_a_gadget()
    {
        $gadget = Gadgets::factory()->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $response = $this->delete("/gadgets/{$gadget->GadgetID}");

        $this->assertSoftDeleted('gadgets', [
            'GadgetID' => $gadget->GadgetID,
        ]);
    }
}

