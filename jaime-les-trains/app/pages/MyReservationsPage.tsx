import { Dispatch, SetStateAction, useEffect, useState } from "react";
import { Button, Card } from "react-bootstrap";
import { authProps } from "../page";

export function MyReservationsPage( {...props}: authProps) {

    const [res, setRes] = useState<Reservation[]>([])
    const [isOpen, setIsOpen] = useState(false)

    useEffect(() => loadReservations(props.token, setRes), []);
    console.log(res)

    return (
        <div className="m-3">
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Mes Reservations"}
                </Card.Title>
                <Card.Body>
                    {res.length == 0 ? 
                        <p>{"Vous n'avez aucune reservation"}</p> :
                    res.map((x, i) => <ReservationComponent key={i} res={x}/>)}
                </Card.Body>
            </Card>
            <Button onClick={() => {}}>
                    {"Rafraichir"}
                </Button>
            {isOpen ? <AddReservation /> : 
                <Button onClick={() => setIsOpen(true)}>
                    {"Ajouter une reservation"}
                </Button>
            }
        </div>
    )

function ReservationComponent({...props}: {res: Reservation}) {
    return (
        <Card className="p-1 mb-2">
            <Card.Title className="">
                <p>{"Reservation #" + props.res.id}</p>
                <p>{props.res.dateReserv}</p>
            </Card.Title>
            <Card.Body>

            </Card.Body>
        </Card>
    )
}

function loadReservations(token: string, callbk: Dispatch<SetStateAction<Reservation[]>>) {
        const PATH = 'https://equipe500.tch099.ovh/projet6/api/list_reservations'
        
          const requestOptions = {
            method: "GET",
            headers: { 'Authorization': token},
          };
          fetch(PATH, requestOptions)
            .then(response => {
              if (!response.ok) return null;
              else return response.json()
            })
            .then(data => {
              if (data) {
                callbk(data)
              } else {
                return []
              }
            });
        }

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