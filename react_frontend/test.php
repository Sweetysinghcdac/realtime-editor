import React, { useContext, useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { AuthContext } from "../App";
import { initEcho, getEcho } from "../echo";

export default function Nav() {
  const { user, logout, token } = useContext(AuthContext);
  const nav = useNavigate();
  const [inviteCount, setInviteCount] = useState(0);

  useEffect(() => {
    if (!user || !token) return;

    const echo = initEcho(token);
    const channel = echo.private(`invitations.${user.id}`);

    channel.listen(".InvitationCountUpdated", (e) => {
      console.log("Updated invitation count:", e.count);
      setInviteCount(e.count);
    });

    return () => {
      getEcho()?.leave(`invitations.${user.id}`);
    };
  }, [user, token]);

  return (
    <header className="nav">
      <div style={{ display: "flex", gap: 12, alignItems: "center" }}>
        <Link to="/" style={{ textDecoration: "none", color: "inherit" }}>
          <strong>RealtimeDocs</strong>
        </Link>
        <span className="small">Collaborative editor</span>
      </div>

      <div style={{ display: "flex", gap: 12, alignItems: "center" }}>
        {user ? (
          <>
            <span className="small">
              Signed in as <strong>{user.name}</strong>
            </span>
            <button className="ghost" onClick={() => nav("/")}>
              My Docs
            </button>
              <Link to="/invitations" style={{ textDecoration: "none" }}>
              <button className="ghost" style={{ position: "relative" }}>
                Invitations
                {inviteCount > 0 && (
                  <span
                    style={{
                      position: "absolute",
                      top: -8,
                      right: -18,
                      background: "red",
                      color: "white",
                      borderRadius: "50%",
                      padding: "2px 8px",
                      fontSize: "0.8rem",
                      marginLeft: 4,
                    }}
                  >
                    {inviteCount}
                  </span>
                )}
              </button>
            </Link>
            <button onClick={logout}>Logout</button>
          </>
        ) : (
          <>
            <Link to="/login">
              <button className="ghost">Login</button>
            </Link>
            <Link to="/register">
              <button>Register</button>
            </Link>
          </>
        )}
      </div>
    </header>
  );
}
