// src/components/Popup.js
import React from "react";

export default function Popup({ message, onClose }) {
  if (!message) return null;

  return (
    <div style={{
      position: "fixed",
      top: 0, left: 0, right: 0, bottom: 0,
      backgroundColor: "rgba(0,0,0,0.5)",
      display: "flex",
      justifyContent: "center",
      alignItems: "center",
      zIndex: 9999
    }}>
      <div style={{
        background: "#fff",
        padding: "20px",
        borderRadius: "8px",
        minWidth: "300px",
        textAlign: "center"
      }}>
        <p>{message}</p>
        <button onClick={onClose} style={{marginTop: "12px"}}>Close</button>
      </div>
    </div>
  );
}
