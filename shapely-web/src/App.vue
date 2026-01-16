<template>
  <div class="app-wrapper">
    
    <header class="top-bar">
      <div class="header-content">
        <div class="logo">
          <span class="logo-icon">💠</span>
          <h1>Shapely - ALPHV Intern Assignment</h1>
        </div>
        <div class="user-info" v-if="isLoggedIn">
          <span class="badge">Admin Access</span>
          <button @click="handleLogout" class="btn-logout">Logout</button>
        </div>
      </div>
    </header>

    <div class="container">
      <div class="split-screen">
        
        <div class="card control-panel">
          
          <div v-if="!isLoggedIn" class="login-wrapper">
            <h2 class="section-title">Welcome Back</h2>
            <p class="subtitle">Please sign in to continue.</p>
            
            <form @submit.prevent="handleLogin" class="clean-form">
              <div class="input-group">
                <label>Email</label>
                <input v-model="loginForm.email" type="email" placeholder="admin@alphv.com" required />
              </div>
              <div class="input-group">
                <label>Password</label>
                <input v-model="loginForm.password" type="password" placeholder="Admin123@" required />
              </div>
              <button type="submit" class="btn-primary full-width">Login</button>
              <p v-if="loginError" class="error-msg">{{ loginError }}</p>
            </form>
          </div>

          <div v-else>
            <h2 class="section-title">Manage Items</h2>
            
            <div class="selector-wrapper">
               <select v-model="selectedItemId" @change="handleSelection" class="modern-select">
                   <option :value="null">➕ Create New Item</option>
                   <option disabled>--- Edit Existing ---</option>
                   <option v-for="item in items" :key="item.id" :value="item.id">
                     ✏️ Edit: {{ item.name }}
                   </option>
               </select>
            </div>

            <form @submit.prevent="submitItem" class="clean-form">
              <div class="input-group">
                <label>Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  placeholder="Item Name" 
                  :class="{ 'input-error': errors.name }"
                />
                <span v-if="errors.name" class="error-text">{{ errors.name[0] }}</span>
              </div>

              <div class="row">
                <div class="input-group half">
                  <label>Shape</label>
                  <select v-model="form.shape" :class="{ 'input-error': errors.shape }">
                    <option disabled value="">Select...</option>
                    <option>Circle</option>
                    <option>Square</option>
                    <option>Triangle</option>
                  </select>
                </div>

                <div class="input-group half">
                  <label>Color</label>
                  <div class="color-display">
                    <input v-model="form.color" type="color" class="color-picker" />
                    <span class="color-code">{{ form.color }}</span>
                  </div>
                </div>
              </div>
              <span v-if="errors.shape" class="error-text">{{ errors.shape[0] }}</span>

              <div class="button-row">
                <button v-if="!selectedItemId" type="submit" class="btn-primary full-width shadow-btn">
                  Add Item
                </button>

                <div v-else class="dual-btns">
                    <button type="submit" class="btn-warning">Update</button>
                    <button type="button" class="btn-danger" @click="deleteItem">Delete</button>
                </div>
              </div>
              
              <p v-if="generalError" class="error-msg">{{ generalError }}</p>
            </form>
          </div>
        </div>

        <div class="card collection-panel">
          <h2 class="section-title">User Portal</h2>
          
          <div v-if="items.length === 0" class="empty-state">
            <p>No items found.</p>
          </div>

          <div class="modern-table-container" v-else>
            <table class="modern-table">
              <thead>
                <tr>
                  <th>Date & Time</th>
                  <th>Name</th>
                  <th class="center">Shape & Color</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in items" :key="item.id" :class="{ 'active-row': selectedItemId === item.id }">
                  <td class="text-muted">{{ new Date(item.created_at).toLocaleString() }}</td>
                  <td class="font-bold">{{ item.name }}</td>
                  <td class="center">
                    <div class="icon-wrapper">
                      <svg v-if="item.shape === 'Circle'" width="40" height="40">
                        <circle cx="20" cy="20" r="16" :fill="item.color" />
                      </svg>
                      <svg v-if="item.shape === 'Square'" width="40" height="40">
                        <rect x="4" y="4" width="32" height="32" rx="6" :fill="item.color" />
                      </svg>
                      <svg v-if="item.shape === 'Triangle'" width="40" height="40">
                        <polygon points="20,4 4,36 36,36" :fill="item.color" />
                      </svg>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

