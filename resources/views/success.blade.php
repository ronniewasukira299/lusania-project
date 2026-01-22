Success
@extends('layouts.app')

@section('title', 'Order Successful')

@section('content')
<div class="container text-center my-5">
  <h1 class="text-warning">🛒 Order Placed!</h1>

  @if(request()->query('method') == 'system')
    <p class="mt-3">Thank you for ordering from <b>Caleb’s Chicken Lusania</b>.</p>
    <p>Your order has been recorded in our system and assigned to an available staff member. We'll process it shortly.</p>
  @else
    <p class="mt-3">Thank you for ordering from <b>Caleb’s Chicken Lusania</b>.</p>
    <p>Our team will contact you shortly via WhatsApp.</p>
  @endif

  <a href="{{ route('products') }}" class="btn btn-warning mt-4">← Back to Products</a>
</div>
@endsection