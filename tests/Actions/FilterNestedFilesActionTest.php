<?php

namespace Tests\Actions;

use App\Actions\FilterNestedFilesAction;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\TestCase;

class FilterNestedFilesActionTest extends TestCase
{

    use DatabaseTransactions;


    ///////////////////////////////////////////////////////////////////////////
    public function test_validation_failed(): void {
        $inode  = Inode::factory()->file()->make();
        $search = 'test';

        $this->expectException(ValidationException::class);

        $this->app->make(FilterNestedFilesAction::class)->execute($inode, $search);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully_filter_fetching_files(): void {
        $root         = Inode::find(Inode::ROOT_ID);
        $directory    = Inode::factory()->directory()->create(['name' => 'Documents']);
        $subdirectory = Inode::factory()->directory()->create(['name' => 'Pdfs']);
        $nestedFile   = Inode::factory()->file()->create(['name' => 'test123.pdf']);

        $nestedFile->recursiveParents()->sync(collect([
            $root,
            $directory,
            $subdirectory,
        ]));

        $this->assertSame([$root->id, $directory->id, $subdirectory->id], $nestedFile->recursiveParents()->get()->pluck('id')->toArray());

        $this->mock(StateValidator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('validate')->andReturnNull();
        });

        $search = 'test';

        $files = $this->app->make(FilterNestedFilesAction::class)->execute($root, $search);

        $this->assertCount(1, $files);
        $this->assertSame('test123.pdf', $files->first()->name);
        $this->assertTrue($files->first()->relationLoaded('recursiveParents'));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully_limit_fetched_files(): void {
        $root = Inode::find(Inode::ROOT_ID);

        for ($i = 10; $i < 25; $i++) {
            $nestedFile = Inode::factory()->file()->create(['name' => "test{$i}.pdf"]);
            
            $nestedFile->recursiveParents()->sync(collect([$root]));
        }

        $this->assertSame(15, $root->recursiveChildren->count());

        $this->mock(StateValidator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('validate')->andReturnNull();
        });

        $search = 'test';

        $files = $this->app->make(FilterNestedFilesAction::class)->execute($root, $search);

        $this->assertCount(10, $files);
        $this->assertSame('test10.pdf', $files->first()->name);
        $this->assertSame('test19.pdf', $files->last()->name);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully_filter_not_fetching_directories(): void {
        $root            = Inode::find(Inode::ROOT_ID);
        $directory       = Inode::factory()->directory()->create(['name' => 'Documents']);
        $subdirectory    = Inode::factory()->directory()->create(['name' => 'Pdfs']);
        $nestedDirectory = Inode::factory()->directory()->create(['name' => 'test234']);

        $nestedDirectory->recursiveParents()->sync(collect([
            $root,
            $directory,
            $subdirectory,
        ]));

        $this->assertSame([$root->id, $directory->id, $subdirectory->id], $nestedDirectory->recursiveParents()->get()->pluck('id')->toArray());

        $this->mock(StateValidator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('validate')->andReturnNull();
        });

        $search = 'test';

        $files = $this->app->make(FilterNestedFilesAction::class)->execute($root, $search);

        $this->assertCount(0, $files);
    }

}