<html>
<head>
    <title>Create Calendar Event</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4285f4;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: none;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Create Calendar Event</h1>
    
    <div id="successAlert" class="alert alert-success"></div>
    <div id="errorAlert" class="alert alert-error"></div>

    <form id="eventForm">
        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label>Start Time:</label>
            <input type="datetime-local" name="start_time" required>
        </div>
        <div class="form-group">
            <label>End Time:</label>
            <input type="datetime-local" name="end_time" required>
        </div>
        <div class="form-group">
            <label>Attendee Email:</label>
            <input type="email" name="attendees[]">
        </div>
        <button type="submit">Create Event</button>
    </form>

    <script>
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {};
        formData.forEach((value, key) => {
            if (key === 'attendees[]' && value) {
                if (!data.attendees) data.attendees = [];
                data.attendees.push(value);
            } else {
                data[key] = value;
            }
        });
        
        fetch('/calendar/event', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            
            if (data.success) {
                successAlert.textContent = 'Event created successfully!';
                successAlert.style.display = 'block';
                errorAlert.style.display = 'none';
                document.getElementById('eventForm').reset();
            } else {
                errorAlert.textContent = 'Error: ' + data.message;
                errorAlert.style.display = 'block';
                successAlert.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>