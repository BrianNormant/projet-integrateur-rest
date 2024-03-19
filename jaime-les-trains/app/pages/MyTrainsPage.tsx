import { useEffect, useState } from "react";
import { Card } from "react-bootstrap";

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
    name: "Station C",
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
        <Card className="m-3 p-2">
            <Card.Title>
                {"Mes Trains"}
            </Card.Title>
            <Card.Body>
                {trains.map(x => <TrainComponent key={x.power} train={x}/>)}
            </Card.Body>
        </Card>
    )
}

interface TrainComponentProps {
    train: Train
}

function TrainComponent( {...props}: TrainComponentProps) {
    return (
        <div>
            <div className="d-flex justify-content-between">
                {props.train.route.path.map((x, i) => <StationComponent key={i} stationName={x.connection1.name}/>)}
                <StationComponent key={0} stationName={props.train.route.destination.name} noTrack={true} />
            </div>
        </div>
    )
}

interface StationComponentProps {
    key: number,
    stationName: string,
    noTrack?: boolean,
}

function StationComponent( {...props}: StationComponentProps ) {
    const {height, width} = useWindowDimensions();

    return (
        <div className="d-flex flex-column align-items-start station-wrapper">
            <div className="d-flex flex-column align-items-center">
                <p className="mb-0" key={props.key}>{props.stationName}</p>
                <div className="station-blip">
                {props.noTrack ? <></> : <div className="station-rail" style={{width: width/3}}></div>}
                </div>
            </div>
         </div>
    )
}

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


