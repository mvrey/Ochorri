/* LOS NOMBRES DE LAS TABLAS COMIENZAN POR MAY�SCULA Y NO LLEVAN TILDES. ADEM�S NO TENDR�N ESPACIOS EN BLANCO.
LOS NOMBRES DE LAS ENTIDADES COMIENZAN POR MAY�SCULAS PERO SI LLEVAN TILDES.
LAS PALABRAS QUE NO SEAN ADVERBIOS, CONJUNCIONES O ART�CULOS COMENZAR�N TAMBIEN POR MAY�SCULA
LOS ATRIBUTOS DE LAS TABLAS ESTAR�N TODO EN MIN�SCULAS Y TENDR�N TILDES SOLO SI SE VA A MOSTRAR EN EL JUEGO LA CADENA ALMACENADA*/

/* 3.1 CAMBIADA TABLA EJERCITO */
/* 3.3 A�ADIDO DESCRIPCI�N EN EDIFICIO_TIPO */
/* 3.4 A�ADIDO PORCENTAJE, DETENIDO EN EDIFICIO. A�ADIDO IMAGEN EN EDIFICIO_TIPO*/
/* 4.0 Cambiados todos los nombres a ingl�s, soporte multiidioma, convenciones de nombrado, restricciones en campos y los costes/mantenimientos/producciones son ahora un string que se desguaza en código*/
/* 4.1 Backup 14/03/11 */

drop database ochorri;
create database ochorri;
use ochorri;


create table Unit (			/* Aqui se almacenan solo las unidades que no tienen hijos (las instanciables)*/
unit_id int not NULL AUTO_INCREMENT PRIMARY KEY,
unit_nameId int not NULL,

unit_attack int DEFAULT 0,
unit_health int DEFAULT 0,
unit_speed int DEFAULT 0,

unit_startAge int DEFAULT 0,		/* La epoca en que aparece para ser producida*/
unit_endAge int DEFAULT 0,		/* La ultima epoca en la que puede producirse*/

unit_pictureURL varchar(100) DEFAULT '',	/* en formato Hyperlink, guarda la direccion de la imagen asociada a esa unidad*/
unit_classId varchar(15),			/* Superclase de la unidad (Infanteria, caballeria, artilleria... etc)*/

unit_productionCost varchar(30) DEFAULT '0,0,0,0,0',	/* Costes de PRODUCCION de una unidad*/
unit_manteinanceCost varchar(30) DEFAULT '0,0,0,0,0',	/* Costes de MANTENIMIENTO de una unidad*/
unit_advanceCost varchar(30) DEFAULT '0,0,0,0,0',	/* Costes de PASO DE ERA de una unidad*/

unit_time int NOT NULL DEFAULT 0,	/*de fabricaci�n, en segundos*/
unit_descriptionId int,

unit_upgradesTo int DEFAULT NULL,	/*Id de la unidad en la que se convierte*/
unit_autoUpgrade bool DEFAULT 0	/*Sube automáticamente al avanzar de era*/
);

/* Esta tabla relaciona cada uno de los t�rminos traducibles con su tabla origen y sus traducciones*/
create table Term (
term_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
term_spanish varchar(150) DEFAULT '',
alias varchar(30) DEFAULT NULL
);


create table UnitClass (
unitClass_id int NOT NULL PRIMARY KEY,
unitClass_nameId int not NULL,
unitClass_transitable varchar(10) DEFAULT ''	/*Ground, Water, Coast*/

);


/************************************************/


/*Guarda todos los requisitos para que aparezca una unidad/edificio/tecnologia o se pueda pasar de era*/
create table Requirement (
requirement_id int not NULL AUTO_INCREMENT PRIMARY KEY,
requirement_targetId int,	/*A lo que se le aplican los requisitos*/
requirement_targetClassId varchar(30) not NULL,	/*Tipo del objetivo, controlable en el programa con un switch case)*/

requirement_requirementId int,		/* nombre del requisito*/
requirement_requirementClass varchar(30) not NULL,	/* tipo del requisito */
requirement_level int
);


/************************************************/


