create table modulefunctions
(
    id        int auto_increment
        primary key,
    name      varchar(45) null,
    module_id int         null,
    constraint name_UNIQUE
        unique (name)
);

INSERT INTO CreaditOlineRights.modulefunctions (id, name, module_id) VALUES (4, 'function1', 1);
INSERT INTO CreaditOlineRights.modulefunctions (id, name, module_id) VALUES (5, 'function2', 1);
INSERT INTO CreaditOlineRights.modulefunctions (id, name, module_id) VALUES (11, 'function3', 7);
INSERT INTO CreaditOlineRights.modulefunctions (id, name, module_id) VALUES (12, 'function4', 7);
