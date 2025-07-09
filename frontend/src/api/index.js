import api from '../axios';

export const getScpis = () => api.get('/scpis');
export const register = (email, password) => api.post('/register', { email, password });
export const login = (email, password) => api.post('/login', { email, password });
export const logout = () => api.post('/logout');
export const me = () => api.get('/me');
export const simulatePortfolio = (portefeuille) => api.post('/portfolio', { portefeuille });
