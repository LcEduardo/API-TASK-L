CREATE TABLE workers (
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    email   VARCHAR(150) NOT NULL UNIQUE,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bosses (
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    email   VARCHAR(150) NOT NULL UNIQUE,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    id          SERIAL PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    description TEXT,
    status      VARCHAR(20) NOT NULL DEFAULT 'pending',
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    worker_id   INT REFERENCES workers(id),
    boss_id     INT REFERENCES bosses(id)
);

INSERT INTO workers (name, email) VALUES
    ('Ana Silva',     'ana.silva@example.com'),
    ('Bruno Costa',   'bruno.costa@example.com'),
    ('Carla Souza',   'carla.souza@example.com'),
    ('Diego Lima',    'diego.lima@example.com'),
    ('Elisa Rocha',   'elisa.rocha@example.com');

INSERT INTO bosses (name, email) VALUES
    ('Fernando Alves',  'fernando.alves@example.com'),
    ('Gabriela Nunes',  'gabriela.nunes@example.com'),
    ('Henrique Dias',   'henrique.dias@example.com'),
    ('Isabela Martins', 'isabela.martins@example.com'),
    ('João Pereira',    'joao.pereira@example.com');

INSERT INTO tasks (title, description, status, worker_id, boss_id) VALUES
    ('Create login page',    'Build the login form with validation.',  'pending',     1, 1),
    ('Fix database bug',     'Connection fails on the staging server.', 'in_progress', 2, 1),
    ('Write API docs',       'Document all the API endpoints.',         'pending',     3, 2),
    ('Design dashboard',     'Create the layout for the main screen.',  'done',        4, 3),
    ('Set up backups',       'Schedule daily database backups.',        'in_progress', 5, 4);