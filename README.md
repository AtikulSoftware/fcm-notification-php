# PHP Firebase Notification (OOP)

A clean **PHP OOP implementation** for sending **Firebase Cloud Messaging (FCM) notifications** using **Service Account + OAuth2 Bearer Token**, without using Composer.

This project is Docker-ready and fully configurable for multiple environments.

---

## Features

- Pure **OOP PHP** structure
- Firebase OAuth2 token generation using **Service Account JSON**
- Automatic token refresh
- Supports **single device notifications**
- Data payload values automatically converted to **strings** (required by FCM)
- Docker-ready
- JSON output for API-friendly responses
- Easy to extend for **multi-device** or **topic notifications**

---

### Project Structure

- `src/`
  - `config/`
    - `FirebaseConfig.php`: Firebase configuration
  - `classes/`
    - `FirebaseService.php`: OAuth2 token handling
    - `FirebaseNotification.php`: Notification sending
  - `send.php`: Example entry point
