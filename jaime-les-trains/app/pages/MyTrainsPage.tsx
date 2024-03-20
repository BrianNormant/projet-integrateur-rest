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
    currentRail: railBC,
    lastStation: stationA,
    nextStation: stationB,
    load: 100,
    power: 100,
    relative_position: 0.8,
}

export function MyTrainsPage( ) {

    const [trains, setTrains] = useState([train1, {...train1, relative_position: 0.3}]);

    return (
        <div className="m-3">
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Mes Trains"}
                </Card.Title>
                <Card.Body>
                    {trains.map((x, i) => <TrainComponent key={i} train={x}/>)}
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

    //Note - On assume ici que chaque rail est traverse au maximum une seule fois
    let currentTrackIndex: number = 0;
    props.train.route.path.forEach((x, i) => x == props.train.currentRail ? currentTrackIndex = i : "")

    let trackWidth: number = (width-150)/props.train.route.path.length;

    return (
        <div>
            <div className="d-flex justify-content-between">
                {props.train.route.path.map((x, i) => <StationComponent key={i} stationName={x.connection1.name} tracklength={trackWidth} stationPassed={i<=currentTrackIndex} trackStatus={i==currentTrackIndex ? props.train.relative_position * trackWidth : (i < currentTrackIndex ? trackWidth : 0)}/>)}
                <StationComponent key={0} stationName={props.train.route.destination.name} noTrack={true} />
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


