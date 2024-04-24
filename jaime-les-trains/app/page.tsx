'use client'

import { StrictMode, useEffect, useState } from "react";
import { LoginPage } from "./pages/LoginPage";
import { Nav, Navbar } from "react-bootstrap";
import { MyTrainsPage } from "./pages/MyTrainsPage";
import { createRoot } from "react-dom/client";
import { BrowserRouter, createBrowserRouter, Link, Route, RouterProvider, Routes } from "react-router-dom"
import { SignupPage } from "./pages/SignupPage";
import { MyReservationsPage } from "./pages/MyReservationsPage";
export default function Home() {

  const [token, setToken] = useState("");
  const [user, setUser] = useState("");

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<LoginPage fcttoken={setToken} fctuser={setUser}/>} />
        <Route path="/signup" element={<SignupPage />} />
        <Route path="/main" element={<Trains token={token}/>} />
        <Route path="/dashboard" element={<Reservations token={token} username={user}/>} />
      </Routes>
    </BrowserRouter> 
  )
}

export interface authProps {
  token: string
}

export interface MyReservationsPageProps extends authProps {
  username: string
}

function Trains( {...props}: authProps ) {
  return (
    <>
      <Navigation />
      <MyTrainsPage token={props.token}/>
    </>
  )
}

function Reservations( {...props}: MyReservationsPageProps ) {
  return (
    <>
      <Navigation />
      <MyReservationsPage token={props.token} username={props.username}/>
    </>
  )
}

function Navigation() {
  return (
    <Navbar className="px-2" bg="dark" data-bs-theme="dark">
      <Navbar.Brand>{"J'aime les trains"}</Navbar.Brand>
      <Nav className="w-100 d-flex justify-content-between">
        <div className="d-flex">
          <Nav.Link><Link to="/dashboard">{"Mes Reservations"}</Link></Nav.Link>
          <Nav.Link><Link to="/main">{"Mes Trains"}</Link></Nav.Link>
        </div>
        <Nav.Link><Link to="/">{"Deconnexion"}</Link></Nav.Link>
      </Nav>
    </Navbar>
  )
}