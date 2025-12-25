<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\GadgetsRepository;
use App\Models\Gadgets;
use App\Models\Categories;
use App\Models\Brands;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GadgetsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $category;
    protected $brand;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new GadgetsRepository(new Gadgets());
        $this->category = Categories::factory()->create();
        $this->brand = Brands::factory()->create();
    }

    /** @test */
    public function it_can_create_a_gadget()
    {
        $data = [
            'GadgetName' => 'Test Gadget',
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ];

        $gadget = $this->repository->create($data);

        $this->assertInstanceOf(Gadgets::class, $gadget);
        $this->assertEquals('Test Gadget', $gadget->GadgetName);
    }

    /** @test */
    public function it_can_find_a_gadget_by_id()
    {
        $gadget = Gadgets::factory()->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $found = $this->repository->findWithRelations($gadget->GadgetID);

        $this->assertInstanceOf(Gadgets::class, $found);
        $this->assertEquals($gadget->GadgetID, $found->GadgetID);
    }

    /** @test */
    public function it_can_update_a_gadget()
    {
        $gadget = Gadgets::factory()->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $updated = $this->repository->update($gadget->GadgetID, [
            'GadgetName' => 'Updated Name',
        ]);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('gadgets', [
            'GadgetID' => $gadget->GadgetID,
            'GadgetName' => 'Updated Name',
        ]);
    }

    /** @test */
    public function it_can_delete_a_gadget()
    {
        $gadget = Gadgets::factory()->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);

        $deleted = $this->repository->delete($gadget->GadgetID);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('gadgets', [
            'GadgetID' => $gadget->GadgetID,
        ]);
    }

    /** @test */
    public function it_can_restore_a_deleted_gadget()
    {
        $gadget = Gadgets::factory()->create([
            'CategoryID' => $this->category->CategoryID,
            'BrandID' => $this->brand->BrandID,
        ]);
        $gadget->delete();

        $restored = $this->repository->restore($gadget->GadgetID);

        $this->assertTrue($restored);
        $this->assertDatabaseHas('gadgets', [
            'GadgetID' => $gadget->GadgetID,
            'deleted_at' => null,
        ]);
    }
}

