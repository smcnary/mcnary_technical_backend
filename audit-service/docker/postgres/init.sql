-- Create database if it doesn't exist
SELECT 'CREATE DATABASE audit_service'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'audit_service')\gexec

-- Connect to the audit_service database
\c audit_service;

-- Create extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";
CREATE EXTENSION IF NOT EXISTS "btree_gin";

-- Create audit tables
CREATE TABLE IF NOT EXISTS audit_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID,
    action VARCHAR(255) NOT NULL,
    resource_type VARCHAR(255) NOT NULL,
    resource_id VARCHAR(255),
    details JSONB,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_sessions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    ip_address INET,
    user_agent TEXT,
    started_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP WITH TIME ZONE,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS audit_events (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    event_type VARCHAR(255) NOT NULL,
    event_data JSONB,
    severity VARCHAR(50) DEFAULT 'info',
    source VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_action ON audit_logs(action);
CREATE INDEX IF NOT EXISTS idx_audit_logs_resource_type ON audit_logs(resource_type);
CREATE INDEX IF NOT EXISTS idx_audit_logs_created_at ON audit_logs(created_at);
CREATE INDEX IF NOT EXISTS idx_audit_logs_details_gin ON audit_logs USING GIN(details);

CREATE INDEX IF NOT EXISTS idx_audit_sessions_user_id ON audit_sessions(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_sessions_session_id ON audit_sessions(session_id);
CREATE INDEX IF NOT EXISTS idx_audit_sessions_is_active ON audit_sessions(is_active);

CREATE INDEX IF NOT EXISTS idx_audit_events_event_type ON audit_events(event_type);
CREATE INDEX IF NOT EXISTS idx_audit_events_severity ON audit_events(severity);
CREATE INDEX IF NOT EXISTS idx_audit_events_created_at ON audit_events(created_at);
CREATE INDEX IF NOT EXISTS idx_audit_events_event_data_gin ON audit_events USING GIN(event_data);

-- Create function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create trigger for updated_at
CREATE TRIGGER update_audit_logs_updated_at 
    BEFORE UPDATE ON audit_logs 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Insert sample data
INSERT INTO audit_events (event_type, event_data, severity, source) VALUES
('system_startup', '{"version": "1.0.0", "environment": "development"}', 'info', 'audit_service'),
('database_connected', '{"database": "postgres", "host": "postgres"}', 'info', 'audit_service');

-- Grant permissions
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO audit_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO audit_user;
GRANT EXECUTE ON ALL FUNCTIONS IN SCHEMA public TO audit_user;
