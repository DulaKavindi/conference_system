const express = require('express');
const fs = require('fs');
const path = require('path');
const QRCode = require('qrcode');
const bodyParser = require('body-parser');
const mysql = require('mysql2');
const app = express();
const port = 3000;

// Middlewares
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// MySQL database connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root', // Your MySQL username
    password: '', // Your MySQL password
    database: 'conference_db' // Your database name
});

db.connect((err) => {
    if (err) {
        console.error('Error connecting to the database:', err);
        process.exit();
    }
    console.log('Connected to the database');
});

// Directory for saving QR codes
const qrCodeDir = path.join(__dirname, 'qrcodes');
if (!fs.existsSync(qrCodeDir)) {
    fs.mkdirSync(qrCodeDir); // Create 'qrcodes' directory if it doesn't exist
}

// API to generate QR code for participant registration
app.post('/register', async (req, res) => {
    const { name, email, session_id } = req.body;

    // Validate input data
    if (!name || !email || !session_id) {
        return res.status(400).json({ message: 'Missing required fields' });
    }

    // Create the data to be encoded in the QR code (participant's info)
    const qrData = `Name: ${name}, Email: ${email}, Session ID: ${session_id}`;

    try {
        // Generate the QR code and save it as an image file
        const fileName = `${name.replace(/\s+/g, '_')}_QR.png`; // Replace spaces with underscores for the filename
        const filePath = path.join(qrCodeDir, fileName); // Save the QR code in the 'qrcodes' directory

        await QRCode.toFile(filePath, qrData, {
            color: {
                dark: '#000000',  // QR code color (black)
                light: '#FFFFFF'  // Background color (white)
            },
            width: 300  // QR code image width
        });

        // Insert participant data into the database
        const query = 'INSERT INTO participants (name, email, session_id, qr_code_path) VALUES (?, ?, ?, ?)';
        db.execute(query, [name, email, session_id, `/qrcodes/${fileName}`], (err, result) => {
            if (err) {
                console.error('Error inserting data into the database:', err);
                return res.status(500).json({ message: 'Error saving data to database' });
            }

            // Respond with participant data (including the QR code path)
            res.status(200).json({
                message: 'Registration successful',
                participantData: {
                    name,
                    email,
                    session_id,
                    qr_code_path: `/qrcodes/${fileName}`
                }
            });
        });

    } catch (error) {
        console.error('Error generating QR code:', error);
        res.status(500).json({ message: 'Error generating QR code' });
    }
});

// Serve the QR codes directory (so that the frontend can access the QR images)
app.use('/qrcodes', express.static(qrCodeDir));

// Start the server
app.listen(port, () => {
    console.log(`Server is running on http://localhost:${port}`);
});