// --- CONFIGURATION ---
//const API_BASE = 'http://localhost:8081/Shapely/shapely-api/public/api'; 
const API_BASE = import.meta.env.VITE_API_URL;

const items = ref([]);
const isLoggedIn = ref(false);
const loginError = ref('');
const userToken = ref(localStorage.getItem('auth_token') || null);

// Forms
const loginForm = reactive({ email: '', password: '' });
const form = reactive({ name: '', shape: '', color: '#5D5FEF' });
const selectedItemId = ref(null);
const errors = ref({});
const generalError = ref('');

const getAuthHeader = () => ({ headers: { Authorization: `Bearer ${userToken.value}` } });

// --- ACTIONS ---
const handleLogin = async () => {
  try {
    // We added the third argument (config) here 👇
    const response = await axios.post(`${API_BASE}/login`, loginForm, {
      headers: {
        'Accept': 'application/json' // <--- THIS PREVENTS THE 404 CRASH
      }
    });

    localStorage.setItem('auth_token', response.data.token);
    userToken.value = response.data.token;
    isLoggedIn.value = true;
    loginError.value = '';
    loginForm.email = ''; 
    loginForm.password = '';
  } catch (error) {
    // Now we can actually see the real error in the console
    console.error("Login Failed:", error.response); 
    loginError.value = "Incorrect credentials. Try admin@alphv.com / Admin123@";
  }
};


const handleLogout = async () => {
  try { await axios.post(`${API_BASE}/logout`, {}, getAuthHeader()); } catch(e) {}
  localStorage.removeItem('auth_token');
  userToken.value = null;
  isLoggedIn.value = false;
  selectedItemId.value = null;
};

const fetchItems = async () => {
  try {
    const response = await axios.get(`${API_BASE}/items`);
    items.value = response.data;
  } catch (error) { console.error(error); }
};

const submitItem = async () => {
  errors.value = {}; generalError.value = '';
  try {
    if (selectedItemId.value) {
      await axios.put(`${API_BASE}/items/${selectedItemId.value}`, form, getAuthHeader());
    } else {
      await axios.post(`${API_BASE}/items`, form, getAuthHeader());
    }
    selectedItemId.value = null;
    form.name = ''; form.shape = ''; form.color = '#5D5FEF';
    fetchItems();
  } catch (error) {
    if (error.response && error.response.status === 422) errors.value = error.response.data.errors;
    else if (error.response && error.response.status === 401) { generalError.value = "Session expired."; handleLogout(); }
    else generalError.value = "Something went wrong.";
  }
};

const deleteItem = async () => {
  if(!confirm("Are you sure?")) return;
  try {
    await axios.delete(`${API_BASE}/items/${selectedItemId.value}`, getAuthHeader());
    selectedItemId.value = null;
    form.name = ''; form.shape = ''; form.color = '#5D5FEF';
    fetchItems();
  } catch (error) { generalError.value = "Failed to delete."; }
};

const handleSelection = () => {
    errors.value = {}; generalError.value = '';
    if (selectedItemId.value === null) { form.name = ''; form.shape = ''; form.color = '#5D5FEF'; } 
    else {
        const item = items.value.find(i => i.id === selectedItemId.value);
        if (item) { form.name = item.name; form.shape = item.shape; form.color = item.color; }
    }
};

onMounted(() => {
  if (userToken.value) isLoggedIn.value = true;
  fetchItems();
});
</script>

<style scoped>
/* --- FONT & GLOBAL --- */
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap');

* { box-sizing: border-box; }

.app-wrapper {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background-color: #EFEFF6; /* Dark White / Light Gray */
  padding-top: 80px; /* Space for fixed header */
}

