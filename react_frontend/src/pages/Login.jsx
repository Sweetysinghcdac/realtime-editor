import React, { useContext, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../App';
import { login as loginApi } from '../api/auth';

export default function Login() {
  const { login } = useContext(AuthContext);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [err, setErr] = useState(null);
  const navigate = useNavigate();

  const submit = async e => {
    e.preventDefault();
    setErr(null);
    try {
      const res = await loginApi({ email, password });
      // res contains { user, token }
      login(res);
      navigate('/');
    } catch (error) {
      console.error(error);
      setErr('Invalid credentials or server error.');
    }
  };

  return (
    <div className="container">
      <div className="card" style={{maxWidth:480, margin:'0 auto'}}>
        <h2>Login</h2>
        <form onSubmit={submit}>
          <div>
            <label className="small">Email</label>
            <input value={email} onChange={e=>setEmail(e.target.value)} />
          </div>
          <div style={{marginTop:8}}>
            <label className="small">Password</label>
            <input type="password" value={password} onChange={e=>setPassword(e.target.value)} />
          </div>
          <div style={{marginTop:12}}>
            <button type="submit">Login</button>
          </div>
          {err && <div className="small" style={{color:'crimson', marginTop:8}}>{err}</div>}
        </form>
      </div>
    </div>
  );
}
