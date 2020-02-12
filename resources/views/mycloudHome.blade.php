@extends('layouts.mycloudLayout')


@section('vueScript')
<script> window.csrf_token="{{ csrf_token() }}"</script>
<script src="{{ asset('js/mycloudApp.js') }}" defer></script>
<script src="{{ asset('js/layoutApp.js') }}" defer></script>
@endsection

@section('content')

            <mycloud-component></mycloud-component>

@endsection