create table Resource (
resource_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
resource_nameId int,

resource_startAge int,
resource_endAge int,			/* La ultima epoca donde aparece el recurso*/

resource_basic bool,			/*Determina si el recurso es basico (todos los territorios lo producen)*/

resource_img varchar(30) DEFAULT ''
);


/*************************************************/


create table Sector (
sector_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
sector_coordinateX int not NULL,
sector_coordinateY int not NULL,

sector_name varchar(30) DEFAULT '', 
sector_occupantId int,
sector_ownerId int,
sector_isLand bool,			/*1=terrestre 0=maritimo*/
sector_edge bool,			/*1=borde del mapa*/

sector_productionId varchar(100) DEFAULT '0,0,0,0,0',	/* Produccion Total de un sector*/
sector_CostId varchar(30) DEFAULT '0,0,0,0,0',			/* Gasto Total de un sector*/

sector_isBattle bool DEFAULT false,

sector_productionBase varchar(30) DEFAULT '5,50,30,20,0'
);

ALTER TABLE Sector AUTO_INCREMENT = 0;

/************************************************/


create table Division (
division_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
division_ownerId int not NULL,
division_sectorId int not NULL,

division_unitId int,
division_quantity int,
division_remainingHealth int DEFAULT 0	/*Used on battles*/
);


/************************************************/

create table DivisionMovement (
divisionMovement_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,

divisionMovement_unitList VARCHAR(100) DEFAULT '',
divisionMovement_quantityList VARCHAR(100) DEFAULT '',
divisionMovement_ownerId int NOT NULL,
divisionMovement_startSectorId int NOT NULL,
divisionMovement_endSectorId int NOT NULL,
DivisionMovement_startDateTime int NOT NULL,
DivisionMovement_time int NOT NULL
);

/************************************************/
create table BuildingClass (		/*Aqui se guardan exclusivamente los edificios (clase, no instancia)*/
buildingClass_id int NOT NULL PRIMARY KEY,
buildingClass_nameId int,

buildingClass_pictureURL varchar(100) DEFAULT '',

buildingClass_health int DEFAULT 0,

buildingClass_startAge int,
buildingClass_endAge int,
buildingClass_upgradable bool,		/*1=Si 0=No*/

buildingClass_productionCost varchar(30) DEFAULT '0,0,0,0,0',			/* Costes de PRODUCCION*/
buildingClass_incrementCost varchar(30) DEFAULT '0,0,0,0,0',		/*Factor de aumento de costes por nivel*/
buildingClass_manteinanceCost varchar(30) DEFAULT '0,0,0,0,0',	/*Costes de mantenimiento por edificio*/
buildingClass_advanceCost varchar(30) DEFAULT '0,0,0,0,0',		/*Costes de paso de era*/

buildingClass_time int,					/* de construcci�n, en segundos*/
buildingClass_incrementTime float(2),	/* incremento del tiempo de construcci�n por nivel*/

buildingClass_description varchar(300),

buildingClass_upgradesTo int DEFAULT NULL,	/*Id de la unidad en la que se convierte*/
buildingClass_autoUpgrade bool DEFAULT 0	/*Sube automáticamente al avanzar de era*/
);


/***************************************************/


create table ProductionMod (
productionMod_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,

productionMod_targetClassId varchar(30) NOT NULL DEFAULT '',	/* Tipo de lo que tiene el modificador (unit, building, technology) */
productionMod_targetId int,

productionMod_resourceId int,	/*Recurso para el que aumenta la producci�n*/
productionMod_operation char(1),
productionMod_value float(2),		/*Si en una tupla magnitud=NULL, entonces se procesa el operando2 */
productionMod_operator2 float(2)
);


/****************************************************/


create table BattleMod (
battleMod_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
battleMod_nameId int,

battleMod_operation char(1),
battleMod_value float(2),			/*Si en una tupla value=NULL, entonces se procesa el operando2 */
battleMod_targetClassId int,		/*TIPO del objetivo contra el que act�a el modificador*/

battleMod_operator2 varchar(30) DEFAULT NULL
);


/****************************************************/


