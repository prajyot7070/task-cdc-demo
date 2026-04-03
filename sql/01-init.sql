CREATE TABLE tasks (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'open'
);

CREATE TABLE change_log (
    id SERIAL PRIMARY KEY,
    operation VARCHAR(10),
    record_id INTEGER,
    new_data JSONB,
    changed_at TIMESTAMP DEFAULT NOW()
);

CREATE OR REPLACE FUNCTION log_cdc() RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO change_log (operation, record_id, new_data)
    VALUES (TG_OP, COALESCE(NEW.id, OLD.id), row_to_json(NEW)::JSONB);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tasks_cdc_trigger AFTER INSERT OR UPDATE OR DELETE ON tasks
FOR EACH ROW EXECUTE FUNCTION log_cdc();
