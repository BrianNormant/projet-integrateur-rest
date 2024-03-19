#include <stdio.h>

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

Station stations[] = {
	(Station)  {"Toronto",     861,  417},
	(Station)  {"Brockville",  931,  364},
	(Station)  {"Ottawa",      907,  348},
	(Station)  {"Montreal",    947,  320},
	(Station)  {"SainteFoy",   961,  263},
	(Station)  {"Quebec",      976,  246},
	(Station)  {"Winnipeg",    517,  333},
	(Station)  {"Edmonton",    312,  230},
	(Station)  {"Vancouver",   141,  290},
};

int main(void) {
	const double factor_x = ORIGIN_DIMX / DESTINATION_DIMX;
	const double factor_y = ORIGIN_DIMY / DESTINATION_DIMY;
	for (long unsigned i = 0; i < sizeof(stations)/sizeof(Station); i++) {
		Station *s = stations+i;

		s->x = ORIGIN_DIMX - s->x;
		s->x /= factor_x;
		s->y /= factor_y;

		printf("format(\"%s\", %.0f, %.0f),\n", s->name, s->x, s->y);
	}

	return 0;
}
