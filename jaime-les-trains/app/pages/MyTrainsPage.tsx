import { create } from "domain";
import { Dispatch, SetStateAction, useEffect, useState } from "react";
import { Button, Card, Form } from "react-bootstrap";
import { authProps } from "../page";

let buffer_trains: Train[] = []

export function MyTrainsPage( {...props}: authProps ) {

    const [trains, setTrains] = useState<SimpleTrain[]>([]);
    //const [isOpen, setIsOpen] = useState(false)

    useEffect(() => loadTrains(props.token, buffer_trains, setTrains), []);

    return (
        <div className="m-3">
            <div className="mb-3">
                <Button className="me-2" onClick={() => {setTrains([]);buffer_trains=[];loadTrains(props.token, buffer_trains, setTrains)}}>
                        {"Rafraichir"}
                    </Button>
                {/*isOpen ? <AddTrain addTrain={(x) => {setTrains([...trains, x])}}/> : 
                    <Button onClick={() => setIsOpen(true)}>
                        {"Ajouter un train"}
                    </Button>
                    */}
            </div>
            <Card className="p-2 mb-2">
                <Card.Title>
                    {"Mes Trains"}
                </Card.Title>
                <Card.Body>
                    {trains.length == 0 ? 
                    <p>{"Vous n'avez aucun train actuellement en fonctionnement"}</p> :
                    trains.map((x, i) => <TrainComponent key={i} train={x} token={props.token}/>)}
                </Card.Body>
            </Card>
            
        </div>
    )
}

export function loadTrains(token: string, buffer: Train[], callbk: Dispatch<SetStateAction<SimpleTrain[]>>) {
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
            data.filter((x: { rail_id: any; }) => x.rail_id)
            callbk(data)
            /*let trains: Train[] = []
            data.map((x: { rail_id: any; id: number; }, i: number) => {
                    if (x.rail_id) addTrainData(x.id, token, trains, i==data.length-1, callbk)
            })*/
          } else {
            return []
          }
        });
    }

function addTrainData(trainid: number, token: string, callbk: Dispatch<SetStateAction<Train|null>>) {
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
            callbk(data)
          } else {
            return []
          }
        }).catch(() => console.log("nuh uh"));
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

interface SimpleTrain {
    id: number,
    rail_id: number,
    pos: number
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
    train: SimpleTrain
    token: string
}

function TrainComponent( {...props}: TrainComponentProps) {

    const [dTrain, setDTrain] = useState<Train|null>(null)
    const {height, width} = useWindowDimensions();
    
    useEffect(() => addTrainData(props.train.id, props.token, setDTrain), [])

    if (dTrain) {
        let currentTrackIndex: number = 0;
        dTrain.route.forEach((x, i) => x.name == dTrain.next_station.name ? currentTrackIndex = i : "")

        let trackWidth: number = (width-150);

        return (
            <div>
                <div className="d-flex justify-content-between">
                    {dTrain.route.map((x, i) => <StationComponent key={i} noTrack={i!=0} stationName={x.name} tracklength={trackWidth} stationPassed={i<=currentTrackIndex} trackStatus={((currentTrackIndex+ (currentTrackIndex==dTrain.route.length-1 ? 0 : (dTrain.pos/100)))/(dTrain.route.length-1)) * trackWidth}/>)}
                </div>
            </div>
        )
    } else return <></>
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


