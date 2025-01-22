const loadView = (view) => () => import(`@/views/${view}.vue`);

export default [
  { path: "/", name: "Home", component: loadView("Home"), meta: { requiresAuth: true } },
  { path: "/login", name: "Login", component: loadView("Login"), meta: { guestOnly: true } },
  { path: "/tutors", name: "Tutors", component: loadView("Tutors"), meta: { requiresAuth: true } },
  { path: "/renters", name: "Renters", component: loadView("Renters"), meta: { requiresAuth: true } },
  { path: "/rooms", name: "Rooms", component: loadView("Rooms"), meta: { requiresAuth: true } },
  { path: "/reservations", name: "Reservations", component: loadView("Reservations"), meta: { requiresAuth: true } },
  { path: "/invoices", name: "Invoices", component: loadView("Invoices"), meta: { requiresAuth: true } },
  { path: "/:pathMatch(.*)*", name: "NotFound", component: loadView("NotFound") }, // Page 404
];
