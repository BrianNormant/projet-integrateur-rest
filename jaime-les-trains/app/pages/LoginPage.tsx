import { Button, Card, Form } from "react-bootstrap"
import "./style.css";
import { Link, Navigate } from "react-router-dom";
import { Dispatch, SetStateAction, useEffect, useState } from "react";

interface LoginPageProps {
  fcttoken: Dispatch<SetStateAction<string>>
  fctuser: Dispatch<SetStateAction<string>>
}
  
export function LoginPage( {...props}: LoginPageProps ) {

  //States
  const [validated, setValidated] = useState(false) 
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [token, setToken] = useState("");

  function onSubmitPressed() {
    setValidated(true)
    if (username && password) getToken(username, password, setToken)
  }

  if (token.length > 4) {
    props.fcttoken(token)
    props.fctuser(username)
    return (<Navigate to="/main" />)
  }


  return (
    <div className="d-flex h-100 justify-content-center align-items-center">
      <Card className="w-75 align-self-center">
        <Card.Header>
          <Card.Title>{"Connexion a votre compte"}</Card.Title>
        </Card.Header>
        <Card.Body>
          <Form noValidate validated={validated}>
            <Form.Label className="mb-1">{"Nom d'utilisateur"}</Form.Label>
            <Form.Control required className="w-100 mb-2" type="text" placeholder="Username" onChange={e => setUsername(e.target.value)}></Form.Control>
            <Form.Control.Feedback type="invalid">{"Vous devez entrer un nom d'utilisateur"}</Form.Control.Feedback>
            <Form.Label className="mb-1">{"Mot de passe"}</Form.Label>
            <Form.Control required className="w-100 mb-2" type="text" placeholder="Password" onChange={e => setPassword(e.target.value)}></Form.Control>
            <Form.Control.Feedback type="invalid">{"Vous devez entrer un mot de passe"}</Form.Control.Feedback>
            {/*<Form.Check type="checkbox" label="Se souvenir de moi" />*/}
            {token == "n/a" ? <p>{"Votre nom d'utilisateur ou mot de passe est incorrect"}</p> : ""}
            <Button variant="primary" className="mx-2 mt-2" onClick={e => onSubmitPressed()}>{"Connexion"}</Button>
          </Form>
        </Card.Body>
        {/*<Card.Footer className="text-muted"> 
          {"Vous ne possedez pas de compte?"} <Link to="/signup" className="hoverable">{"Creez-en un!"}</Link>
  </Card.Footer>*/}
      </Card>
    </div>
  )
}

function getToken(username: string, password: string, tokenfct: Dispatch<SetStateAction<string>> /*bruh*/) {

  const PATH = 'https://equipe500.tch099.ovh/projet6/api/login/'+username

  const requestOptions = {
    method: "PUT",
    headers: { 'Authorization': password},
  };
  fetch(PATH, requestOptions)
    .then(response => {
      if (!response.ok) return null;
      else return response.json()
    })
    .then(data => {
      if (data) {
        tokenfct(data.token);
      } else {
        tokenfct("n/a")
      }
    });
}