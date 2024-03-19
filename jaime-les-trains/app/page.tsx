'use client'

import { useState } from "react";
import { LoginPage } from "./pages/LoginPage";
import { Nav, Navbar } from "react-bootstrap";
import { MyTrainsPage } from "./pages/MyTrainsPage";

export default function Home() {
  
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  return (
    <>
      {isLoggedIn ? (
        <MainPage />
      ) : (
        <LoginPage onClick={setIsLoggedIn}/>
      )}
  </>
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
          <Nav.Link href="#main">{"Tableau de bord"}</Nav.Link>
          <Nav.Link href="#trains">{"Mes Trains"}</Nav.Link>
        </div>
        <Nav.Link href="#profile">{"Mon profil"}</Nav.Link>
      </Nav>
    </Navbar>
  )
}

function MainContent() {
  return (
    <main>
      <p>{"This will be the main content display area"}</p>
    </main>
  )
}
