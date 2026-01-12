@extends('layouts.app')

@section('title', 'Order Successful')

@section('content')
<div class="container text-center my-5">
  <h1 class="text-warning">🎉 Order Placed!</h1>
  <p class="mt-3">Thank you for ordering from <b>Caleb’s Chicken Lusania</b>.</p>
  <p>Our team will contact you shortly via WhatsApp.</p>

  <a href="{{ route('home') }}" class="btn btn-warning mt-4">🏠 Back to Home</a>
</div>
@endsection