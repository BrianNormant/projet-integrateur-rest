import { Button, Card, Form } from "react-bootstrap"
import "./style.css";
import { Link } from "react-router-dom";
import { useState } from "react";


  
export function LoginPage( ) {

  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");

  return (
    <div className="d-flex h-100 justify-content-center align-items-center">
      <Card className="w-75 align-self-center">
        <Card.Header>
          <Card.Title>{"Connexion a votre compte"}</Card.Title>
        </Card.Header>
        <Card.Body>
          <Form>
            <Form.Label className="mb-1">{"Nom d'utilisateur"}</Form.Label>
            <Form.Control className="w-100 mb-2" type="text" placeholder="Username" onChange={e => setUsername(e.target.value)}></Form.Control>
            <Form.Label className="mb-1">{"Mot de passe"}</Form.Label>
            <Form.Control className="w-100 mb-2" type="text" placeholder="Password" onChange={e => setPassword(e.target.value)}></Form.Control>
            <Form.Check type="checkbox" label="Se souvenir de moi" />
            <Button variant="primary" className="mx-2 mt-2" onClick={e => verifyPassword(username, password)}>{"Connexion"}</Button>
          </Form>
        </Card.Body>
        <Card.Footer className="text-muted"> 
          {"Vous ne possedez pas de compte?"} <Link to="/signup" className="hoverable">{"Creez-en un!"}</Link>
        </Card.Footer>
      </Card>
    </div>
  )
}

function verifyPassword(username: string, password: string) {
  const requestOptions = {
    method: 'PUT',
    headers: { 'Authorization': password, "Access-Control-Allow-Origin": '*' }
  };
  fetch('https://equipe500.tch099.ovh/projet6/api/login/'+username, requestOptions)
    .then(response => response.json())
    .then(data => console.log(data));
}