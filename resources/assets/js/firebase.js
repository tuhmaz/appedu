
'use strict';
// Import the functions you need from the SDKs you need
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-analytics.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js";

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyD0CruyYyOTbW8Fgmv8C3OJgw_N7lq7aE0",
    authDomain: "alemedu-001.firebaseapp.com",
    projectId: "alemedu-001",
    storageBucket: "alemedu-001.appspot.com",
    messagingSenderId: "1030869846849",
    appId: "1:1030869846849:web:55932499cd071b84606fc0",
    measurementId: "G-SMQFQ1ZW15"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

// Initialize Firebase Cloud Messaging
const messaging = getMessaging(app);

// Request permission to send notifications
async function requestPermission() {
    try {
        const token = await getToken(messaging, { vapidKey: "RRfVfGePuY9cLP1CkO-k9K7cQO3Dr2tsgNa8eU9DcTA" });
        console.log("Firebase Token:", token);

        fetch('/api/save-firebase-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + userAccessToken,
            },
            body: JSON.stringify({ firebase_token: token })
        })
        .then(response => response.json())
        .then(data => console.log("Token saved successfully:", data))
        .catch(error => console.error("Error saving token:", error));
    } catch (error) {
        console.error("Error getting token:", error);
    }
}

// Call the request permission function to get the token
requestPermission();

// Listen for messages when the app is in the foreground
onMessage(messaging, (payload) => {
    console.log("Message received. ", payload);
});
