# Shapely - ALPHV Internship Assignment
Candidate: Muhammad Itqan bin Azis
Date: 12th January 2026

# Project Overview
Shapely is a secure, decoupled web application for managing geometric data. It allows administrators to Create, Read, Update, and Delete (CRUD) shapes while providing a read-only view for public users.

# Live Links
GitHub Repository: [INSERT YOUR GITHUB LINK HERE]
Live Deployment: [INSERT LIVE SERVER LINK HERE - OR DELETE THIS LINE IF NOT DEPLOYED]

# Key Features Implemented
- UI: Clean design with responsive mobile layout (Vue.js).
- Security: Token-based authentication (Laravel Sanctum).
- Validation: Server-side input validation with instant UI feedback.
- Testing: Automated Feature tests included.


------Installation Guide------


This project consists of two parts/files:
1.  Backend: shapely-api (Laravel)
2.  Frontend: shapely-web (Vue 3 + Vite)

# Prerequisites
-PHP 8.2+
-Composer
-Node.js (v18+) & NPM
-MySQL


---- Backend Setup (API) -----


1.  Navigate to the backend folder:
    ```bash
    cd shapely-api
    ```

2.  Install PHP dependencies:
    ```bash
    composer install
    ```

3.  Configure Environment:
    * Copy `.env.example` and rename it to `.env`.
    * Open `.env` and configure your database settings:
    ```ini
    DB_DATABASE=alphv_assignment
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  Generate Application Key:
    ```bash
    php artisan key:generate
    ```

5.  **Run Migrations & Seeder (Crucial):**
    This creates the database tables and the **Admin User**.
    ```bash
    php artisan migrate --seed
    ```

6.  Start the Server:
    ```bash
    php artisan serve
    ```
    *The API will run at `http://localhost:8000` (or `http://shapely-api.test` if using Laragon).*


----- Frontend Setup (Web) ----


1.  Open a new terminal and navigate to the frontend folder:
    ```bash
    cd shapely-web
    ```

2.  Install Node dependencies:
    ```bash
    npm install
    ```

3.  Start the Development Server:
    ```bash
    npm run dev
    ```

4.  Open the link shown in the terminal (usually `http://localhost:5173`).


------ Access Credentials-------


To access the Admin Portal features (Add/Edit/Delete), use the following credentials:

Email: admin@alphv.com
Password: Admin123@


------ Automated Testing -----


This project includes automated feature tests to verify security and CRUD logic. To run them:

1.  Ensure you are in the `shapely-api` folder.
2.  Run the test suite:
    ```bash
    php artisan test
    ```

Expected Output:
-guests_cannot_add_items ✅ (PASS)
-logged_in_admin_can_add_items ✅ (PASS)
-logged_in_admin_can_delete_items ✅ (PASS)


------ Folder Structure------


/
├── shapely-api       # Laravel Backend (Controllers, Models, Tests)
├── shapely-web       # Vue 3 Frontend (Components, Assets, CSS)
└── README.md         # This file