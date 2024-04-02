#include <stdio.h>
#include <stdint.h>
#include <math.h>

#define S(station) STATIONS+station
#define LEN(a) (sizeof(a)/sizeof(a[0]))

// In km
#define DESTINATION_DIMX 5972
#define DESTINATION_DIMY 2025

// In px
#define ORIGIN_DIMX 1232.0
#define ORIGIN_DIMY 545.0

typedef struct {
	char name[50];
	double x,y;
} Station;

typedef struct {
	Station *c1,*c2;
	double l;
} Rail;

enum station_id {
	WINDSOR,
	SARNIA,
	LONDON,
	STRATFORD,
	KICHENER,
	ALDERSHOT,
	NIAGARA_FALLS,
	OAKVILLE,
	TORONTO,
	BELLEVILLE,
	KINGSTON,
	BROCKVILLE,
	OTTAWA,
	DORVAL,
	MONTREAL,
	HERVEY,
	JONQUIERE,
	SENNETERRE,
	SAINTE_FOY,
	QUEBEC,
	RIVIERE_DU_LOUP,
	RIMOUSKI,
	MONT_JOLI,
	MATAPEDIA,
	CAMPBELLTON,
	BATHURST,
	MIRAMICHI,
	MONCTON,
	AMHERST,
	TRURO,
	HALIFAX,
	SUDBURY_JCT,
	SUDBURY,
	WHITE_RIVER,
	SIOUX_LOOKOUT,
	WINNINPEG,
	THE_PAS,
	THOMPSOM,
	CHURCHILL,
	SASKATOON,
	EDMONTON,
	JASPER,
	PRINCE_GEORGE,
	PRINCE_RUPERT,
	KAMLOOPS,
	VANCOUVER,
};
Station STATIONS[] = {
	[WINDSOR]          =  (Station)  {"Winsdor",          808,   488},
	[SARNIA]           =  (Station)  {"Sarnia",           810,   459},
	[LONDON]           =  (Station)  {"London",           835,   457},
	[STRATFORD]        =  (Station)  {"Stratford",        832,   442},
	[KICHENER]         =  (Station)  {"Kichener",         840,   429},
	[ALDERSHOT]        =  (Station)  {"Aldershot",        849,   442},
	[NIAGARA_FALLS]    =  (Station)  {"Niagara Falls",    870,   452},
	[OAKVILLE]         =  (Station)  {"Oakville",         855,   432},
	[TORONTO]          =  (Station)  {"Toronto",          864,   417},
	[BELLEVILLE]       =  (Station)  {"Belleville",       893,   396},
	[KINGSTON]         =  (Station)  {"Kingston",         918,   383},
	[BROCKVILLE]       =  (Station)  {"Brockville",       930,   364},
	[OTTAWA]           =  (Station)  {"Ottawa",           908,   349},
	[DORVAL]           =  (Station)  {"Dorval",           940,   341},
	[MONTREAL]         =  (Station)  {"Montréal",         947,   320},
	[HERVEY]           =  (Station)  {"Hervey",           937,   264},
	[JONQUIERE]        =  (Station)  {"Jonquiere",        939,   242},
	[SENNETERRE]       =  (Station)  {"Senneterre",       841,   280},
	[SAINTE_FOY]       =  (Station)  {"Sainte-Foy",       962,   263},
	[QUEBEC]           =  (Station)  {"Québec",           976,   248},
	[RIVIERE_DU_LOUP]  =  (Station)  {"Rivière-du-Loup",  1006,  224},
	[RIMOUSKI]         =  (Station)  {"Rimouski",         1011,  209},
	[MONT_JOLI]        =  (Station)  {"Mont-Joli",        1017,  194},
	[MATAPEDIA]        =  (Station)  {"Matapédia",        1028,  182},
	[CAMPBELLTON]      =  (Station)  {"Campbellton",      1041,  193},
	[BATHURST]         =  (Station)  {"Bathurst",         1056,  187},
	[MIRAMICHI]        =  (Station)  {"Miramichi",        1061,  202},
	[MONCTON]          =  (Station)  {"Moncton",          1090,  228},
	[AMHERST]          =  (Station)  {"Amherst",          1114,  235},
	[TRURO]            =  (Station)  {"Truro",            1127,  234},
	[HALIFAX]          =  (Station)  {"Halifax",          1129,  257},
	[SUDBURY_JCT]      =  (Station)  {"Sudbury Jct.",     814,   376},
	[SUDBURY]          =  (Station)  {"Sudbury",          793,   389},
	[WHITE_RIVER]      =  (Station)  {"White River",      730,   365},
	[SIOUX_LOOKOUT]    =  (Station)  {"Sioux Lookout",    616,   323},
	[WINNINPEG]        =  (Station)  {"Winnipeg",         516,   335},
	[THE_PAS]          =  (Station)  {"The pas",          451,   213},
	[THOMPSOM]         =  (Station)  {"Thompsom",         499,   179},
	[CHURCHILL]        =  (Station)  {"Churchill",        516,   82},
	[SASKATOON]        =  (Station)  {"Saskatoon",        394,   275},
	[EDMONTON]         =  (Station)  {"Edmonton",         312,   229},
	[JASPER]           =  (Station)  {"Jasper",           252,   233},
	[PRINCE_GEORGE]    =  (Station)  {"Prince George",    179,   189},
	[PRINCE_RUPERT]    =  (Station)  {"Prince Rupert",    103,   161},
	[KAMLOOPS]         =  (Station)  {"Kamloops",         205,   271},
	[VANCOUVER]        =  (Station)  {"Vancouver",        143,   290},
};

