@extends('aorapagebuilder::layouts.master')

@section('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{themeAsset('css/sections/homepage_v8.css')}}">
@endsection

@section('content')
    @include(theme('snippets.components._home_page_banner_v8'))
    @include(theme('snippets.components._home_page_stats_v8'))
    @include(theme('snippets.components._home_page_teachers_v8'))
    @include(theme('snippets.components._home_page_features_v8'))
    @include(theme('snippets.components._home_page_gamification_v8'))
@endsection
