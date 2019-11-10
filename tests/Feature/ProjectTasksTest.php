<?php

namespace Tests\Feature;

use App\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Arrangements\ProjectFactorySetup;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_add_tasks_to_projects()
    {
        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

    public function test_only_the_owner_of_a_project_may_add_tasks()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }

    public function test_only_the_owner_of_a_project_may_update_a_task()
    {
        $this->signIn();

        $project = app(ProjectFactorySetup::class)->withTasks(1)->create();

        $this->patch($project->tasks[0]->path(), ['body' => 'Changed task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Changed task']);
    }

    public function test_a_project_can_have_tasks()
    {
        $project = app(ProjectFactorySetup::class)->create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    public function test_a_task_can_be_updated()
    {
        $project = app(ProjectFactorySetup::class)
            ->withTasks(1)
            ->create();

        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'changed',
                'completed' => true
            ]);

//        $this->signIn();

//        $project = auth()->user()->projects()->create(
//            factory(Project::class)->raw()
//        );
//
//        $task = $project->addTask('Test task');
//
//        $this->patch($project->path() . '/tasks/' . $task->id, [
//            'body' => 'changed',
//            'completed' => '1'
//        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => true
        ]);
    }

    public function test_a_task_requires_a_body()
    {
        $project = app(ProjectFactorySetup::class)->create();

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');
    }
}
