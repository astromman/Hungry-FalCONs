@extends('layouts.buyer.buyermaster')

@section('content')
<!-- Main Section -->
<div class="container my-5 flex-grow-1">
    <div class="row">
        <!-- Text and Search Box Section -->
        <div class="col-lg-6 mb-4">
            <div>
                <h1 class="custom-font">We provide a platform for your cravings</h1>
            </div>
            <div class="searchbox-wrap">
                <form action="{{ route('searchItem') }}" method="GET" class="search-bar">
                    <input type="text" name="query" placeholder="Search for something..." required>
                    <button type="submit"><span>Submit</span></button>
                </form>
            </div>
        </div>

        <!-- Carousel Section -->
        <div class="col-lg-6">
            <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{asset('images/bg/default_shop_image.png')}}" class="d-block w-100" alt="Image 1">
                    </div>
                    @foreach($products as $product)
                    <div class="carousel-item" style="height: 370px;">
                    <img src="{{ asset('storage/products/' . $product->image) }}" class="d-block w-100" style="object-fit: cover; height: 100%;" title="{{ $product->product_name }}" alt="{{ $product->product_name }}">
                    </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Canteen Section -->
    <div class="row mt-5">
        <h3>Canteens</h3>
        @foreach($canteens as $canteen)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow" style="width: 100%;">
                <!-- Background Image Container -->
                @if(is_null($canteen->building_image))
                <div class="position-relative" style="height: 150px; overflow: hidden;">
                    <div class="position-absolute w-100 h-100"
                        style="background-image: url('{{ asset('images/bg/default_shop_image.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;">
                    </div>
                    <!-- Gradient Overlay -->
                    <div class="position-absolute w-100 h-100"
                        style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3));">
                    </div>
                    <!-- Title and Icon -->
                    <div class="position-relative text-white p-3" style="z-index: 1;">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="fas fa-map-marker-alt me-2"></i>
                        </div>
                        <h5 class="card-title text-white">{{ $canteen->building_name }}</h5>
                    </div>
                </div>
                @else
                <div class="position-relative" style="height: 150px; overflow: hidden;">
                    <div class="position-absolute w-100 h-100"
                        style="background-image: url('{{ asset('storage/canteen/' . $canteen->building_image) }}'); 
                    background-size: cover; 
                    background-repeat: no-repeat; 
                    background-position: center;">
                    </div>
                    <!-- Gradient Overlay -->
                    <div class="position-absolute w-100 h-100"
                        style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3));">
                    </div>
                    <!-- Title and Icon -->
                    <div class="position-relative text-white p-3" style="z-index: 1;">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="fas fa-map-marker-alt me-2"></i>
                        </div>
                        <h5 class="card-title text-white">{{ $canteen->building_name }}</h5>
                    </div>
                </div>
                @endif

                <!-- Card Body -->
                <a href="{{ route('visit.canteen', ['id' => $canteen->id, 'building_name' => Str::slug($canteen->building_name)]) }}" class="text-dark stretched-link" style="text-decoration: none;">
                    <div class="card-body d-flex flex-column justify-content-between" style="height: 100px; ">
                        <p class="card-text mb-1">Available Shops: {{ $canteen->shops->where('status', 'Verified')->count() }}</p>
                        <small>{{ $canteen->building_description }}</small>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- CSS for adjustments -->
<style>
    .search-bar {
        display: flex;
        align-items: center;
        background-color: #fff;
        /* The entire search bar remains white */
        border-radius: 50px;
        /* Fully rounded corners */
        overflow: hidden;
        width: 100%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Soft shadow */
    }

    .search-bar input {
        flex-grow: 1;
        padding: 22px 20px;
        border: none;
        outline: none;
        font-size: 16px;
        border-top-left-radius: 50px;
        border-bottom-left-radius: 50px;
        box-shadow: none;
        /* Removes any shadow from input */
    }

    .search-bar button {
        padding: 14px 30px;
        background-color: white;
        /* Button will be fully white */
        color: #5b9bd5;
        /* Text color remains blue */
        border: none;
        /* Removes the border around the button */
        border-top-right-radius: 50px;
        border-bottom-right-radius: 50px;
        cursor: pointer;
        transition: none;
        /* No hover effects */
        outline: none;
        /* Removes default outline */
        box-shadow: none;
        /* Removes shadow from the button */
    }




    /* Adjust card layout */
    .card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    .card-body {
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stretched-link {
        color: inherit;
        text-decoration: none;
    }

    .stretched-link:hover {
        text-decoration: underline;
    }

    .carousel-image {
        height: 400px; /* Set a fixed height */
        width: 100%; /* Make the width fill the carousel */
        object-fit: cover; /* Ensure the image covers the area, cropping if necessary */
    }
</style>

@endsection