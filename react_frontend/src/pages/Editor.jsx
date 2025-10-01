// src/pages/Editor.jsx
import React, { useContext, useEffect, useRef, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../api/api";
import { AuthContext } from "../App";
import useDebounce from "../hooks/useDebounce";
import InviteModal from "../components/InviteModal";
import { getEcho } from "../echo";

export default function Editor() {
  const { id } = useParams();
  const { user, token } = useContext(AuthContext);
  const navigate = useNavigate();

  const [doc, setDoc] = useState(null);
  const [present, setPresent] = useState([]);
  const [canEdit, setCanEdit] = useState(true);
  const [showInvite, setShowInvite] = useState(false);

  const [versions, setVersions] = useState([]);
  const [showVersions, setShowVersions] = useState(false);

  const [showCollaborators, setShowCollaborators] = useState(false); // sidebar toggle

  const textareaRef = useRef();

  // --- Debounced save ---
  const saveDebounced = useDebounce(async (content) => {
    try {
      const res = await api.patch(`/api/documents/${id}`, { content });
      setDoc(res.data);
      fetchVersions(); // refresh versions after save
    } catch (e) {
      if (e.response?.status === 403) {
        setCanEdit(false);
        console.error("You do not have permission to edit this document.");
      } else {
        console.error(e);
      }
    }
  }, 800);

  // --- Load doc + join channel ---
  useEffect(() => {
    let channel;

    async function init() {
      if (!token) return;

      try {
        const res = await api.get(`/api/documents/${id}`);
        setDoc(res.data);
        if (textareaRef.current) textareaRef.current.value = res.data.content ?? "";

        // disable editing if viewer
        const me = (res.data.collaborators || []).find((c) => c.id === user?.id);
        if (me && me.pivot?.role === "viewer") setCanEdit(false);

        fetchVersions();
      } catch (err) {
        console.error("Failed to load document", err);
        if (err.response?.status === 403) {
          navigate("/");
          return;
        }
      }

      const echo = getEcho();
      if (!echo) return;

      channel = echo
        .join(`document.${id}`)
        .here((users) => setPresent(uniqueById(users)))
        .joining((u) => setPresent((prev) => uniqueById([...prev, u])))
        .leaving((u) => setPresent((prev) => prev.filter((m) => m.id !== u.id)))
        .listen(".DocumentUpdated", (e) => {
          if (e.updated_by?.id === user?.id) return; // skip own edits
          setDoc((prev) => ({ ...prev, content: e.content }));
          if (textareaRef.current && document.activeElement !== textareaRef.current) {
            textareaRef.current.value = e.content ?? "";
          }
        });
    }

    init();

    return () => {
      const echo = getEcho();
      if (echo) echo.leave(`document.${id}`);
    };
  }, [id, token, navigate, user?.id]);

  // --- Fetch versions ---
  const fetchVersions = async () => {
    try {
      const res = await api.get(`/api/documents/${id}/versions`);
      setVersions(res.data || []);
    } catch (err) {
      console.error("Failed to load versions", err);
    }
  };

  // --- Revert to version ---
  const revertToVersion = async (versionId) => {
    try {
      const res = await api.post(`/api/documents/${id}/revert/${versionId}`);
      setDoc(res.data);
      if (textareaRef.current) textareaRef.current.value = res.data.content ?? "";
      fetchVersions();
    } catch (err) {
      console.error("Failed to revert version", err);
    }
  };

  // --- Handle typing ---
  const onChange = (e) => {
    const content = e.target.value;
    if (!canEdit) return;
    setDoc((prev) => ({ ...prev, content }));
    saveDebounced(content);
  };

  // --- Revoke collaborator ---
  const revokeAccess = async (collabId) => {
    if (!window.confirm("Revoke this collaborator's access?")) return;

    try {
      await api.post(`/api/documents/${id}/revoke/${collabId}`);
      setDoc((prev) => ({
        ...prev,
        collaborators: prev.collaborators.filter((c) => c.id !== collabId),
      }));
    } catch (err) {
      console.error("Failed to revoke collaborator:", err);
    }
  };

  if (!doc) return <div className="card">Loading document…</div>;

  return (
    <div style={{ display: "flex", gap: 16 }}>
      {/* Main editor area */}
      <div style={{ flex: 1 }}>
        <div className="card">
          <div style={{ display: "flex", justifyContent: "space-between" }}>
            <div>
              <h2>{doc.title}</h2>
              <div className="small">Owner: {doc.owner?.name ?? "—"}</div>
            </div>

            <div>
              <div className="small">Present ({present.length}):</div>
              <div className="present-list">
                {present.map((u) => (
                  <div key={`present-${u.id}`} className="avatar">
                    {u.name}
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>

        <div className="card">
          {!canEdit && <div className="warning">View-only access. Editing disabled.</div>}
          <textarea
            ref={textareaRef}
            value={doc.content ?? ""}
            onChange={onChange}
            readOnly={!canEdit}
            style={{ minHeight: 360, width: "100%" }}
          />
        </div>

        <div style={{ display: "flex", gap: 8 }}>
          <button onClick={() => setShowInvite(true)} disabled={doc.owner?.id !== user?.id}>
            Invite
          </button>
          <button onClick={() => setShowVersions((v) => !v)}>
            {showVersions ? "Hide Versions" : "Show Versions"}
          </button>
          <button onClick={() => setShowCollaborators((v) => !v)}>
            {showCollaborators ? "Hide Collaborators" : "Show Collaborators"}
          </button>
        </div>

        {showInvite && <InviteModal documentId={id} onClose={() => setShowInvite(false)} />}
      </div>

      {/* Versions sidebar */}
      {showVersions && (
        <div className="card" style={{ width: 240, maxHeight: 500, overflowY: "auto" }}>
          <h4>Versions</h4>
          {versions.length === 0 && <div className="small">No versions yet.</div>}
          <ul style={{ listStyle: "none", padding: 0 }}>
            {versions.map((v) => (
              <li key={v.id} style={{ marginBottom: 8 }}>
                <div className="small">{new Date(v.created_at).toLocaleString()}</div>
                <button onClick={() => revertToVersion(v.id)} style={{ marginTop: 4 }}>
                  Revert
                </button>
              </li>
            ))}
          </ul>
        </div>
      )}

      {/* Collaborators sidebar */}
      {showCollaborators && (
        <div className="card" style={{ width: 240, maxHeight: 500, overflowY: "auto" }}>
          <h4>Collaborators</h4>
          {(doc.collaborators || []).length === 0 && <div className="small">No collaborators yet.</div>}
          <ul style={{ listStyle: "none", padding: 0 }}>
            {(doc.collaborators || []).map((c) => (
              <li
                key={c.id}
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                  marginBottom: 8,
                }}
              >
                <span>
                  {c.name} <span className="small">({c.pivot?.role ?? "unknown"})</span>
                </span>
                {doc.owner?.id === user?.id && (
                  <button
                    className="ghost"
                    onClick={() => revokeAccess(c.id)}
                    disabled={c.id === user?.id}
                  >
                    Revoke
                  </button>
                )}
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
}

// --- Helpers ---
function uniqueById(arr) {
  return Array.from(new Map(arr.map((u) => [u.id, u])).values());
}
