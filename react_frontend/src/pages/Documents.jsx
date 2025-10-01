import React, { useContext, useEffect, useState } from 'react';
import api from '../api/api';
import { Link } from 'react-router-dom';
import { AuthContext } from '../App';
import Popup from '../components/Popup';

export default function Documents() {
  const { user } = useContext(AuthContext);
  const [docs, setDocs] = useState([]);
  const [title, setTitle] = useState('');
  const [creating, setCreating] = useState(false);
  const [search, setSearch] = useState('');
  const [popup, setPopup] = useState('');  // <-- popup message

  useEffect(() => {
    fetchDocs();
  }, []);

  const fetchDocs = async () => {
    try {
      const res = await api.get('/api/documents');
      const list = res.data.data ?? res.data;
      if (!list || list.length === 0) {
        setPopup("No documents found.");
      }
      setDocs(list);
    } catch (e) {
      console.error(e);
      if (e.response?.status === 401) {
        setPopup("Unauthorized: Please log in again.");
      } else if (e.response?.status === 403) {
        setPopup("Forbidden: You don’t have permission.");
      } else if (e.response?.status === 404) {
        setPopup("No documents found.");
      } else {
        setPopup("Something went wrong. Please try again.");
      }
    }
  };

  const createDoc = async () => {
    if (!title) return;
    setCreating(true);
    try {
      const res = await api.post('/api/documents', { title, content: '' });
      setTitle('');
      setDocs(d => [res.data, ...d]);
    } catch (e) {
      console.error(e);
      setPopup("Failed to create document.");
    } finally {
      setCreating(false);
    }
  };

  const deleteDoc = async (id) => {
    if (!confirm('Delete this document?')) return;
    try {
      await api.delete(`/api/documents/${id}`);
      setDocs(d => d.filter(x => x.id !== id));
    } catch (e) {
      console.error(e);
      setPopup("Failed to delete document.");
    }
  };

  const filteredDocs = docs.filter(doc =>
    doc.title?.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div>
      <div className="card">
        <h2>My Documents</h2>
        <div className="small">Signed in as <strong>{user?.name}</strong></div>

        <div style={{ marginTop: 12 }}>
          <input
            placeholder="New document title"
            value={title}
            onChange={e => setTitle(e.target.value)}
          />
          <div style={{ marginTop: 8 }}>
            <button onClick={createDoc} disabled={creating}>Create</button>
          </div>
        </div>

        {/* Search bar */}
        <div style={{ marginTop: 16 }}>
          <input
            placeholder="Search documents…"
            value={search}
            onChange={e => setSearch(e.target.value)}
            style={{ width: '100%' }}
          />
        </div>
      </div>

      {filteredDocs.map(doc => (
        <div className="card" key={doc.id}>
          <div style={{display:'flex', justifyContent:'space-between', alignItems:'center'}}>
            <div>
              <Link to={`/documents/${doc.id}`} style={{textDecoration:'none', color:'inherit'}}>
                <h3>{doc.title}</h3>
              </Link>
              <div className="small">Owner: {doc.owner?.name ?? '—'}</div>
            </div>
            <div style={{display:'flex', gap:8}}>
              <Link to={`/documents/${doc.id}`}><button>Open</button></Link>
              <button className="ghost" onClick={() => deleteDoc(doc.id)}>Delete</button>
            </div>
          </div>
        </div>
      ))}

      {filteredDocs.length === 0 && docs.length > 0 && (
        <div className="card small">No matching documents.</div>
      )}

      {/* Popup */}
      <Popup message={popup} onClose={() => setPopup('')} />
    </div>
  );
}
