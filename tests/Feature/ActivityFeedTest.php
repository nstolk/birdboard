<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\ProjectFactory;
use Tests\Arrangements\ProjectFactorySetup;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_project_generates_activity()
    {
        $project = app(ProjectFactorySetup::class)->create();

        $this->assertCount(1, $project->activity);
        $this->assertEquals('created', $project->activity[0]->description);
    }

    public function test_updating_a_project_generates_activity()
    {
        $project = app(ProjectFactorySetup::class)->create();

        $project->update(['title' => 'changed']);

        $this->assertCount(2, $project->activity);
        $this->assertEquals('updated', $project->activity->last()->description);
    }
}
