import { useState } from "react";
import { Button, Card } from "react-bootstrap";

export function MyReservationsPage( ) {

    const [isOpen, setIsOpen] = useState(false)

    return (
        <div className="m-3">
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Mes Reservations"}
                </Card.Title>
                <Card.Body>
                    
                </Card.Body>
            </Card>
            {isOpen ? <AddReservation /> : 
                <Button onClick={() => setIsOpen(true)}>
                    {"Ajouter une reservation"}
                </Button>
            }
        </div>
    )

    function AddReservation( {...props} ) {
        return (
                <Card className="p-2 mb-2">
                    <Card.Title>
                        {"Ajouter une reservation"}
                    </Card.Title>
                    <Card.Body>
                        {"sus"}
                    </Card.Body>
                </Card>
        )
    }
}