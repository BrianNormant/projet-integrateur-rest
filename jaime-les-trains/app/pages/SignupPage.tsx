import { Button, Card, Form } from "react-bootstrap"
import "./style.css";
import { Link } from "react-router-dom";


  
export function SignupPage( ) {
  return (
    <div className="d-flex h-100 justify-content-center align-items-center">
      <Card className="w-75 align-self-center">
        <Card.Header>
          <Card.Title>{"Creer un compte"}</Card.Title>
        </Card.Header>
        <Card.Body>
          <Form>
            <Form.Label className="mb-1">{"Nom d'utilisateur"}</Form.Label>
            <Form.Control className="w-100 mb-2" type="text" placeholder="Username"></Form.Control>
            <Form.Label className="mb-1">{"Mot de passe"}</Form.Label>
            <Form.Control className="w-100 mb-2" type="text" placeholder="Password"></Form.Control>
            <Form.Label className="mb-1">{"Confirmer votre mot de passe"}</Form.Label>
            <Form.Control className="w-100 mb-2" type="text" placeholder="Password"></Form.Control>
            <Link to="/main">
              <Button  variant="primary" className="mx-2 mt-2">{"Inscription"}</Button>
            </Link>
          </Form>
        </Card.Body>
        <Card.Footer className="text-muted"> 
          {"Vous avez deja un compte?"} <Link to="/" className="hoverable">{"Connectez vous!"}</Link>
        </Card.Footer>
      </Card>
    </div>
  )
}