Rail RAILS[] = {
	(Rail)  {S(WINDSOR),          S(LONDON)},
	(Rail)  {S(LONDON),           S(SARNIA)},
	(Rail)  {S(LONDON),           S(STRATFORD)},
	(Rail)  {S(STRATFORD),        S(KICHENER)},
	(Rail)  {S(KICHENER),         S(TORONTO)},
	(Rail)  {S(LONDON),           S(ALDERSHOT)},
	(Rail)  {S(ALDERSHOT),        S(NIAGARA_FALLS)},
	(Rail)  {S(ALDERSHOT),        S(OAKVILLE)},
	(Rail)  {S(OAKVILLE),         S(TORONTO)},
	(Rail)  {S(TORONTO),          S(BELLEVILLE)},
	(Rail)  {S(BELLEVILLE),       S(KINGSTON)},
	(Rail)  {S(KINGSTON),         S(BROCKVILLE)},
	(Rail)  {S(BROCKVILLE),       S(OTTAWA)},
	(Rail)  {S(OTTAWA),           S(DORVAL)},
	(Rail)  {S(BROCKVILLE),       S(DORVAL)},
	(Rail)  {S(DORVAL),           S(MONTREAL)},
	(Rail)  {S(MONTREAL),         S(HERVEY)},
	(Rail)  {S(HERVEY),           S(JONQUIERE)},
	(Rail)  {S(HERVEY),           S(SENNETERRE)},
	(Rail)  {S(MONTREAL),         S(SAINTE_FOY)},
	(Rail)  {S(SAINTE_FOY),       S(QUEBEC)},
	(Rail)  {S(MONTREAL),         S(RIVIERE_DU_LOUP)},
	(Rail)  {S(RIVIERE_DU_LOUP),  S(RIMOUSKI)},
	(Rail)  {S(RIMOUSKI),         S(MONT_JOLI)},
	(Rail)  {S(MONT_JOLI),        S(MATAPEDIA)},
	(Rail)  {S(MATAPEDIA),        S(CAMPBELLTON)},
	(Rail)  {S(CAMPBELLTON),      S(BATHURST)},
	(Rail)  {S(BATHURST),         S(MIRAMICHI)},
	(Rail)  {S(MIRAMICHI),        S(MONCTON)},
	(Rail)  {S(MONCTON),          S(AMHERST)},
	(Rail)  {S(AMHERST),          S(TRURO)},
	(Rail)  {S(TRURO),            S(HALIFAX)},
	(Rail)  {S(TORONTO),          S(SUDBURY_JCT)},
	(Rail)  {S(SUDBURY_JCT),      S(SIOUX_LOOKOUT)},
	(Rail)  {S(SIOUX_LOOKOUT),    S(WINNINPEG)},
	(Rail)  {S(WINNINPEG),        S(THE_PAS)},
	(Rail)  {S(THE_PAS),          S(THOMPSOM)},
	(Rail)  {S(THOMPSOM),         S(CHURCHILL)},
	(Rail)  {S(WINNINPEG),        S(SASKATOON)},
	(Rail)  {S(SASKATOON),        S(EDMONTON)},
	(Rail)  {S(EDMONTON),         S(JASPER)},
	(Rail)  {S(JASPER),           S(KAMLOOPS)},
	(Rail)  {S(KAMLOOPS),         S(VANCOUVER)},
	(Rail)  {S(JASPER),           S(PRINCE_GEORGE)},
	(Rail)  {S(PRINCE_GEORGE),    S(PRINCE_RUPERT)},
	(Rail)  {S(SUDBURY),          S(WHITE_RIVER)}
};


int main(void) {
	const double factor_x = ORIGIN_DIMX / DESTINATION_DIMX;
	const double factor_y = ORIGIN_DIMY / DESTINATION_DIMY;

	puts("START TRANSACTION;");

	puts("DELETE FROM EQ06_Station;");

	puts("INSERT INTO EQ06_Station (nameStation, posX, posY) VALUES");
	for (uint64_t i = 0; i < LEN(STATIONS); i++) {
		Station *s = STATIONS+i;

		s->x = ORIGIN_DIMX - s->x;
		s->x /= factor_x;
		s->y /= factor_y;

		printf("('%s', %4.2f, %4.2f)", s->name, s->x, s->y);
		putchar((i == LEN(STATIONS) - 1)? ';' : ',');
		putchar('\n');
	}

	puts("INSERT INTO EQ06_Rail (conn1_station, conn2_station, longueur) VALUES");
	for (uint64_t i = 0; i < LEN(RAILS); i++) {
		Rail *r = &RAILS[i];
		r->l = sqrt(r->c1->x * r->c2->x + r->c1->y * r->c2->y);

		printf("((SELECT id FROM EQ06_Station WHERE nameStation = '%s'), (SELECT id FROM EQ06_Station WHERE nameStation = '%s'), %5.0f)", r->c1->name, r->c2->name, r->l);
		putchar((i == LEN(RAILS) - 1)? ';' : ',');
		putchar('\n');
	}

	puts("COMMIT;");

	return 0;
}
