@extends('aorapagebuilder::layouts.master')

@section('styles')
    <link rel="stylesheet" href="{{themeAsset('css/sections/homepage_v8.css')}}">
@endsection

@section('content')
    @include(theme('snippets.components._home_page_banner_v8'))
    @include(theme('snippets.components._home_page_stats_v8'))
    @include(theme('snippets.components._home_page_teachers_v8'))
    @include(theme('snippets.components._home_page_features_v8'))
    @include(theme('snippets.components._home_page_gamification_v8'))
@endsection
