start transaction;

INSERT INTO EQ06_Route (origin_station, destination_station) VALUES
	((SELECT id FROM EQ06_Station WHERE nameStation = 'Vancouver'),
	 (SELECT id FROM EQ06_Station WHERE nameStation = 'Jasper'));

INSERT INTO EQ06_RailRoute ( rail_id, route_id, nb_stop) VALUES
	((SELECT id FROM EQ06_Rail WHERE conn1_station = 241 AND conn2_station = 240 OR conn1_station = 240 AND conn2_station = 241), 3, 0),
	((SELECT id FROM EQ06_Rail WHERE conn1_station = 237 AND conn2_station = 240 OR conn1_station = 240 AND conn2_station = 237), 3, 1);

INSERT INTO EQ06_Train (charge, puissance, company_id, route_id, relative_position, currentRail, lastStation, nextStation) VALUES
	( 40, 4000, 'PHP', 3, 17, 97, 241, 240);

rollback;

DELIMITER $$
CREATE OR REPLACE FUNCTION EQ06_selectother(IN c1 INT, IN c2 INT, IN s INT) RETURNS INT
BEGIN
	if (s = c1) then
		return c2;
	else
		return c1;
	end if;
END;
$$
DELIMITER ;