/* FIXED HEADER */
.top-bar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 70px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
}
.header-content {
  width: 100%;
  max-width: 1400px;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.logo { display: flex; align-items: center; gap: 10px; }
.logo-icon { font-size: 24px; }
h1 { margin: 0; font-size: 20px; font-weight: 700; color: #333; letter-spacing: -0.5px; }

.user-info { display: flex; align-items: center; gap: 15px; }
.badge { background: #E1F0FF; color: #007AFF; padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.btn-logout { background: none; border: none; font-weight: 600; color: #666; cursor: pointer; transition: 0.2s; }
.btn-logout:hover { color: #FF3B30; }

/* MAIN LAYOUT */
.container { max-width: 1400px; margin: 0 auto; padding: 40px 20px; }

/* FLEXBOX LAYOUT (DESKTOP DEFAULT) */
.split-screen { display: flex; gap: 30px; align-items: flex-start; }
.control-panel { flex: 0.8; }
.collection-panel { flex: 1.2; min-height: 500px; }

/* RESPONSIVE LAYOUT (MOBILE) */
@media (max-width: 900px) {
  .split-screen {
    flex-direction: column; /* Stack Top-to-Bottom */
  }
  .control-panel, .collection-panel {
    flex: none;
    width: 100%; /* Take full width */
    margin-bottom: 20px;
  }
  .collection-panel { min-height: auto; }
}

/* CARDS/Portals */
.card {
  background: white;
  border-radius: 24px;
  padding: 35px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.04);
  transition: transform 0.3s ease;
}

.section-title { margin-top: 0; font-size: 18px; color: #222; margin-bottom: 25px; font-weight: 700; }
.subtitle { color: #888; font-size: 14px; margin-top: -15px; margin-bottom: 25px; }

/* FORMS */
.input-group { margin-bottom: 20px; }
label { display: block; font-size: 12px; font-weight: 700; color: #8F92A1; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }

input[type="text"], input[type="email"], input[type="password"], select {
  width: 100%;
  padding: 14px 16px;
  border-radius: 12px;
  border: 2px solid #F3F3F3;
  background: #FCFCFC;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  color: #333;
  transition: all 0.2s;
}
input:focus, select:focus { outline: none; border-color: #5D5FEF; background: white; }
.input-error { border-color: #FF3B30 !important; background: #FFF5F5 !important; }
.error-text, .error-msg { color: #FF3B30; font-size: 12px; margin-top: 5px; font-weight: 500; }

.row { display: flex; gap: 15px; }
.half { flex: 1; }

/* COLOR PICKER STYLE */
.color-display { 
  display: flex; align-items: center; gap: 10px; 
  border: 2px solid #F3F3F3; padding: 8px; border-radius: 12px; background: #FCFCFC;
}
.color-picker { width: 35px; height: 35px; border: none; padding: 0; background: none; cursor: pointer; border-radius: 8px; }
.color-code { font-size: 13px; font-family: monospace; color: #666; }

/* BUTTONS */
.btn-primary {
  background: #5D5FEF;
  color: white;
  border: none;
  padding: 16px;
  border-radius: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.2s;
}
.btn-primary:hover { background: #4B4DCE; transform: translateY(-2px); }
.shadow-btn { box-shadow: 0 10px 20px rgba(93, 95, 239, 0.25); }
.full-width { width: 100%; }

.dual-btns { display: flex; gap: 10px; width: 100%; }
.btn-warning { flex: 1; background: #FFCC00; border: none; padding: 14px; border-radius: 12px; font-weight: 700; cursor: pointer; color: #444; }
.btn-danger { flex: 1; background: #FF3B30; border: none; padding: 14px; border-radius: 12px; font-weight: 700; cursor: pointer; color: white; }

/* TABLE */
.modern-table { width: 100%; border-collapse: collapse; }
th { text-align: left; font-size: 12px; color: #8F92A1; text-transform: uppercase; padding-bottom: 15px; }
td { padding: 15px 0; border-bottom: 1px solid #F3F3F3; vertical-align: middle; }
tr:last-child td { border-bottom: none; }
.text-muted { color: #888; font-size: 13px; }
.font-bold { font-weight: 700; color: #333; }
.center { text-align: center; }
.active-row td { background: #F9FAFF; }

/* SELECTOR */
.selector-wrapper { margin-bottom: 20px; background: #F3F3F3; padding: 5px; border-radius: 12px; }
.modern-select { border: none; background: transparent; font-weight: 600; }
</style>