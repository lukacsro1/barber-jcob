@extends('layouts.admin')


@section('content')
<div class="max-w-6xl mx-auto">
    <div id="services"
         data-services="{{ json_encode($services) }}"
         data-user="{{ json_encode($user) }}">
    </div>
</div>

@endsection
