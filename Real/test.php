<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editable Name</title>
    <style>
        .editable {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            display: inline-block;
            margin-right: 10px;
            border: 1px solid transparent;
            padding: 2px 5px;
        }
        .editable:focus {
            outline: none;
            border: 1px solid #007bff;
            background-color: #f8f9fa;
        }
        .edit-button {
            cursor: pointer;
            color: #007bff;
            font-size: 16px;
        }
        .edit-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div>
        <div id="name" class="editable" contenteditable="false">Marie</div>
        <span id="editButton" class="edit-button">✏️ Edit</span>
    </div>

    <script>
        const nameDiv = document.getElementById('name');
        const editButton = document.getElementById('editButton');

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
