import { create } from "domain";
import { Dispatch, SetStateAction, useEffect, useState } from "react";
import { Button, Card, Form } from "react-bootstrap";
import { authProps } from "../page";

var stationA = {
    name: "Station A",
    position_x: 1,
    position_y: 1,
    free: true,
}

var stationB = {
    name: "Station B",
    position_x: 1,
    position_y: 2,
    free: true,
}

var stationC = {
    name: "Susmogus",
    position_x: 2,
    position_y: 1,
    free: true,
}

var railAB = {
    connection1: stationA,
    connection2: stationB,
    max_grade: 100,
    length: 1
}

var railBC = {
    connection1: stationB,
    connection2: stationC,
    max_grade: 100,
    length: 1
}

var railCA = {
    connection1: stationC,
    connection2: stationA,
    max_grade: 100,
    length: 1
}

var train1 = {
    route: {
        origin: stationA,
        destination: stationA,
        path: [railAB, railBC, railCA]
    },
    currentRail: railBC,
    lastStation: stationA,
    nextStation: stationB,
    load: 100,
    power: 100,
    relative_position: 0.8,
}

export function MyTrainsPage( {...props}: authProps ) {

    const [trains, setTrains] = useState<Train[]>([]);
    const [isOpen, setIsOpen] = useState(false)

    function loadTrain(train: Train) {
        setTrains([...trains, train])
    }

    if (!trains.length) loadTrains(props.token, loadTrain);
    console.log(trains)

    return (
        <div className="m-3">
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Mes Trains"}
                </Card.Title>
                <Card.Body>
                    {trains.length == 0 ? 
                    <p>{"Vous n'avez aucun train actuellement en fonctionnement"}</p> :
                    trains.map((x, i) => <TrainComponent key={i} train={x}/>)}
                </Card.Body>
            </Card>
            <Button onClick={() => {setTrains([]);loadTrains(props.token, loadTrain)}}>
                    {"Rafraichir"}
                </Button>
            {/*isOpen ? <AddTrain addTrain={(x) => {setTrains([...trains, x])}}/> : 
                <Button onClick={() => setIsOpen(true)}>
                    {"Ajouter un train"}
                </Button>
    */}
        </div>
    )
}

export function loadTrains(token: string, callbk: (t: Train) => void) {
    const PATH = 'https://equipe500.tch099.ovh/projet6/api/trains'

    console.log("called")
    
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
            console.log(data)
          if (data) {
            data.map((x: { rail_id: any; id: number; }) => {
                    console.log(x)
                    if (x.rail_id) addTrainData(x.id, token, callbk)
            })
          } else {
            return
          }
        });
    }


function addTrainData(trainid: number, token: string, callbk: (t: Train) => void) {
    const PATH = 'https://equipe500.tch099.ovh/projet6/api/train/'+trainid+'/details'
    
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
            console.log(data)
            callbk(data)
          } else {
            return
          }
        }).catch(() => console.log("nuh uh"));
    }

interface AddTrainProps {
    addTrain: (x: Train) => void
}

/*function AddTrain( {...props}: AddTrainProps ) {

    function createTrain(origin: string, destination: string) {
        let originStation: Station = {
            name: origin,
            position_x: 10,
            position_y: 10,
            free: true
        }

        let destinationStation: Station = {
            name: destination,
            position_x: 10,
            position_y: 10,
            free: true
        }

        let rail: Rail = {
            connection1: originStation,
            connection2: destinationStation,
            max_grade: 100,
            length: 1
        }

        return {
            route: {
                origin: originStation,
                destination: destinationStation,
                path: [rail]
            },
            currentRail: rail,
            lastStation: originStation,
            nextStation: destinationStation,
            load: 100,
            power: 100,
            relative_position: 0,
        }
    }

    const [origin, setOrigin] = useState("Station A");
    const [dest, setDest] = useState("Station A");

    return (
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Ajouter un train"}
                </Card.Title>
                <Card.Body>
                    <Form>
                        <Form.Select onChange={e => setOrigin(e.target.value)}>
                            <option>{"Station A"}</option>
                            <option>{"Station B"}</option>
                            <option>{"Station C"}</option>
                        </Form.Select>
                        <Form.Select onChange={e => setDest(e.target.value)}>
                            <option>{"Station A"}</option>
                            <option>{"Station B"}</option>
                            <option>{"Station C"}</option>
                        </Form.Select>
                        <Button variant="primary" onClick={() => props.addTrain(createTrain(origin, dest))}>
                            {"Ajouter"}
                        </Button>
                    </Form>
                </Card.Body>
            </Card>
    )
}/*/

interface TrainComponentProps {
    train: Train
}

function TrainComponent( {...props}: TrainComponentProps) {

    const {height, width} = useWindowDimensions();
    //Note - le map ici assume que props.train.route.path est coherent relativement a props.train.route.destination
    //       cad on assume que connection2 de l'element x de route est le meme que l'element x+1 de route
    //       et que connection 2 du dernier element de route est le meme que la destination finale

    //Note - On assume ici que chaque rail est traverse au maximum une seule fois
    let currentTrackIndex: number = 0;
    props.train.route.forEach((x, i) => x == props.train.next_station ? currentTrackIndex = i : "")

    let trackWidth: number = (width-150)/props.train.route.length;

    return (
        <div>
            <div className="d-flex justify-content-between">
                {props.train.route.map((x, i) => <StationComponent key={i} stationName={x.name} tracklength={trackWidth} stationPassed={i<=currentTrackIndex} trackStatus={i==currentTrackIndex ? props.train.pos/100 * trackWidth : (i < currentTrackIndex ? trackWidth : 0)}/>)}
                <StationComponent key={0} stationName={"test"} noTrack={true} />
            </div>
        </div>
    )
}

interface StationComponentProps {
    key: number,
    stationName: string,
    stationPassed?: boolean,
    noTrack?: boolean,
    tracklength?: number,
    trackStatus?: number,
}

function StationComponent( {...props}: StationComponentProps ) {

    //TODO changer le magic number ici. Potentiellement une constante a extraire mais la meilleure solution serait de
    //     calculer la valeur exacte des marges pq live si les stations ont des noms de tailles variable tt brise

    return (
        <div className="d-flex flex-column align-items-start station-wrapper">
            <div className="d-flex flex-column align-items-center">
                <p className="mb-0" key={props.key}>{props.stationName}</p>
                <div className={props.stationPassed ? "station-blip passed" : "station-blip"}>
                {props.noTrack ? <></> : 
                    <>
                        <div className="station-rail" style={{width: props.tracklength}}></div>
                        <div className="station-rail passed" style={{width: props.trackStatus}}></div>
                    </>
                }
                </div>
            </div>
         </div>
    )
}

//TODO attribution
function getWindowDimensions() {
  const { innerWidth: width, innerHeight: height } = window;
  return {
    width,
    height
  };
}

export default function useWindowDimensions() {
  const [windowDimensions, setWindowDimensions] = useState(getWindowDimensions());

  useEffect(() => {
    function handleResize() {
      setWindowDimensions(getWindowDimensions());
    }

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  return windowDimensions;
}