create table BattleModLink (
battleModLink_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
battleModLink_battleModId int,
battleModLink_targetId int		/*Id de la unidad que posee el modificador*/
);


/****************************************************/


create table Building (		/*Aqui se guardan los edificios que hay en cada sector (descompuesto por redundancia, se guardan las instancias de edificio_tipo)*/
building_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
building_BuildingClassId int,
building_sectorId int,

building_level int, 		/* o cantidad, seg�n el tipo de edificio */

building_dateStarted varchar(20),	/*Fecha y hora en la que se puso a constru�r. El porcentaje contru�do se calcula a partir de esto y el tiempo de construcci�n*/
building_dateStopped varchar(20),	/*Fecha y hora en la que se detuvo la construcci�n. Si esto vale NULL significa que no est� detenido. Todo se puede calcular a partir de este valor y el superior*/

building_remainingHealth int DEFAULT 0
);


/****************************************************/


create table Player (
player_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,

player_nick varchar(30) not NULL,		/* Nombre de Jugador*/
player_password varchar(50) not NULL,	/* contraseña del jugador*/
player_email varchar(50) DEFAULT '',
player_age int,			/*La epoca en la que se encuentra ese jugador*/
player_flag varchar(100) DEFAULT 'trollface.png',
player_avatar varchar(100) DEFAULT 'trollface.png',
player_civName varchar(30) DEFAULT '',

player_resources varchar(30) DEFAULT '0,0,0,0,0',	/*cantidad total de recursos que tiene el jugador*/
player_lastUpdate varchar(30) DEFAULT '',
player_isLogged boolean DEFAULT false,
player_lastMapOrigin varchar(10) DEFAULT '0,0',
player_lastMapHeight int DEFAULT 5
);


/***************************************************/


create table Age (
age_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
age_nameId int,

age_pictureURL varchar(100),	/* En formato hyperlink, guarda la direccion de la imagen de fondo de esa epoca*/
age_advanceCost varchar(30) DEFAULT '0,0,0,0,0'	/*Costes base para pasar a la siguiente epoca*/
);


/****************************************************/


create table Technology (	
technology_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
technology_nameId int,

technology_startAge int,		/*La epoca en la que aparece automaticamente en el menu y se puede investigar*/
technology_endAge int,			/*en la cual desaparece*/
technology_visibleAge int,		/*La epoca en que se puede ir progresando al ganar batallas (no se puede investigar)*/

technology_cost varchar(30),		/* Costes de investigaci�n por cada 1% */
technology_incrementCost varchar(30),	/* Factor por el cual se debve multiplicar el coste original para hallar el coste de cada */
					/* nuevo nivel. e.g. para hallar el coste del nivel 4 deber�a aplicarse 3 veces recurs.*/
					/* Si el coste original es 0 o NULL, el incremento es irrelevante, seguir� siendo 0, */
					/* para suplir esa atadura est�n las techs no nivelables */
					/* (hay una ligerisima redundancia, pero aun asi parece una buena solucion) */

technology_upgradable bool,		/* 1=Se puede ir subiendo de nivel   0=solo se puede investigar una vez */

technology_time int,			/* que tarda cada 1% en investigarse*/
technology_incrementTime float(2),	/* Valor decimal por el que se multiplica el tiempo base por nivel */

technology_pictureURL varchar(100),

technology_descriptionId int,

technology_isAge bool DEFAULT 0
);


/*******************************************************/


create table TechnologyLink (
technologyLink_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
technologyLink_technologyId int,
technologyLink_playerId int,		/*Del jugador, obviamente*/

technologyLink_level int DEFAULT 0,		/*Nivel actual, no el nivel que se est� investigando. Si es �nica y se ha obtenido nivel=1 progreso=100*/
technologyLink_progress float(2) DEFAULT 0.00,	/* El % avanzado para descubrir/mejorar dicha tech*/

technologyLink_dateStartProgress varchar(20),	/* Fecha en la que se ha dado la orden de continuar el progreso */
technologyLink_dateEndProgress varchar(20)	/* Fecha de finalizaci�n del progreso (probablemente mejor que guardar el %) */
);


