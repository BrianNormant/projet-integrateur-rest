import { Dispatch, SetStateAction, useEffect, useState } from "react";
import { Button, Card, Form, InputGroup } from "react-bootstrap";
import { authProps } from "../page";
import internal from "stream";

interface MyReservationsPageProps {
    token: string
    username: string
}

export function MyReservationsPage( {...props}: MyReservationsPageProps) {

    const [res, setRes] = useState<Reservation[]>([])
    const [isOpen, setIsOpen] = useState(false)
    const [solde, setSolde] = useState(0)

    useEffect(() => {loadReservations(props.token, setRes); getSolde(props.token, props.username, setSolde)}, []);

    return (
        <div className="m-3">
            <div className="mb-3">
                <Button className="me-2" onClick={() => {loadReservations(props.token, setRes)}}>
                        {"Rafraichir"}
                    </Button>
                {isOpen ? <AddReservation token={props.token}/> : 
                    <Button onClick={() => setIsOpen(true)}>
                        {"Ajouter une reservation"}
                    </Button>
                }
            </div>
            <Card className="p-2 mb-2">
                <div className="d-flex justify-content-between">
                    <Card.Title>
                        {"Mes Reservations"}
                    </Card.Title>
                    <p>{"Votre solde: " + solde + "$"}</p>
                </div>
                <Card.Body>
                    {res.length == 0 ? 
                        <p>{"Vous n'avez aucune reservation"}</p> :
                    res.map((x, i) => <ReservationComponent key={i} res={x}/>)}
                </Card.Body>
            </Card>
        </div>
    )
}

function ReservationComponent({...props}: {res: Reservation}) {
    return (
        <Card className="mb-4">
            <Card.Header className="mb-0">
                <Card.Title>{"Reservation #" + props.res.id}</Card.Title>
            </Card.Header>
            <Card.Body className="m-2 p-1 d-flex justify-content-between">
                <div>
                    <p className="mb-1">{"Nom de la compagnie: " + props.res.company_id}</p>
                    <p className="mb-1">{"Date: " + props.res.date}</p>
                    <p className="mb-1">{"Plage Horaire: " + props.res.period}</p>
                </div>
                <div>
                    <p  className="mb-1">{"ID du rail: " + props.res.rail}</p>
                </div>
            </Card.Body>
        </Card>
    )
}

function getSolde(token: string, name: string, callbk: Dispatch<SetStateAction<number>>) {
        const PATH = 'https://equipe500.tch099.ovh/projet6/api/user/'+name+'/solde'

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
                    callbk(data.solde)
                } else {
                return []
                }
            });
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

        interface StationDetail {
            id: number,
            name: string,
            pos_x: string,
            pos_y: string
        }

function AddReservation( {...props}: authProps ) {
    const [stations, setStations] = useState<StationDetail[]>([])
    const [origin, setOrigin] = useState(0);
    const [dest, setDest] = useState(0);
    const [plage, setPlage] = useState("evening")
    const [date, setDate] = useState("2024-04-24")

    useEffect(() => loadStations(setStations), [])

    function loadStations(callbk: Dispatch<SetStateAction<StationDetail[]>>) {
        const PATH = 'https://equipe500.tch099.ovh/projet6/api/stations'
        
        const requestOptions = {
            method: "GET",
        };
        fetch(PATH, requestOptions)
            .then(response => {
            if (!response.ok) return null;
            else return response.json()
            })
            .then(data => {
            if (data) {
                return(data)
            } else {
                return []
            }
            }).then(data => callbk(data)).catch(() => console.log("nuh uh"));
        }

    function findStationFromName(name: string): number {
        let returnval = 0
        stations.forEach(x => {
            if (x.name == name) returnval = x.id
        })

        return returnval
    }

    function stationOptionComponent(onChange:Dispatch<SetStateAction<number>>): JSX.Element {
        return (
            <Form.Select onChange={e => onChange(findStationFromName(e.target.value))}>
                {stations.map(x => <option key={x.id}>{x.name}</option>)}
            </Form.Select>
        )
    }

    function addReservation(token: string, origin: number, destination: number, date: string, period: string) {
        const PATH = 'https://equipe500.tch099.ovh/projet6/api/reservations/'+origin+'/'+destination+'?date='+date+'&period='+period
        console.log(PATH)

        const requestOptions = {
            method: "PUT",
            headers: { 'Authorization': token},
        };
        fetch(PATH, requestOptions)
            .then(response => {
            return response.status
            })
            .then(data => console.log(data));
    }

    return (
            <Card className="p-2 my-2">
                <Card.Title>
                    {"Ajouter une reservation"}
                </Card.Title>
                <Card.Body>
                    <Form>
                        <div className="d-flex">
                            {stationOptionComponent(setOrigin)}
                            {stationOptionComponent(setDest)}
                        </div>
                        <Form.Select onChange={e => setPlage(e.target.value)}>
                            <option>{"morning"}</option>
                            <option>{"evening"}</option>
                            <option>{"night"}</option>
                        </Form.Select>
                        <Form.Control onChange={(e) => setDate(e.target.value)} type="date" placeholder="name@example.com" />
                        <Button variant="primary" onClick={() => addReservation(props.token, origin, dest, date, plage)}>
                            {"Ajouter"}
                        </Button>
                    </Form>
                </Card.Body>
            </Card>
    );
}