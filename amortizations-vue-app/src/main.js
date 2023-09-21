import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { initializeApp } from "firebase/app";
import { getFirestore } from 'firebase/firestore';

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCzErrs7BzS5ccKJZCedhfxYYrVIKS28Nk",
  authDomain: "gop-challenge.firebaseapp.com",
  projectId: "gop-challenge",
  storageBucket: "gop-challenge.appspot.com",
  messagingSenderId: "271635350746",
  appId: "1:271635350746:web:d48b0541e588c277d191e3",
  measurementId: "G-KWL9KD2SB9"
};

// Initialize Firebase
const appInstance = initializeApp(firebaseConfig);
const db = getFirestore(appInstance);

const app = createApp(App);
app.config.globalProperties.$db = db; // Providing db instance globally
app.mount('#app');
