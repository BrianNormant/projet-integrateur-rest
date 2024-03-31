import { Button, Card, Form } from "react-bootstrap"
import "./style.css";
import { Link } from "react-router-dom";


  
export function LoginPage( ) {
  return (
    <div className="d-flex h-100 justify-content-center align-items-center">
      <Card className="w-75 align-self-center">
        <Card.Header>
          <Card.Title>{"Connexion a votre compte"}</Card.Title>
        </Card.Header>
        <Card.Body>
          <Form>
            <Form.Label className="mb-1">{"Nom d'utilisateur"}</Form.Label>
            <Form.Control className="w-100 mb-2" type="text" placeholder="Username"></Form.Control>
            <Form.Label className="mb-1">{"Mot de passe"}</Form.Label>
            <Form.Control className="w-100 mb-2" type="text" placeholder="Password"></Form.Control>
            <Form.Check type="checkbox" label="Se souvenir de moi" />
            <Link to="/main">
              <Button  variant="primary" className="mx-2 mt-2">{"Connexion"}</Button>
            </Link>
          </Form>
        </Card.Body>
        <Card.Footer className="text-muted"> 
          {"Vous ne possedez pas de compte?"} <span className="hoverable">{"Creez-en un!"}</span>
        </Card.Footer>
      </Card>
    </div>
  )
}