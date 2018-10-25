@extends('layout')

@section('title', 'Profesiones')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">{{ $title }}</h1>

    </div>

    @if ($professions->isNotEmpty())
    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Titulo</th>
            <th scope="col">Perfiles</th>
            <th scope="col">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($professions as $profession)
        <tr>
            <th scope="row">{{ $profession->id }}</th>
            <td>{{ $profession->title }}</td>
            <td>{{ $profession->profiles_count }}</td>

            <td>
                @if($profession->profiles_count == 0)
                <form action="{{ route('professions.destroy', $profession) }}" method="POST">
                   @csrf
                    @method('DELETE')
                   {{-- <a href="{{ route('users.show', $user) }}" class="btn btn-link"><span class="oi oi-eye"></span></a>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-link"><span class="oi oi-pencil"></span></a>--}}
                    <button type="submit" class="btn btn-link"><span class="oi oi-trash"></span></button>
                </form>
                 @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p>No hay usuarios registrados.</p>
    @endif
@endsection

@section('sidebar')
    @parent
@endsection