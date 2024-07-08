<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
            <h2>Products</h2>
            <a href="{{ url('/Add_product') }}" class="btn btn-success">Add Product</a>
        </div>
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->price }}</td>
                        <td><img src="{{ url('uploads/'.$product->image) }}" alt="{{ $product->description }}" width="100"></td>
                        <td>
                            <a href="{{ url('/edit_product/'.$product->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ url('/delete_product/'.$product->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
