<?php

namespace Tests\Browser;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_create_a_product()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/products/create')
                    ->type('name', 'New Product')
                    ->type('price', 100)
                    ->type('description', 'This is a new product.')
                    ->press('Save')
                    ->assertPathIs('/products')
                    ->assertSee('New Product');
        });
    }

    public function it_shows_validation_errors_on_create()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/products/create')
                    ->press('Save')
                    ->assertSee('The name field is required')
                    ->assertSee('The price field is required');
        });
    }

    public function it_can_read_all_products()
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products')
                    ->assertSee('Test Product');
        });
    }

    public function it_can_read_a_single_product()
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->id)
                    ->assertSee('Test Product');
        });
    }

    public function it_can_update_a_product()
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->id . '/edit')
                    ->type('name', 'Updated Name')
                    ->press('Save')
                    ->assertPathIs('/products')
                    ->assertSee('Updated Name');
        });
    }

    public function it_shows_validation_errors_on_update()
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->id . '/edit')
                    ->type('name', '')
                    ->press('Save')
                    ->assertSee('The name field is required');
        });
    }

    public function it_can_delete_a_product()
    {
        $product = Product::factory()->create(['name' => 'Delete Me']);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products')
                    ->press('@delete-button-' . $product->id)
                    ->assertDontSee('Delete Me');
        });
    }
}
