<?php

namespace Database\Seeders;

use App\Enums\InodeTypeEnum;
use App\Models\Inode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InodeSeeder extends Seeder
{

    use WithoutModelEvents;

    ///////////////////////////////////////////////////////////////////////////
    public function run(): void {
        if ( ! Inode::exists(Inode::ROOT_ID)) {
            $this->createRootNode();
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    private function createRootNode(): void {
        Inode::create([
            'type' => InodeTypeEnum::DIRECTORY,
            'name' => '/',
        ]);
    }
}
