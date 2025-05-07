let map;
let directionsService;
let directionsRenderer;
let currentUser = null;

// Dummy road feedback data
const roadFeedback = {
  "Uttar": 2,
  "Kabir": 4,
  "Airport": 5
};

function initMap() {
  map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: 23.8705, lng: 90.4213 },
    zoom: 13,
  });

  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer();
  directionsRenderer.setMap(map);
}

// AI route scoring
function scoreRoute(route) {
  let score = 0;
  const steps = route.legs[0].steps;
  steps.forEach((step) => {
    const road = step.instructions.replace(/<[^>]*>?/gm, '').split(" ")[0];
    score += roadFeedback[road] || 3;
  });
  return score / steps.length;
}

function findSafeRoute() {
  const start = document.getElementById("start").value;
  const end = document.getElementById("end").value;

  directionsService.route({
    origin: start,
    destination: end,
    travelMode: google.maps.TravelMode.BICYCLING,
    provideRouteAlternatives: true
  }, (result, status) => {
    if (status === "OK") {
      const safestRoute = result.routes.reduce((best, current) => {
        return scoreRoute(current) > scoreRoute(best) ? current : best;
      });
      directionsRenderer.setDirections({ routes: [safestRoute] });
    } else {
      alert("Could not find a safe route.");
    }
  });
}

// Feedback modal
function toggleFeedback() {
  const modal = document.getElementById("feedbackModal");
  modal.style.display = modal.style.display === "block" ? "none" : "block";
}

function submitFeedback() {
  const road = document.getElementById("roadName").value;
  const rating = parseInt(document.getElementById("conditionRating").value);
  if (road && rating) {
    roadFeedback[road] = rating;
    alert("Feedback submitted.");
    toggleFeedback();
  } else {
    alert("Please enter a road name and condition.");
  }
}

// Auth modal
function toggleAuth() {
  const modal = document.getElementById("authModal");
  modal.style.display = modal.style.display === "block" ? "none" : "block";
  showLogin();
}

// Show login
function showLogin() {
  document.getElementById("authTitle").textContent = "Login";
  document.getElementById("loginForm").style.display = "block";
  document.getElementById("signupForm").style.display = "none";
  document.getElementById("userInfo").style.display = currentUser ? "block" : "none";
}

// Show signup
function showSignup() {
  document.getElementById("authTitle").textContent = "Sign Up";
  document.getElementById("loginForm").style.display = "none";
  document.getElementById("signupForm").style.display = "block";
  document.getElementById("userInfo").style.display = "none";
}

// Register
function registerUser() {
  currentUser = {
    phone: document.getElementById("phone").value,
    email: document.getElementById("email").value,
    age: document.getElementById("age").value,
    gender: document.getElementById("gender").value,
    nationality: document.getElementById("nationality").value,
    password: document.getElementById("password").value
  };
  alert("Account created. You can now log in.");
  showLogin();
}

// Login
function loginUser() {
  const phone = document.getElementById("loginPhone").value;
  const password = document.getElementById("loginPassword").value;

  if (currentUser && phone === currentUser.phone && password === currentUser.password) {
    document.getElementById("profileDetails").innerHTML = `
      Phone: ${currentUser.phone}<br>
      Email: ${currentUser.email}<br>
      Age: ${currentUser.age}<br>
      Gender: ${currentUser.gender}<br>
      Nationality: ${currentUser.nationality}
    `;
    document.getElementById("loginForm").style.display = "none";
    document.getElementById("userInfo").style.display = "block";
  } else {
    alert("Invalid credentials or no user found.");
  }
}

// Logout
function logoutUser() {
  document.getElementById("userInfo").style.display = "none";
  showLogin();
}

// ====== WEATHER DATA ======
const apiKey = "57b65542f54d567576d4aa52c2ee6db8";
const city = "Dhaka";

function loadWeather() {
  fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`)
    .then(response => response.json())
    .then(data => {
      const weatherBox = document.getElementById("weather");
      const temp = data.main.temp;
      const condition = data.weather[0].main;
      const icon = data.weather[0].icon;

      weatherBox.innerHTML = `
        <img src="https://openweathermap.org/img/wn/${icon}.png" alt="${condition}" style="vertical-align: middle;">
        ${city}: <strong>${temp}°C</strong>, ${condition}
      `;
    })
    .catch(error => {
      document.getElementById("weather").innerText = "Weather unavailable";
      console.error("Weather error:", error);
    });
}

window.onload = function () {
  initMap();
  loadWeather();
};
