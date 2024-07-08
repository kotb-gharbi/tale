
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>
<body>
    <h2>Add Product</h2>
    <form action="{{ url('/Add_product') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description" required><br><br>

        <label for="price">Price:</label><br>
        <input type="text" id="price" name="price" required><br><br>

        <label for="image">Image:</label><br>
        <input type="file" id="image" name="image"  required><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
