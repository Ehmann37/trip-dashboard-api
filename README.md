<div align="center">

  <img src="/public/logo.png" alt="logo" width="200" height="auto" />
  <h1>TRIP: Transit Routing & Integrated Payments</h1>
  
  <p>
    Backend for TRIP-bus: A 'Transit Routing and Integrated Payments' System 
  </p>

  <h4>
    <a href="https://github.com/gian-gg/TRIP-bus">Bus Site</a>
  <span> · </span>
    <a href="https://github.com/Ehmann37/TRIP-bus-api">Bus API</a>
  <span> · </span>
    <a href="https://github.com/gian-gg/TRIP-dashboard">Dashboard Site</a>
  <span> · </span>
    <a href="https://github.com/Ehmann37/TRIP-dashboard-api">Dashboard API</a>
  </h4>

![Tech Stack](https://skills-icons.vercel.app/api/icons?i=php,xampp,mysql)

</div>

<br />

## 📂 About This API

This backend powers the **dashboard** interface of the TRIP system, focused on two main user roles:

- **Conductors** – Upload trip summaries and view assignments.
- **Operators** – Access real-time analytics, assign buses, conductors, drivers, and manage route configurations.

---

## 🔑 Role-Based Features

### 👨‍✈️ Conductor Capabilities

| Feature | Description |
|--------|-------------|
| ✅ Login | Conductors can log in using their credentials. |
| 🚌 Assigned Bus View | After login, they can see which **bus** they’re assigned to and who their **drivers** are. |
| 📤 Trip Summary Upload | Can upload `.enc` files downloaded from the bus site, which will populate trip data into the dashboard database for operator analysis. |

---

### 🧑‍💼 Operator Capabilities

| Feature | Description |
|--------|-------------|
| ✅ Login | Operators can log in securely using their credentials. |
| 📊 Dashboard Overview | View summarized company data including: |
| &nbsp; | - 💰 **Total revenue** |
| &nbsp; | - 📈 **Financial statistics per date range** |
| &nbsp; | - 🧾 **Revenue by passenger type (e.g. PWD, Student)** |
| &nbsp; | - 💳 **Stats by payment method (cash, online)** |
| 🗺️ Route Analytics | Monitor route popularity, number of trips taken per route, and performance. |
| 🔧 Assignment System | Assign: |
| &nbsp; | - ✅ Conductor and Driver to a specific Bus |
| &nbsp; | - 🛣️ Route to a specific Bus |


## 👥 The Team

[![Geri Gian Epanto](https://github.com/gian-gg.png?size=48 'Geri Gian Epanto')](https://github.com/gian-gg) [![Emmanuel Cañete](https://github.com/Ehmann37.png?size=48 'Emmanuel Cañete')](https://github.com/Ehmann37) [![Czachary Villarin](https://github.com/ccxavi.png?size=48 'Czachary Villarin')](https://github.com/ccxavi) [![Ryan Romero](https://github.com/arynn1.png?size=48 'Ryan Romero')](https://github.com/arynn1)

## 📜 License

This project is **open-source** but **not free for commercial use**.

- ✅ **Allowed**: View, modify, and use for **non-commercial** projects.
- ❌ **Prohibited**: Selling, redistributing, or monetizing this code without permission.

For commercial licensing, contact epanto.gg@gmail.com.
