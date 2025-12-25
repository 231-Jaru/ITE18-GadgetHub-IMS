<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Disable foreign key checks for truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'payments',
            'transactions',
            'transaction_items',
            'stocks',
            'buyers',
            'gadgets',
            'brands',
            'categories',
            'suppliers',
            'admins'
        ];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Admins
        $admins = [];
        for ($i = 0; $i < 5; $i++) {
            $admins[] = [
                'Username' => fake()->unique()->userName(),
                'PasswordHash' => bcrypt('password'),
                'Role' => fake()->randomElement(['Admin', 'Staff']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('admins')->insert($admins);

        // 2. Suppliers
        $suppliers = [];
        for ($i = 0; $i < 20; $i++) {
            $suppliers[] = [
                'SupplierName' => fake()->company(),
                'ContactPerson' => fake()->name(),
                'Phone' => fake()->phoneNumber(),
                'Email' => fake()->unique()->safeEmail(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('suppliers')->insert($suppliers);

        // 3. Categories
        $categories = [
            ['CategoryName' => 'Smartphone', 'created_at' => now(), 'updated_at' => now()],
            ['CategoryName' => 'Laptop', 'created_at' => now(), 'updated_at' => now()],
            ['CategoryName' => 'Tablet', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('categories')->insert($categories);

        // 4. Brands
        $brands = [];
        for ($i = 0; $i < 10; $i++) {
            $brands[] = [
                'BrandName' => fake()->company(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('brands')->insert($brands);

        // 5. Gadgets
        $gadgets = [];
        for ($i = 0; $i < 20; $i++) {
            $gadgets[] = [
                'GadgetName' => ucfirst(fake()->word()),
                'CategoryID' => DB::table('categories')->inRandomOrder()->first()->CategoryID,
                'BrandID' => DB::table('brands')->inRandomOrder()->first()->BrandID,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('gadgets')->insert($gadgets);

        // 6. Buyers
        $buyers = [];
        for ($i = 0; $i < 20; $i++) {
            $buyers[] = [
                'BuyerName' => fake()->name(),
                'Phone' => fake()->phoneNumber(),
                'Email' => fake()->unique()->safeEmail(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('buyers')->insert($buyers);

        // 7. Stocks (purchases from suppliers)
        $stocks = [];
        foreach (DB::table('gadgets')->get() as $gadget) {
            for ($i = 0; $i < 2; $i++) {
                $stocks[] = [
                    'GadgetID' => $gadget->GadgetID,
                    'SupplierID' => DB::table('suppliers')->inRandomOrder()->first()->SupplierID,
                    'QuantityAdded' => fake()->numberBetween(5, 20),
                    'CostPrice' => fake()->randomFloat(2, 100, 5000),
                    'SellingPrice' => fake()->randomFloat(2, 200, 8000),
                    'PurchaseDate' => fake()->dateTimeThisYear(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('stocks')->insert($stocks);

        // 8. Transactions (sales/purchases)
        $transactions = [];
        for ($i = 0; $i < 50; $i++) {
            $transactions[] = [
                'GadgetID' => DB::table('gadgets')->inRandomOrder()->first()->GadgetID,
                'BuyerID' => DB::table('buyers')->inRandomOrder()->first()->BuyerID,
                'AdminID' => DB::table('admins')->inRandomOrder()->first()->AdminID,
                'StockID' => DB::table('stocks')->inRandomOrder()->first()->StockID,
                'TransactionType' => fake()->randomElement(['IN', 'OUT']),
                'Quantity' => fake()->numberBetween(1, 5),
                'TransactionDate' => fake()->dateTimeThisYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('transactions')->insert($transactions);

        // 9. TransactionItems (items per transaction)
        $transaction_items = [];
        foreach (DB::table('transactions')->get() as $transaction) {
            $transaction_items[] = [
                'TransactionID' => $transaction->TransactionID,
                'GadgetID' => $transaction->GadgetID,
                'Quantity' => $transaction->Quantity,
                'PriceAtSale' => DB::table('stocks')->where('StockID', $transaction->StockID)->value('SellingPrice') ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('transaction_items')->insert($transaction_items);

        // 10. Payments
        $payments = [];
        foreach (DB::table('transactions')->inRandomOrder()->limit(50)->get() as $transaction) {
            $payments[] = [
                'TransactionID' => $transaction->TransactionID,
                'Method' => fake()->randomElement(['Cash', 'Card', 'GCash']),
                'Amount' => fake()->randomFloat(2, 500, 20000),
                'PaymentDate' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('payments')->insert($payments);

        // Run demo users seeder
        $this->call(DemoUsersSeeder::class);
    }
}