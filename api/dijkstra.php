<?php
## Source:
## https://rosettacode.org/wiki/Dijkstra%27s_algorithm#PHP

/*
$graph_array = array(
	array("a", "b", 7),
	array("a", "c", 9),
	array("a", "f", 14),
	array("b", "c", 10),
	array("b", "d", 15),
	array("c", "d", 11),
	array("c", "f", 2),
	array("d", "e", 6),
	array("e", "f", 9)
);

$path = dijkstra($graph_array, "a", "e");
 */

function to_vertice($row) {
	return array(
		$row["conn1_station"],
		$row["conn2_station"],
		$row["longueur"],
	);
}

function fetch_network_as_graph($dbh) {
	$data = $dbh->query("SELECT conn1_station, conn2_station, longueur FROM EQ06_Rail")->fetchAll();
	return array_map("to_vertice", $data);
}

function dijkstra($graph_array, $source, $target) {
	$vertices = array();
	$neighbours = array();
	foreach ($graph_array as $edge) {
		array_push($vertices, $edge[0], $edge[1]);
		$neighbours[$edge[0]][] = array("end" => $edge[1], "cost" => $edge[2]);
$neighbours[$edge[1]][] = array("end" => $edge[0], "cost" => $edge[2]);
	}
	$vertices = array_unique($vertices);

	foreach ($vertices as $vertex) {
		$dist[$vertex] = INF;
		$previous[$vertex] = NULL;
	}

	$dist[$source] = 0;
	$Q = $vertices;
	while (count($Q) > 0) {

		// TODO - Find faster way to get minimum
		$min = INF;
		foreach ($Q as $vertex){
			if ($dist[$vertex] < $min) {
				$min = $dist[$vertex];
				$u = $vertex;
			}
		}

		$Q = array_diff($Q, array($u));
		if ($dist[$u] == INF or $u == $target) {
			break;
		}

		if (isset($neighbours[$u])) {
			foreach ($neighbours[$u] as $arr) {
				$alt = $dist[$u] + $arr["cost"];
				if ($alt < $dist[$arr["end"]]) {
					$dist[$arr["end"]] = $alt;
					$previous[$arr["end"]] = $u;
				}
			}
		}
	}
	$path = array();
	$u = $target;
	while (isset($previous[$u])) {
		array_unshift($path, $u);
		$u = $previous[$u];
	}
	array_unshift($path, $u);
	return $path;
}


?>
