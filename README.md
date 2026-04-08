BSIT Feedback and Resolution System
===================================

A web-based system designed for the BSIT Organization at Eastern Visayas State University (EVSU) - Ormoc Campus. Developed using **HTML, CSS, JavaScript, PHP, and MySQL**.

This system facilitates structured submission, management, and tracking of feedback, along with a survey module to support organizational decision-making.

## Key Features (Epics & User Stories)

- **Epic A: Feedback Submission** 
  - Students can submit feedback (concerns, suggestions, etc.).
  - Supports controlled anonymity (hidden from officers/public, but tracked internally).
- **Epic B: Feedback Resolution Management**
  - Officers and Admins can review feedback.
  - Update status (`Pending`, `Under Review`, `Resolved`) and add resolution notes.
- **Epic C & F: Survey and Poll Management**
  - Officers/Admins can create unlimited surveys and polls for students to vote on.
  - Students can also create surveys/polls, but are limited to **1 per day**.
- **Epic D: Reporting and Monitoring**
  - Admins can view summary reports of feedback statuses.
  - Activity logs track system actions (login, feedback submission, status updates, etc.).
- **Epic E & G: Account Lifecycle & Approval**
  - Students sign up using their Student ID. 
  - New student accounts are set to **"Pending Approval"** and cannot log in until an Admin approves them.
  - Admins can **Deactivate** graduated students so they lose access to create surveys.

## Quick Setup Guide

1. **Move files to local server:** 
   Copy this folder into your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\feeds`).
2. **Setup the Database:**
   - Open phpMyAdmin (`http://localhost/phpmyadmin/`).
   - Create a new database named `bsit_feedback_system`.
   - Import the `database.sql` file provided in this folder.
   - *Note:* If you are updating from an older version, run `http://localhost/feedback/db_update.php` in your browser to apply the latest column changes (like `student_id` and `status` updates), then delete the file.
3. **Database Credentials:** 
   If your MySQL password is not blank, adjust the credentials in `config.php`.
4. **Run the Application:** 
   Open your browser and go to `http://localhost/feedback/`.

## Default Access & Roles

- **Creating the First Admin:**
  If you don't have an admin account yet, open `http://localhost/feedback/create_admin.php` in your browser. This will generate an admin account (Username: `admin@bsit.local`, Password: `password`). **Delete `create_admin.php` after use for security.**

- **User Roles:**
  - `student`: Must register via the Sign Up page. Must be approved by an Admin.
  - `officer`: Created manually in the database by an Admin. Can manage feedback and create unlimited surveys.
  - `admin`: Full system access, including User Management and Reports.

## Design Details
The UI is styled using custom CSS implementing an **EVSU-themed maroon and gold color palette**, featuring modern glass-morphism effects, responsive cards, and clean typography.
