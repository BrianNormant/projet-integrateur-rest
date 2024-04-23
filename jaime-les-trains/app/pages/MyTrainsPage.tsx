import { create } from "domain";
import { Dispatch, SetStateAction, useEffect, useState } from "react";
import { Button, Card, Form } from "react-bootstrap";
import { authProps } from "../page";

let buffer_trains: Train[] = []

export function MyTrainsPage( {...props}: authProps ) {

    const [trains, setTrains] = useState<Train[]>([]);
    //const [isOpen, setIsOpen] = useState(false)

    useEffect(() => loadTrains(props.token, buffer_trains, setTrains), []);

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
            <Button onClick={() => {setTrains([]);buffer_trains=[];loadTrains(props.token, buffer_trains, setTrains)}}>
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

export function loadTrains(token: string, buffer: Train[], callbk: Dispatch<SetStateAction<Train[]>>) {
    const PATH = 'https://equipe500.tch099.ovh/projet6/api/trains'
    
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
            let trains: Train[] = []
            data.map((x: { rail_id: any; id: number; }, i: number) => {
                    if (x.rail_id) addTrainData(x.id, token, trains, i==data.length-1, callbk)
            })
          } else {
            return []
          }
        });
    }

function addTrainData(trainid: number, token: string, buffer: Train[], last:boolean, callbk: Dispatch<SetStateAction<Train[]>>) {
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
            buffer.push(data)
            return buffer
          } else {
            return []
          }
        }).then(
            data => {if (last) callbk(data)}
        ).catch(() => console.log("nuh uh"));
    }

interface AddTrainProps {
    addTrain: (x: Train) => void
}

interface StationDetail {
    id: number,
    name: string,
    pos_x: string,
    pos_y: string
}

function AddTrain( {...props}: AddTrainProps ) {

    const [stations, setStations] = useState<StationDetail[]>([])
    const [origin, setOrigin] = useState("Station A");
    const [dest, setDest] = useState("Station A");

    useEffect(() => loadStations(setStations), [])
    console.log(stations)

    function loadStations(callbk: Dispatch<SetStateAction<StationDetail[]>>) {
        console.log("called")
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

    function stationOptionComponent(): JSX.Element {
        return (
            <>
                {stations.forEach(x => <option key={x.id}>{x.name}</option>)}
            </>
        )
    }

    return (
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Ajouter un train"}
                </Card.Title>
                <Card.Body>
                    <Form>
                        <Form.Select onChange={e => setOrigin(e.target.value)}>
                            <>{stations.forEach(x => <OptionComponent x={x}/>)}</>
                        </Form.Select>
                        <Form.Select onChange={e => setDest(e.target.value)}>
                            {stationOptionComponent()}
                        </Form.Select>
                        <Button variant="primary" onClick={() => console.log(origin, dest)}>
                            {"Ajouter"}
                        </Button>
                    </Form>
                </Card.Body>
            </Card>
    )
}

function OptionComponent( {...props}: {x: StationDetail} ) {
    return (
        <option>{props.x.name}</option>
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
    props.train.route.forEach((x, i) => x == props.train.next_station ? currentTrackIndex = i : "")

    let trackWidth: number = (width)/props.train.route.length;

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


