@extends ('layouts.app')

@section('content')
    <div class="flex items-center mb-4">
        <a href="/projects/create">New Project</a>
    </div>

    <div class="flex">
        <div>
            @forelse($projects as $project)
                <div class="bg-white mr-4 rounded p-3 shadow w-1/3" style="height: 200px; width: 200px;">
                <h3 class="font-normal text-xl py-4">{{ $project->title }}</h3>
                <div class="text-grey">{{ str_limit($project->description, 100) }}</div>
                </div>
        </div>
        @empty
            <div>No projects yet</div>
        @endforelse
    </div>
@endsection
