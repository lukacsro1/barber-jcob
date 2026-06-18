import { createApp } from "vue";
import App from "./components/App.vue";
import StatsWidget from "./components/StatsWidget.vue";
import BookingApp from "./components/BookingApp.vue";
import BarberGallery from "./components/BarberGallery.vue";
import AdminDashboard from "./components/admin/AdminDashboard.vue";
import AdminSidebar from "./components/admin/AdminSidebar.vue";
// Main App (Login)
if (document.getElementById("app")) {
    const app = createApp(App);
    app.mount("#app");
}

// Stats Widget (Dashboard)
if (document.getElementById("vue-stats")) {
    const el = document.getElementById("vue-stats");
    const userData = JSON.parse(el.dataset.user || "{}");
    const app = createApp(StatsWidget, { userData });
    app.mount("#vue-stats");
}

// Booking App (Client Facing)
if (document.getElementById("booking-app")) {
    const app = createApp(BookingApp);
    app.mount("#booking-app");
}

// Barber Gallery (Home Page)
if (document.getElementById("barber-gallery")) {
    const app = createApp(BarberGallery);
    app.mount("#barber-gallery");
}

// Admin Vue Dashboard
if (document.getElementById("admin-dashboard")) {
    const el = document.getElementById("admin-dashboard");
    const userData = JSON.parse(el.dataset.user || "{}");
    const statsData = JSON.parse(el.dataset.stats || "{}");
    const servicesData = JSON.parse(el.dataset.services || "[]");
    const app = createApp(AdminDashboard, {
        userData,
        statsData,
        servicesData,
    });
    app.mount("#admin-dashboard");
}

// Admin Vue Sidebar
if (document.getElementById("vue-admin-sidebar")) {
    const el = document.getElementById("vue-admin-sidebar");
    const currentPath = el.dataset.path || "admin";
    const userData = JSON.parse(el.dataset.user || "{}");
    const app = createApp(AdminSidebar, { currentPath, userData });
    app.mount("#vue-admin-sidebar");
}

// Barber Management
import BarberManagement from "./components/admin/BarberManagement.vue";
if (document.getElementById("barber-management")) {
    const el = document.getElementById("barber-management");
    const initialBarbers = JSON.parse(el.dataset.barbers || "[]");
    const app = createApp(BarberManagement, { initialBarbers });
    app.mount("#barber-management");
}

// Appointment Calendar
import AppointmentCalendar from "./components/admin/AppointmentCalendar.vue";
if (document.getElementById("appointment-calendar")) {
    const el = document.getElementById("appointment-calendar");
    const appointments = JSON.parse(el.dataset.appointments || "[]");
    const barbers = JSON.parse(el.dataset.barbers || "[]");
    const services = JSON.parse(el.dataset.services || "[]");
    const app = createApp(AppointmentCalendar, { appointments, barbers, services });
    app.mount("#appointment-calendar");
}
// Barber Management
import CustomerManagement from "./components/admin/CustomerManagement.vue";
if (document.getElementById("customer-management")) {
    const el = document.getElementById("customer-management");
    const initialCustomers = JSON.parse(el.dataset.customers || "[]");
    const app = createApp(CustomerManagement, { initialCustomers });
    app.mount("#customer-management");
}

// Services Management
import Services from "./components/admin/Services.vue";
if (document.getElementById("services")) {
    const el = document.getElementById("services");
    const initialServices = JSON.parse(el.dataset.services || "[]");
    const user = JSON.parse(el.dataset.user || "{}");
    const app = createApp(Services, { initialServices, user });
    app.mount("#services");
}
