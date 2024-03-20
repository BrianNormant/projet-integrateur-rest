import { useEffect, useState } from "react";
import { Button, Card } from "react-bootstrap";

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
    currentRail: railAB,
    lastStation: stationA,
    nextStation: stationB,
    load: 100,
    power: 100,
    relative_position: 0.5,
}

export function MyTrainsPage( ) {

    const [trains, setTrains] = useState([train1]);

    return (
        <div className="m-3">
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Mes Trains"}
                </Card.Title>
                <Card.Body>
                    {trains.map(x => <TrainComponent key={x.power} train={x}/>)}
                </Card.Body>
            </Card>
            <Button>
                {"Ajouter un train"}
            </Button>
        </div>
    )
}

interface TrainComponentProps {
    train: Train
}

function TrainComponent( {...props}: TrainComponentProps) {

    const {height, width} = useWindowDimensions();
    //Note - le map ici assume que props.train.route.path est coherent relativement a props.train.route.destination
    //       cad on assume que connection2 de l'element x de route est le meme que l'element x+1 de route
    //       et que connection 2 du dernier element de route est le meme que la destination finale
    return (
        <div>
            <div className="d-flex justify-content-between">
                {props.train.route.path.map((x, i) => <StationComponent key={i} stationName={x.connection1.name} tracklength={(width-150)/props.train.route.path.length}/>)}
                <StationComponent key={0} stationName={props.train.route.destination.name} noTrack={true} />
            </div>
        </div>
    )
}

interface StationComponentProps {
    key: number,
    stationName: string,
    noTrack?: boolean,
    tracklength?: number,
}

function StationComponent( {...props}: StationComponentProps ) {

    //TODO changer le magic number ici. Potentiellement une constante a extraire mais la meilleure solution serait de
    //     calculer la valeur exacte des marges pq live si les stations ont des noms de tailles variable tt brise

    return (
        <div className="d-flex flex-column align-items-start station-wrapper">
            <div className="d-flex flex-column align-items-center">
                <p className="mb-0" key={props.key}>{props.stationName}</p>
                <div className="station-blip">
                {props.noTrack ? <></> : <div className="station-rail" style={{width: props.tracklength}}></div>}
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


