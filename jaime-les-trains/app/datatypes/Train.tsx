interface Train {
    id: number
    company_id: String
    route: Station[]
    rail: Rail
    prev_station: Station
    next_station: Station
    charge: number
    speed: number
    pos: number
}
  