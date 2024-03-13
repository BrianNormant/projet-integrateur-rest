'use client'

import { useState } from "react";

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

interface LoginPageProps {
  onClick: (x: boolean) => void
}

function LoginPage( {...props}: LoginPageProps ) {
  return (
    <>
      <button onClick={() => props.onClick(true)}>{"Login"}</button>
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
