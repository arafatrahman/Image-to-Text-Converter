<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image to Text with OCR</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@2.1.1/dist/tesseract.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .upload-section {
            margin: 20px 0;
        }
        .result-section {
            margin: 20px 0;
        }
        #progressBar {
            width: 100%;
            height: 25px;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        #progress {
            height: 100%;
            width: 0%;
            background-color: #4caf50;
        }
        .download-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .download-button:disabled {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <h1>Upload an Image to Extract Text</h1>

    <div class="upload-section">
        <input type="file" id="imageUpload" accept="image/*">
        <button onclick="extractText()">Extract Text</button>
    </div>

    <div id="progressBar">
        <div id="progress"></div>
    </div>

    <div class="result-section" id="result">
        <!-- Extracted text will appear here -->
    </div>

    <button id="downloadBtn" class="download-button" disabled onclick="downloadText()">Download Text</button>

    <script>
        let extractedText = ''; // Variable to store extracted text

        function extractText() {
            const fileInput = $('#imageUpload')[0].files[0];

            if (!fileInput) {
                alert('Please upload an image!');
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                const img = new Image();
                img.src = e.target.result;

                img.onload = function () {
                    // Displaying progress bar while the OCR is processing
                    $('#progress').css('width', '0%');
                    $('#result').text('Processing...');

                    // Run Tesseract.js OCR
                    Tesseract.recognize(
                        img,
                        'eng', // language
                        {
                            logger: function (m) {
                                if (m.status === 'recognizing text') {
                                    const progress = Math.round(m.progress * 100);
                                    $('#progress').css('width', progress + '%');
                                }
                            }
                        }
                    ).then(({ data: { text } }) => {
                        // Store the extracted text and update UI
                        extractedText = text;
                        $('#result').text(text);

                        // Enable the download button after OCR completion
                        $('#downloadBtn').prop('disabled', false);
                    }).catch(err => {
                        console.error(err);
                        $('#result').text('An error occurred while extracting text.');
                    });
                };
            };

            reader.readAsDataURL(fileInput);
        }

        function downloadText() {
            if (!extractedText) {
                alert('No text available to download!');
                return;
            }

            const blob = new Blob([extractedText], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'extracted_text.txt'; // Set the file name for download
            a.click();
            URL.revokeObjectURL(url); // Clean up after download
        }
    </script>
</body>
</html>
