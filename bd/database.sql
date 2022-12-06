use parafisioantigo;

CREATE TABLE node (
  idnode INTEGER UNSIGNED primary key AUTO_INCREMENT,
  idparent INTEGER UNSIGNED,
  item_order INT  UNSIGNED DEFAULT '0',
  idprojeto INT  UNSIGNED not null,
  name varchar(100) not null
 );

CREATE TABLE attr(
	idnode int UNSIGNED,
	attname varchar(50),
	attvalue varchar(50),
	PRIMARY KEY(idnode, attname)
);

alter table node add constraint FK_NODE_IDPROJETO_PK_PROJETOS_IDPROJETO foreign key (idprojeto) references Projetos(codigoprojeto) on delete cascade;
alter table node add constraint FK_IDPARENT_NODE_PK_IDNODE_NODE foreign key (idparent) references node(idnode);
alter table attr add constraint FK_IDNODE_ATTR_PK_IDNODE_NODE foreign key (idnode) references node(idnode) on delete cascade;

create table attrnode(
	nodename varchar(40),
	attrname varchar(40),
	primary key(nodename, attrname)
);


alter table node add value varchar(20);
alter table node add tipo char(1);
alter table node add iduser int;




-- ********************************************
CREATE TABLE editing (
  idediting INTEGER UNSIGNED primary key AUTO_INCREMENT,
  coduser INTEGER UNSIGNED not null,
  dateediting date not null
 );
CREATE TABLE node2 (
  idnode INTEGER UNSIGNED primary key AUTO_INCREMENT,
  idparent INTEGER UNSIGNED,
  item_order INT  UNSIGNED DEFAULT '0',
  idprojeto INT  UNSIGNED not null,
  name varchar(100) not null
);

CREATE TABLE attr(
	idnode int UNSIGNED,
	attname varchar(50),
	attvalue varchar(50),
	PRIMARY KEY(idnode, attname)
);