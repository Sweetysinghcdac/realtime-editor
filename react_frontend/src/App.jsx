import React, { createContext, useEffect, useState } from 'react';
import { Outlet, useNavigate } from 'react-router-dom';
import Nav from './components/Nav';
import api from './api/api';
import { initEcho, disconnectEcho } from './echo';


export const AuthContext = createContext();

export default function App() {
  const [user, setUser] = useState(() => {
    try { return JSON.parse(localStorage.getItem('user')); } catch { return null; }
  });
  const [token, setToken] = useState(() => localStorage.getItem('token') || null);
  const navigate = useNavigate();

  useEffect(() => {
    if (token) {
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      // initialize echo so presence auth uses Authorization header
      initEcho(token);
    } else {
      delete api.defaults.headers.common['Authorization'];
      disconnectEcho();
    }
  }, [token]);

  const login = ({ user, token }) => {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    setToken(token);
    setUser(user);
    navigate('/');
  };

  const logout = async () => {
    try { await api.post('/api/logout'); } catch (e) { /* ignore */ }
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setToken(null);
    setUser(null);
    navigate('/login');
  };

  const value = { user, token, login, logout, setUser };

  return (
    <AuthContext.Provider value={value}>
      <Nav />
      <main className="container">
        <Outlet />
      </main>
    </AuthContext.Provider>
  );
}
