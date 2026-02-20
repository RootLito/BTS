# BFAR Trip Ticket & Booking System (BTS)

<p align="center">
  <img src="public/images/bfar.png" alt="BFAR Logo" width="120">
</p>

<p align="center">
  <strong>Streamlining Official Travel Logistics for BFAR</strong>
</p>

---

## 🚀 Overview
The **BFAR Booking and Trip Ticket System (BTS)** is a specialized logistics platform designed to streamline the request, approval, and management of official travel. It bridges the gap between administrative staff and vehicle operators, replacing manual paper-based processes with a real-time digital workflow.



## ✨ Key Features

### 👤 For Clients (Personnel)
- **Digital Requesting:** Submit trip requests with destination, purpose, and passenger details.
- **Live Notifications:** Receive instant updates via a dedicated notification bell when trips are Approved or modified.
- **Trip Tracking:** View personal history and real-time status of all submitted tickets.

### 🛠 For Administrators
- **Driver & Vehicle Assignment:** Effortlessly link available drivers and fleet vehicles to pending requests.
- **Dynamic Resource Management:** - Drivers/Vehicles automatically switch to **"On Trip"** upon approval.
    - Status resets to **"Available"** automatically upon trip completion or cancellation.
- **Centralized Dashboard:** A unified view to manage all logistics, drivers, and vehicle availability.



## 🛠 Tech Stack
- **Framework:** Laravel 11
- **UI Engine:** [Flux UI](https://fluxui.dev/) & Tailwind CSS
- **Frontend:** Alpine.js & Blade Components
- **Database:** MySQL
- **Date Handling:** Carbon (Human-readable timestamps)

## 📂 Project Structure (Key Modules)
- **Notifications:** A specialized system with `is_admin` flagging to separate internal staff alerts from client status updates.
- **Status Engine:** Hardcoded logic ensuring that no vehicle or driver is double-booked while on an active trip.
- **Searchable Selects:** Integrated Flux UI searchable components for quick driver assignment from large fleets.

## ⚙️ Installation

1. **Clone the repository**
   ```bash
   git clone [https://github.com/RootLito/BTS.git](https://github.com/RootLito/BTS.git)