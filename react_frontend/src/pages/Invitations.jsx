// src/pages/Invitations.jsx
import React, { useEffect, useState, useContext } from "react";
import api from "../api/api";
import { AuthContext } from "../App";

export default function Invitations() {
  const { token } = useContext(AuthContext);
  const [invitations, setInvitations] = useState([]);

  useEffect(() => {
    if (token) fetchInvitations();
  }, [token]);

  const fetchInvitations = async () => {
    try {
      const res = await api.get("/api/invitations");
      setInvitations(res.data);
    } catch (err) {
      console.error(err);
    }
  };

  const acceptInvitation = async (id) => {
    try {
      await api.post(`/api/invitations/${id}/accept`);
      setInvitations((prev) => prev.filter((inv) => inv.id !== id));
    } catch (err) {
      console.error(err);
      alert("Failed to accept invitation.");
    }
  };

  const declineInvitation = async (id) => {
    try {
      await api.post(`/api/invitations/${id}/decline`);
      setInvitations((prev) => prev.filter((inv) => inv.id !== id));
    } catch (err) {
      console.error(err);
      alert("Failed to decline invitation.");
    }
  };

  return (
    <div>
      <div className="card">
        <h2>Pending Invitations</h2>
        <div className="small">
          You have {invitations.length} pending{" "}
          {invitations.length === 1 ? "invite" : "invites"}.
        </div>
      </div>

      {invitations.length === 0 && (
        <div className="card small">🎉 No pending invitations right now.</div>
      )}

      {invitations.map((inv) => (
        <div key={inv.id} className="card">
          <div style={{ display: "flex", justifyContent: "space-between" }}>
            <div>
              <h3>{inv.document?.title ?? "Untitled Document"}</h3>
              <div className="small">
                Invited by <strong>{inv.inviter?.name ?? "Unknown"}</strong>
              </div>
            </div>
            <div style={{ display: "flex", gap: 8 }}>
              <button onClick={() => acceptInvitation(inv.id)}>Accept</button>
              <button className="ghost" onClick={() => declineInvitation(inv.id)}>
                Decline
              </button>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
