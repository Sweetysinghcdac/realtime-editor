import React, { useState } from 'react';
import api from '../api/api';

export default function InviteModal({ documentId, onClose }) {
  const [email, setEmail] = useState('');
  const [role, setRole] = useState('viewer');
  const [status, setStatus] = useState(null);

  const send = async () => {
    try {
      setStatus('sending');
      await api.post(`/api/documents/${documentId}/invite`, { email, role });
      setStatus('sent');
      setTimeout(() => onClose(), 900);
    } catch (e) {
      console.error(e);
      setStatus('error');
    }
  };

  return (
    <div className="card">
      <h3>Invite collaborator</h3>
      <div>
        <label className="small">Email</label>
        <input value={email} onChange={e=>setEmail(e.target.value)} placeholder="user@example.com" />
      </div>
      <div style={{marginTop:8}}>
        <label className="small">Role</label>
        <select value={role} onChange={e=>setRole(e.target.value)}>
          <option value="viewer">Viewer</option>
          <option value="editor">Editor</option>
        </select>
      </div>
      <div style={{marginTop:10, display:'flex', gap:8}}>
        <button onClick={send}>Send Invite</button>
        <button className="ghost" onClick={onClose}>Cancel</button>
      </div>
      {status === 'sent' && <div className="small" style={{marginTop:8}}>Invitation sent.</div>}
      {status === 'error' && <div className="small" style={{marginTop:8,color:'crimson'}}>Failed to send.</div>}
    </div>
  );
}
