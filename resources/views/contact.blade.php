@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container my-5">
  <h1 class="text-center text-warning mb-4">Contact Us</h1>

  <div class="row">
    <div class="col-md-6">
      <h4>Get in Touch</h4>
      <p>Have questions about our Chicken Lusania? Reach out!</p>

      <ul class="list-unstyled">
        <li><strong>Phone:</strong> +256 751 438 976 / +256 769 895 805</li>
        <li><strong>Email:</strong> support@calebschickenlusania.com</li>
        <li><strong>Address:</strong> Wandegeya Market Southwing ground floor</li>
      </ul>
    </div>

    <div class="col-md-6">
      <h4>Send a Message</h4>
      <form action="#" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label">Your Name</label>
          <input type="text" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Send Message</button>
      </form>
    </div>
  </div>
</div>
@endsection