create table TrainingQueue (
trainingQueue_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
trainingQueue_sectorCoordinateX int,
trainingQueue_sectorCoordinateY int,
trainingQueue_ownerId int,
trainingQueue_unitList varchar(200),
trainingQueue_timeList varchar(200),
trainingQueue_startDateTime varchar(30)
);


create table Battle (
battle_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
battle_sectorId int NOT NULL,
battle_lastUpdate varchar(30) DEFAULT '',
battle_isOver bool DEFAULT false,
battle_attackerId int DEFAULT NULL,
battle_defenderId int DEFAULT NULL
);

create table BattleRound (
	battleRound_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	battleRound_battleId int NOT NULL,
	battleRound_roundId int NOT NULL,	/* Saves round number*/
	battleRound_attackLog varchar(500) DEFAULT '',
	battleRound_defendLog varchar(500) DEFAULT ''
);

create table BattleCosts (
	battleCosts_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	battleCosts_battleId int NOT NULL,
	battleCosts_ownerId int NOT NULL,
	battleCosts_costs varchar(30) DEFAULT '0,0,0,0,0'
);


create table Message (
	message_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	message_from int DEFAULT 0,
	message_to int DEFAULT 0,
	message_subject text,
	message_content text,
	message_date varchar(50),
	message_read bool,
	message_deleted bool DEFAULT false
);
/*
CREATE PROCEDURE Select_TrainingQueue_unitList(coordinateX INT, coordinateY INT, playerId INT)
BEGIN
  	SELECT TrainingQueue_unitList
	FROM TrainingQueue
	WHERE TrainingQueue_sectorCoordinateX = coordinateX
		AND TrainingQueue_sectorCoordinateY = coordinateY
		AND TrainingQueue_ownerId = playerId;
END

CREATE PROCEDURE Select_TrainingQueue_timeList(coordinateX INT, coordinateY INT, playerId INT)
BEGIN
  	SELECT TrainingQueue_timeList
	FROM TrainingQueue
	WHERE TrainingQueue_sectorCoordinateX = coordinateX
		AND TrainingQueue_sectorCoordinateY = coordinateY
		AND TrainingQueue_ownerId = playerId;
END
*/
/*********************INSERTS*****************************/

insert into Sector (sector_coordinateX, sector_coordinateY, sector_isLand, sector_edge)
values (0,5,1,0),(0,6,1,0),
(0,7,1,0),(0,8,1,0),(0,9,1,0),
(1,5,1,0),(1,6,1,0),(1,7,1,0),(1,8,1,0),(1,9,1,0),
(2,5,1,0),(2,6,1,0),(2,7,1,0),(2,8,1,0),(2,9,1,0),
(3,5,1,0),(3,6,1,0),(3,7,1,0),(3,8,1,0),(3,9,1,0);


insert into Technology
values (1, 25, 1, 2, 1, 
	'0,0,0,20,0', NULL, 
	0, 30, NULL, 'silex.jpg', 42, 0),

(2, 26, 1, 3, 1, 
	'0,4,0,0,0', '1.5,1.5,1.5,1.5,1.5', 
	1, 5, 1.7, 'leadership.jpg', 43, 0),
(3, 24, 1, 2, 1, 
	'0,50,30,20,0', NULL, 
	0, 70, NULL, 'neolitic.jpg', 44, 1),
(4, 27, 2, 3, 2, 
	'0,30,20,0,10', NULL, 
	0, 60, NULL, 'agriculture.jpg', 45, 0);

insert into Age values (1,22,'LinkIMG','0,100,0,0,0'), (2,23,'LinkIMG',NULL);

insert into BattleModLink values (0, 0, 4);

