@extends('errors::minimal')

@section('title', 'Error interno del servidor')
@section('code', '500')
@section('message', __('http.500'))
