'use client'

import { StrictMode, useEffect, useState } from "react";
import { LoginPage } from "./pages/LoginPage";
import { Nav, Navbar } from "react-bootstrap";
import { MyTrainsPage } from "./pages/MyTrainsPage";
import { createRoot } from "react-dom/client";
import { BrowserRouter, createBrowserRouter, Link, Route, RouterProvider, Routes } from "react-router-dom"
import { SignupPage } from "./pages/SignupPage";
export default function Home() {
  
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {})

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<LoginPage />} />
        <Route path="/signup" element={<SignupPage />} />
        <Route path="/main" element={<MainPage />} />
        <Route path="/dashboard" element={<Navigation />} />
      </Routes>
    </BrowserRouter>
  )
}

function MainPage() {
  return (
    <>
      <Navigation />
      <MyTrainsPage />
    </>
  )
}

function Navigation() {
  return (
    <Navbar className="px-2" bg="dark" data-bs-theme="dark">
      <Navbar.Brand>{"J'aime les trains"}</Navbar.Brand>
      <Nav className="w-100 d-flex justify-content-between">
        <div className="d-flex">
          <Nav.Link><Link to="/dashboard">{"Tableau de bord"}</Link></Nav.Link>
          <Nav.Link><Link to="/main">{"Mes Trains"}</Link></Nav.Link>
        </div>
        <Nav.Link><Link to="/">{"Deconnexion"}</Link></Nav.Link>
      </Nav>
    </Navbar>
  )
}