@extends("moonshine::layouts.app")

@section('sidebar-inner')
    @parent

    <div class="text-center mt-8">
        @include('moonshine::shared.btn', [
            'title' => trans('moonshine::ui.back'),
            'href' => $resource->route("index"),
            'filled' => true,
            'icon' => false,
        ])
    </div>
@endsection

@section('header-inner')
    @parent
@endsection

@section('header-inner')
    @parent
@endsection

@section('content')
    @include("moonshine::shared.title", ["title" => $resource->title()])

    <div class="mt-8"></div>

    <div class="flex flex-col mt-8">
        @include("moonshine::base.form.form", ["item" => $item])
    </div>

    @include("moonshine::base.changelog", ["resource" => $resource, "item" => $item])
@endsection