@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Join Group: {{ $group->name ?? 'Group' }}
                </div>

                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="mb-2">
                        <strong>Description:</strong>
                        {{ $group->description ?? 'No description provided.' }}
                    </p>

                    <p class="mb-3">
                        <strong>Members:</strong>
                        {{ $group->members_count ?? $group->members->count() ?? 'N/A' }}
                    </p>

                    @if(isset($isMember) && $isMember)
                        <div class="alert alert-info">
                            You are already a member of this group.
                        </div>

                        <form method="POST" action="{{ route('groups.leave', $group->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Leave Group</button>
                            <a href="{{ route('groups.show', $group->id) }}" class="btn btn-link">Back</a>
                        </form>
                    @else
                        <form method="POST" action="{{ route('groups.join', $group->id) }}">
                            @csrf

                            <div class="form-group">
                                <label for="message">Message (optional)</label>
                                <textarea id="message" name="message" class="form-control" rows="4" placeholder="Introduce yourself or explain why you want to join">{{ old('message') }}</textarea>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">Request to Join</button>
                                <a href="{{ route('groups.index') }}" class="btn btn-link">Back to groups</a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection