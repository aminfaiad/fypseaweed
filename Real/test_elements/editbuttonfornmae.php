<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editable Name</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
            margin: 0;
        }

        .unique-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        .unique-editable {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            display: inline-block;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 5px 10px;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .unique-editable:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            background-color: #f8f9fa;
        }

        .unique-edit-button {
            cursor: pointer;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 10px;
        }

        .unique-edit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="unique-container">
        <div>
            <div id="unique-name" class="unique-editable" contenteditable="false">Marie</div>
        </div>
        <button id="unique-edit-button" class="unique-edit-button">✏️ Edit</button>
    </div>

    <script>
        const nameDiv = document.getElementById('unique-name');
        const editButton = document.getElementById('unique-edit-button');

        // Function to toggle editing
        function toggleEdit(save = false) {
            const isEditable = nameDiv.getAttribute('contenteditable') === 'true';
            if (isEditable || save) {
                // Save the edits
                nameDiv.setAttribute('contenteditable', 'false');
                editButton.textContent = '✏️ Edit';
            } else {
                // Make the name editable
                nameDiv.setAttribute('contenteditable', 'true');
                nameDiv.focus();
                editButton.textContent = 'Save';
            }
        }

        // Event listener for Edit button
        editButton.addEventListener('click', () => toggleEdit());

        // Event listener for Enter key to save
        nameDiv.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent new line
                toggleEdit(true); // Save changes
            }
        });
    </script>
</body>
</html>
