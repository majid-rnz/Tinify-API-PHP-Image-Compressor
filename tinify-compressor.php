<?php

function compressImageWithCurl($sourcePath, $destinationPath, $apiKey)
{
    // Read the image file content
    $imageData = file_get_contents($sourcePath);

    // Set up the request URL and headers
    $url = "https://api.tinify.com/shrink";
    $headers = [
        "Authorization: Basic " . base64_encode("api:$apiKey"),
        "Content-Type: application/octet-stream",
        "Content-Length: " . strlen($imageData)
    ];

    // Initialize CURL request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $imageData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Send the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode === 201) { // Request succeeded
        $responseData = json_decode($response, true);
        $compressedImageUrl = $responseData['output']['url'];

        // Download the compressed image and save it to the destination path
        $compressedImage = file_get_contents($compressedImageUrl);
        file_put_contents($destinationPath, $compressedImage);

        echo "Image successfully compressed and saved!";
    } else {
        echo "Error compressing image: HTTP Code $httpCode. Response: $response";
    }

    // Close CURL
    curl_close($ch);
}

// Usage of the function
$sourceImage = "path/to/your/source/image.jpg"; // Path to the input image
$destinationImage = "path/to/your/destination/image.jpg"; // Path to save the compressed image
$apiKey = "YOUR_API_KEY"; // Replace with your API key

compressImageWithCurl($sourceImage, $destinationImage, $apiKey);
?>
