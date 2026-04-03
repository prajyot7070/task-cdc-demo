CREATE TABLE tasks (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'open'
);

CREATE TABLE change_log (
    id SERIAL PRIMARY KEY,
    operation VARCHAR(10),
    record_id INTEGER,
    old_data JSONB,
    new_data JSONB,
    changed_at TIMESTAMP DEFAULT NOW()
);

CREATE OR REPLACE FUNCTION log_cdc() RETURNS TRIGGER AS $$
BEGIN
    IF (TG_OP = 'DELETE') THEN
        INSERT INTO change_log (operation, record_id, old_data, new_data)
        VALUES ('DELETE', OLD.id, row_to_json(OLD)::JSONB, NULL);
        RETURN OLD;
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO change_log (operation, record_id, old_data, new_data)
        VALUES ('UPDATE', NEW.id, row_to_json(OLD)::JSONB, row_to_json(NEW)::JSONB);
        RETURN NEW;
    ELSIF (TG_OP = 'INSERT') THEN
        INSERT INTO change_log (operation, record_id, old_data, new_data)
        VALUES ('INSERT', NEW.id, NULL, row_to_json(NEW)::JSONB);
        RETURN NEW;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tasks_cdc_trigger AFTER INSERT OR UPDATE OR DELETE ON tasks
FOR EACH ROW EXECUTE FUNCTION log_cdc();
