<x-layouts.admin title="users">
    <div class="container mt-5">
        <h1>Chat Users</h1>
        <ul class="list-group mt-3">
            @foreach ($users as $user)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('chat.chat', $user->id) }}">{{ $user->name }}</a>
                    <span class="badge {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }}">
                        {{ $user->isOnline() ? 'Online' : 'Offline' }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.admin>
