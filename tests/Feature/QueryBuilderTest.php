<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\CounterSeeder;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp(); 
        DB::delete("delete from categories");
    }
    public function testInsert()
    {
        DB::table("categories")->insert([
            "id" => "GADGET",
            "name" => "Gadget"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food"
        ]);

        $result = DB::select("select count(id) as total from categories");
        self::assertEquals(2, $result[0]->total);
    }

    public function testSelect()
    {
        $this->testInsert();

        $collection = DB::table("categories")->select(["id", "name"])->get();
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

    }

    public function insertCategories()
    {
        DB::table("categories")->insert([
            "id" => "SMARTPHONE",
            "name" => "Smartphone",
            "created_at" => "2024-10-10 10:10:10"
        ]);

        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food",
            "created_at" => "2024-10-10 10:10:10"
        ]);

        DB::table("categories")->insert([
            "id" => "LAPTOP",
            "name" => "Laptop",
            "created_at" => "2024-10-10 10:10:10"
        ]);

        DB::table("categories")->insert([
            "id" => "FASHION",
            "name" => "Fashion",
            "created_at" => "2024-10-10 10:10:10"
        ]);
    }

    public function testWhere() // Cari menggunakan id
    {
        $this->insertCategories();

        $collection = DB::table("categories")->where(function (Builder $builder) {
            $builder->where('id', '=', 'SMARTPHONE');
            $builder->orWhere('id', '=', 'LAPTOP');
            // SELECT * FROM categories WHERE (id = smartphone OR id = laptop)
        })->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

    }

    public function testWhereBetween() // Cari diantara waktunya
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereBetween("created_at", ["2024-09-10 10:10:10", "2024-11-10 10:10:10"])
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereIn() // Didalam id
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereIn("id", ["SMARTPHONE", "LAPTOP"])->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

    }

    public function testWhereNull()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereNull("description")->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereDate() // Mencari di tanggal hari itu juga
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereDate("created_at", "2024-10-10")->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testUpdate() // Mengganti nama dari smartphone ke handphone
    {
        $this->insertCategories();

        DB::table("categories")->where("id", "=", "SMARTPHONE")->update([
            "name" => "Handphone"
        ]);

        $collection = DB::table("categories")->where("name", "=", "Handphone")->get();
        self::assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testUpsert() // Mengupdate atau jika tidak ada menambah ke dalam database
    {

        DB::table("categories")->updateOrInsert([
            "id" => "VOUCHER"
        ], [
            "name" => "Voucher",
            "description" => "Ticket and Voucher",
            "created_at" => "2024-10-10 10:10:10"
        ]);

        $collection = DB::table("categories")->where("id", "=", "Voucher")->get();
        self::assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }


}
