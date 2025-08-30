# Water Percentage Meter

A simple, elegant web-based application for tracking daily water intake percentage. Features a visual water meter with smooth animations, increment buttons, and persistent storage of daily progress.

## Features

- **Visual Water Meter**: Animated meter that fills up as you track your water intake
- **Easy Controls**: Buttons to increment by 1%, 5%, or 10%
- **Reset Functionality**: Quickly reset the meter to 0%
- **Persistent Storage**: Saves your daily progress to a JSON file
- **Responsive Design**: Works on desktop and mobile devices
- **Smooth Animations**: Fluid transitions and visual feedback

## Technologies Used

- **Backend**: PHP (for data persistence)
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Storage**: JSON file for simple data persistence

## Installation

1. Clone or download this repository to your web server
2. Ensure your server has PHP support (version 7.0 or higher recommended)
3. Place the files in a web-accessible directory
4. No additional dependencies required - pure PHP and vanilla JavaScript

## Usage

1. Open `water.php` in your web browser
2. Use the +1, +5, or +10 buttons to increment your water intake percentage
3. The meter will visually fill up and animate accordingly
4. Your progress is automatically saved for the current day
5. Use the Reset button to start over
6. The app will remember your progress when you return

## File Structure

- `water.php` - Main application file (HTML/CSS/JS)
- `water_save.php` - Backend API for saving/loading data
- `water_state.json` - Data storage file (created automatically)
- `README.md` - This file
- `LICENSE` - Project license

## API Endpoints

### GET /water_save.php
Retrieves the current day's water percentage.

**Response:**
```json
{
  "date": "2025-08-30",
  "percent": 75
}
```

### POST /water_save.php
Saves the water percentage for the current day.

**Request Body:**
```json
{
  "percent": 75
}
```

**Response:**
```json
{
  "ok": true,
  "date": "2025-08-30",
  "percent": 75
}
```

## Customization

The visual styling can be easily customized by modifying the CSS variables in `water.php`:

- `--bg`: Background color
- `--panel`: Panel background
- `--accent`: Primary accent color
- `--accent-strong`: Stronger accent for full meter
- `--muted`: Muted text color

## Browser Support

Works in all modern browsers that support:
- CSS Custom Properties (CSS Variables)
- ES6 JavaScript features
- Fetch API

## License

This project is open source and available under the terms of the LICENSE file included in this repository.

## Contributing

Feel free to submit issues, feature requests, or pull requests to improve this project!

## Demo

To see the app in action, simply open `water.php` in any modern web browser with PHP support.