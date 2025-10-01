import api from './api';

export async function register(payload) {
  // payload: {name,email,password,password_confirmation}
  const res = await api.post('/api/register', payload);
  return res.data;
}

export async function login(payload) {
  // payload: {email,password}
  const res = await api.post('/api/login', payload);
  return res.data;
}
