import { Button, Card, Form } from "react-bootstrap"

interface LoginPageProps {
    onClick: (x: boolean) => void
  }
  
  export function LoginPage( {...props}: LoginPageProps ) {
    return (
      <>
      <Card>
        <Card.Title>{"Connectez vous a votre compte"}</Card.Title>
          <Card.Body>
            <Form>
              <Form.Label>{"Nom d'utilisateur"}</Form.Label>
              <Form.Control className="mx-2 w-auto" type="text" placeholder="Username"></Form.Control>
              <Form.Label>{"Mot de passe"}</Form.Label>
              <Form.Control className="mx-2 w-auto" type="text" placeholder="Password"></Form.Control>
              <Form.Check type="checkbox" label="Se souvenir de moi" />
              <Button  className="danger mx-2 mt-2" onClick={() => props.onClick(true)}>{"Login"}</Button>
            </Form>
        </Card.Body>
      </Card>
      </>
    )
  }