INSERT INTO Term (`term_id`, `term_spanish`, `alias`) VALUES
(1, 'Garrotero', NULL),
(2, 'Infanter?aCC', NULL),
(3, 'Guerrero con Hacha (s?lex)', NULL),
(4, 'Jabalinero', NULL),
(5, 'InfanteriaD', NULL),
(6, 'Arquero', NULL),
(7, 'Guerrero con Antorcha', NULL),
(8, 'Artilleria', NULL),
(9, 'Edificio', NULL),
(10, 'Población', NULL),
(11, 'Comida', NULL),
(12, 'Madera', NULL),
(13, 'Piedra', NULL),
(14, 'Oro', NULL),
(15, 'Centro de Mando', NULL),
(16, 'Viviendas', NULL),
(17, 'Cuarteles (Primitivo)', NULL),
(18, 'Campamento Maderero', NULL),
(19, 'Cantera', NULL),
(20, 'Campamento de caza', NULL),
(21, 'Granja', NULL),
(22, 'Fuego', NULL),
(23, 'Prehistoria', NULL),
(24, 'Neol?tico', NULL),
(25, 'S?lex', NULL),
(26, 'Liderazgo', NULL),
(27, 'Agricultura', NULL),
(28, 'Un tipo con un garrote de madera. Basta con ir al bosque para ser uno de éstos.', NULL),
(32, 'Arcos y flechas, this is serious shit.', NULL),
(31, 'Fuego!', NULL),
(30, 'Un hacha de piedra! Una tecnología básica para tiempos primitivos, pero más efectiva que la puta madera.', NULL),
(29, 'Este tipo ya ha aprendido a sacarle punta a la madera y a lanzarla.', NULL),
(39, 'Granjas! Comienza la era dorada', NULL),
(38, 'Campamentos de caza.', NULL),
(37, 'Una cantera', NULL),
(36, 'Puestos para recoger madera más eficientemente', NULL),
(35, 'Aún no está clara la función de los cuarteles', NULL),
(34, 'Las viviendas atraen a nuevos colonos al haber más espacio disponible', NULL),
(33, 'El centro de mando permite coordinar nuestros esfuerzos en este sector.', NULL),
(40, 'EPIC DRAGON', NULL),
(41, 'Capitolio', NULL),
(42, 'El tallado del sílex permite una nueva variedad de armas y herramientas.', NULL),
(43, 'Un líder fuerte, así como unas técnicas de control más avanzadas, permiten que más hombres os sigan en el campo de batalla.', NULL),
(44, 'Una nueva época florece a medida que los conocimientos de vuestra civilización avanzan.', NULL),
(45, 'Granjas!', NULL),
(46, 'Tercera época. Si ves esto algo va mal.', NULL),
(47, 'El corazón del imperio. Vuestras tropas necesitarán más recursos cuanto más lejos estén .', NULL);

insert into UnitClass values (1, 2, 'G,C')
, (2, 5, 'G,C')
, (3, 8, 'G,C')
, (4, 9, 'C,W');


insert into Unit values (1, 1, 2, 100, 5, 1, 2, 'garrotero.png', 1, '1, 100, 10, 0, 0', '0, 5, 0, 0, 0', '0, 10, 0, 0, 0', 30, 1, 2, 1)
, (2, 3, 4, 120, 3, 1, 3, 'axe1.jpg', 1, '180, 0, 10, 0, 0', '5, 0, 0, 0, 0', '10, 0, 0, 0, 0', 50, 3, NULL, 0)
, (3, 4, 3, 80, 6, 1, 2, 'javelin.gif', 2, '1, 150, 10, 0, 0', '0, 5, 0, 0, 0', '0, 10, 0, 0, 0', 40, 4, 4, 1)
, (4, 6, 4, 100, 6, 2, 3, 'archer.jpg', 2, '1, 150, 10, 0, 0', '0, 5, 5, 0, 0', '0, 0, 0, 0, 0', 40, 6, NULL, 0)
, (5, 7, 1, 70, 4, 1, 3, 'torchman.jpg', 3, '1, 150, 20, 0, 0', '0, 5, 0, 0, 0', '0, 5, 0, 0, 0', 35, 7, NULL, 0);

insert into Resource values (1,10,1,3,1, 'population.png')
, (2,11,1,3,1, 'food.png')
, (3,12,1,3,1, 'wood.png')
, (4,13,1,3,1, 'stone.png')
, (5,14,2,3,0, 'gold.png');



