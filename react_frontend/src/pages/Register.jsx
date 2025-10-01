import React, { useContext, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../App';
import { register as registerApi } from '../api/auth';

export default function Register() {
  const { login } = useContext(AuthContext);
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [err, setErr] = useState(null);
  const navigate = useNavigate();

  const submit = async e => {
    e.preventDefault();
    setErr(null);
    try {
      const res = await registerApi({ name, email, password, password_confirmation: passwordConfirmation });
      // res contains { user, token }
      login(res);
      navigate('/');
    } catch (error) {
      console.error(error);
      setErr('Registration failed.');
    }
  };

  return (
    <div className="container">
      <div className="card" style={{maxWidth:520, margin:'0 auto'}}>
        <h2>Register</h2>
        <form onSubmit={submit}>
          <div>
            <label className="small">Full name</label>
            <input value={name} onChange={e=>setName(e.target.value)} />
          </div>
          <div style={{marginTop:8}}>
            <label className="small">Email</label>
            <input value={email} onChange={e=>setEmail(e.target.value)} />
          </div>
          <div style={{marginTop:8}}>
            <label className="small">Password</label>
            <input type="password" value={password} onChange={e=>setPassword(e.target.value)} />
          </div>
          <div style={{marginTop:8}}>
            <label className="small">Confirm password</label>
            <input type="password" value={passwordConfirmation} onChange={e=>setPasswordConfirmation(e.target.value)} />
          </div>
          <div style={{marginTop:12}}>
            <button type="submit">Create account</button>
          </div>
          {err && <div className="small" style={{color:'crimson', marginTop:8}}>{err}</div>}
        </form>
      </div>
    </div>
  );
}
