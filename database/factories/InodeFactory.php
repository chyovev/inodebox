<?php

namespace Database\Factories;

use App\Enums\InodeTypeEnum;
use App\Models\Inode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

/**
 * @extends Factory<Inode>
 */
class InodeFactory extends Factory
{

    ///////////////////////////////////////////////////////////////////////////
    public function definition(): array {
        return [
            'name' => File::basename($this->faker->filePath()),
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    public function file(): static {
        return $this->state(fn (): array => [
            'type' => InodeTypeEnum::FILE,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function directory(): static {
        return $this->state(fn (): array => [
            'type' => InodeTypeEnum::DIRECTORY,
        ]);
    }
}
