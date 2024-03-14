'use client'

import { useState } from "react";
import { LoginPage } from "./pages/LoginPage";

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
      <MainContent />
    </>
  )
}

function Navigation() {
  return (
    <nav>
      <p>{"This will be the navigation page"}</p>
    </nav>
  )
}

function MainContent() {
  return (
    <main>
      <p>{"This will be the main content display area"}</p>
    </main>
  )
}