insert into BuildingClass
values (0, 41, 'capitol.jpg', 5000, 1, 3, 0, 
	'500,5000,3000,2000,1500', 
	'0,0,0,0,0', 
	'0,0,0,0,0', 
	'0,0,0,0,0',
	43200, NULL, 47, 
	NULL, 0),

(1,15,'headquarters.jpg', 2000, 1, 3, 0, 
	'5, 0, 300, 150, 0',
	NULL,
	'0, 0, 2, 2, 0', 
	'0, 0, 30, 20, 0',
	900, NULL, 33, 
	NULL, 0)
,
 (2,16, 'houses.jpg', 500, 1, 3, 0,
	'0, 0, 100, 75, 0',
	NULL,
	NULL,
	NULL,
	240, 0, 34, 
	NULL, 0)
,
 (3,17, 'barracks.jpg', 1500, 1, 3, 1,
	'2,0,100,50,0',
	'0,0,1.5,1.5,0',
	'0,0,2,2,0',
	'0,10,0,0,0',
	480, 1, 35, 
	NULL, 0)
,
 (4,18, 'wood_camp.jpg', 1000, 1, 3, 1,
	'2,100,0,40,0',
	'0,1.7,0,1.7,0',
	NULL,
	'0,0,30,0,0',
	240, 1, 36, 
	NULL, 0)
,
 (5,19, 'quarry.jpg', 1000, 1, 3, 1,
	'2,100,60,0,0',
	'0,1.7,1.7,0,0',
	NULL,
	'0,0,0,20,0',
	240, 1, 37, 
	NULL, 0)
,
 (6,20, 'hunt_camp.jpg', 1000, 1, 3, 1,
	'2,0,60,40,0',
	'0,0,1.7,1.7,0',
	NULL,
	'0,50,0,0,0',
	240,1, 38, 
	7, 0)
,
 (7,21, 'farm.jpg', 1300, 2, 3, 1,
	'3,0,70,50,0',
	'0,0,1.7,1.7,0',
	NULL,
	NULL,
	2,1, 39, 
	NULL, 0);

insert into BattleMod values (1, 22, '+', 29, 4, NULL);

insert into ProductionMod values (1, 'Building', 4, 3, '*', 1.7, NULL)
, (2, 'Building', 5, 4, '*', 1.7, NULL)
, (3, 'Building', 6, 2, '*', 1.7, NULL)
, (4, 'Building', 7, 2, '*', 2, NULL);

insert into Requirement values (1, 2, 'Unit', 1, 'Technology', 1)
, (2, 2, 'Age', 1, 'Technology', 1)
, (3, 7, 'Building', 4, 'Technology', 1);



create view rank_1 AS
SELECT sector_ownerId AS playerId, 'Sectors' AS concept, COUNT(*) AS points FROM Sector WHERE sector_ownerId IS NOT NULL GROUP BY sector_ownerId
    UNION
SELECT division_ownerId AS playerId, 'Divisions' AS concept, SUM(division_quantity) AS points FROM Division GROUP BY division_ownerId

    UNION
SELECT  b.sector_ownerId AS playerId, 'Buildings' AS concept, COUNT(*) AS points FROM Building a INNER JOIN Sector b ON a.building_sectorId=b.sector_id GROUP BY sector_ownerId;


create view ranking AS
SELECT b.player_id, b.player_nick, b.player_lastUpdate, a.concept, a.points FROM
rank_1 a
RIGHT JOIN Player b ON b.player_id=a.playerId
ORDER BY b.player_nick DESC;


create table Admin (
admin_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,

admin_nick varchar(30) not NULL,
admin_password varchar(50) not NULL,
admin_email varchar(50) DEFAULT '',
admin_rank varchar(10) DEFAULT ''
);

INSERT INTO Player VALUES (0, 'System', '', 'bnm', 1, 'ejp_flag.png', 'ejp_avatar.png', 'nmjk,', '100,1000,1000,1000,0', '1327689575', 0, '0,0', 5);
insert into Admin(admin_nick, admin_password, admin_email, admin_rank) VALUES ('Matrix', '0746c9af2a3fb0fc001cdc485412a888', 'mrkvr84@gmail.com', 'A');
