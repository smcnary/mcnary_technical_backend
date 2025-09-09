# Terraform configuration for RDS PostgreSQL deployment
# This file provides Infrastructure as Code for deploying the database to AWS RDS

terraform {
  required_version = ">= 1.0"
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

# Configure the AWS Provider
provider "aws" {
  region = var.aws_region
}

# Variables
variable "aws_region" {
  description = "AWS region for RDS deployment"
  type        = string
  default     = "us-east-1"
}

variable "environment" {
  description = "Environment name (dev, staging, prod)"
  type        = string
  default     = "prod"
}

variable "db_instance_identifier" {
  description = "RDS instance identifier"
  type        = string
  default     = "counselrank-prod-db"
}

variable "db_name" {
  description = "Database name"
  type        = string
  default     = "counselrank_prod"
}

variable "db_username" {
  description = "Master username"
  type        = string
  default     = "counselrank_admin"
}

variable "db_password" {
  description = "Master password"
  type        = string
  sensitive   = true
}

variable "db_instance_class" {
  description = "DB instance class"
  type        = string
  default     = "db.t3.micro"
}

variable "allocated_storage" {
  description = "Allocated storage in GB"
  type        = number
  default     = 20
}

variable "vpc_id" {
  description = "VPC ID for RDS deployment"
  type        = string
  default     = ""
}

variable "subnet_ids" {
  description = "Subnet IDs for RDS subnet group"
  type        = list(string)
  default     = []
}

# Data sources
data "aws_vpc" "default" {
  count   = var.vpc_id == "" ? 1 : 0
  default = true
}

data "aws_subnets" "default" {
  count = length(var.subnet_ids) == 0 ? 1 : 0
  filter {
    name   = "vpc-id"
    values = [var.vpc_id != "" ? var.vpc_id : data.aws_vpc.default[0].id]
  }
}

# Security Group for RDS
resource "aws_security_group" "rds_sg" {
  name_prefix = "counselrank-rds-sg-"
  description = "Security group for CounselRank RDS database"
  vpc_id      = var.vpc_id != "" ? var.vpc_id : data.aws_vpc.default[0].id

  ingress {
    from_port   = 5432
    to_port     = 5432
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"] # Restrict this in production
    description = "PostgreSQL access"
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "All outbound traffic"
  }

  tags = {
    Name        = "counselrank-rds-sg"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

# DB Subnet Group
resource "aws_db_subnet_group" "rds_subnet_group" {
  name       = "counselrank-rds-subnet-group"
  subnet_ids = length(var.subnet_ids) > 0 ? var.subnet_ids : data.aws_subnets.default[0].ids

  tags = {
    Name        = "counselrank-rds-subnet-group"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

# DB Parameter Group
resource "aws_db_parameter_group" "rds_parameter_group" {
  name   = "counselrank-postgres-16"
  family = "postgres16"

  parameter {
    name  = "shared_preload_libraries"
    value = "pg_stat_statements"
  }

  parameter {
    name  = "log_statement"
    value = "all"
  }

  parameter {
    name  = "log_min_duration_statement"
    value = "1000"
  }

  parameter {
    name  = "log_checkpoints"
    value = "1"
  }

  parameter {
    name  = "log_connections"
    value = "1"
  }

  parameter {
    name  = "log_disconnections"
    value = "1"
  }

  parameter {
    name  = "log_lock_waits"
    value = "1"
  }

  parameter {
    name  = "log_temp_files"
    value = "0"
  }

  parameter {
    name  = "log_autovacuum_min_duration"
    value = "0"
  }

  parameter {
    name  = "timezone"
    value = "UTC"
  }

  parameter {
    name  = "shared_buffers"
    value = "256MB"
  }

  parameter {
    name  = "effective_cache_size"
    value = "1GB"
  }

  parameter {
    name  = "work_mem"
    value = "4MB"
  }

  parameter {
    name  = "maintenance_work_mem"
    value = "64MB"
  }

  parameter {
    name  = "checkpoint_completion_target"
    value = "0.9"
  }

  parameter {
    name  = "wal_buffers"
    value = "16MB"
  }

  parameter {
    name  = "default_statistics_target"
    value = "100"
  }

  parameter {
    name  = "random_page_cost"
    value = "1.1"
  }

  parameter {
    name  = "effective_io_concurrency"
    value = "200"
  }

  parameter {
    name  = "max_worker_processes"
    value = "8"
  }

  parameter {
    name  = "max_parallel_workers_per_gather"
    value = "2"
  }

  parameter {
    name  = "max_parallel_workers"
    value = "8"
  }

  parameter {
    name  = "max_parallel_maintenance_workers"
    value = "2"
  }

  tags = {
    Name        = "counselrank-postgres-16"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

# RDS Instance
resource "aws_db_instance" "rds_instance" {
  identifier = var.db_instance_identifier
  engine      = "postgres"
  engine_version = "16.1"
  instance_class = var.db_instance_class
  
  # Database configuration
  db_name  = var.db_name
  username = var.db_username
  password = var.db_password
  
  # Storage configuration
  allocated_storage     = var.allocated_storage
  max_allocated_storage = var.allocated_storage * 2
  storage_type          = "gp2"
  storage_encrypted     = true
  
  # Network configuration
  db_subnet_group_name   = aws_db_subnet_group.rds_subnet_group.name
  vpc_security_group_ids = [aws_security_group.rds_sg.id]
  publicly_accessible    = false
  
  # Backup configuration
  backup_retention_period = 7
  backup_window          = "03:00-04:00"
  maintenance_window     = "sun:04:00-sun:05:00"
  copy_tags_to_snapshot  = true
  delete_automated_backups = false
  
  # High availability
  multi_az = var.environment == "prod" ? true : false
  
  # Monitoring
  monitoring_interval = 60
  monitoring_role_arn = aws_iam_role.rds_monitoring_role.arn
  
  # Performance Insights
  performance_insights_enabled = true
  performance_insights_retention_period = 7
  
  # Parameter group
  parameter_group_name = aws_db_parameter_group.rds_parameter_group.name
  
  # Final snapshot
  final_snapshot_identifier = "${var.db_instance_identifier}-final-snapshot"
  skip_final_snapshot = false
  
  # Deletion protection
  deletion_protection = var.environment == "prod" ? true : false
  
  tags = {
    Name        = var.db_instance_identifier
    Environment = var.environment
    Project     = "CounselRank"
    Application = "Legal Marketing Platform"
    Owner       = "Sean McNary"
    CostCenter  = "Engineering"
    Backup      = "Required"
    Compliance  = "SOC2"
  }
}

# IAM Role for RDS Enhanced Monitoring
resource "aws_iam_role" "rds_monitoring_role" {
  name = "rds-monitoring-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "monitoring.rds.amazonaws.com"
        }
      }
    ]
  })

  tags = {
    Name        = "rds-monitoring-role"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

resource "aws_iam_role_policy_attachment" "rds_monitoring_role_policy" {
  role       = aws_iam_role.rds_monitoring_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonRDSEnhancedMonitoringRole"
}

# CloudWatch Log Group for RDS logs
resource "aws_cloudwatch_log_group" "rds_logs" {
  name              = "/aws/rds/instance/${var.db_instance_identifier}/postgresql"
  retention_in_days = 30

  tags = {
    Name        = "counselrank-rds-logs"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

# CloudWatch Alarms
resource "aws_cloudwatch_metric_alarm" "rds_cpu" {
  alarm_name          = "counselrank-rds-cpu-utilization"
  comparison_operator = "GreaterThanThreshold"
  evaluation_periods = "2"
  metric_name         = "CPUUtilization"
  namespace           = "AWS/RDS"
  period              = "300"
  statistic           = "Average"
  threshold           = "80"
  alarm_description   = "This metric monitors RDS CPU utilization"
  alarm_actions       = []

  dimensions = {
    DBInstanceIdentifier = aws_db_instance.rds_instance.id
  }

  tags = {
    Name        = "counselrank-rds-cpu-alarm"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

resource "aws_cloudwatch_metric_alarm" "rds_freeable_memory" {
  alarm_name          = "counselrank-rds-freeable-memory"
  comparison_operator = "LessThanThreshold"
  evaluation_periods = "2"
  metric_name         = "FreeableMemory"
  namespace           = "AWS/RDS"
  period              = "300"
  statistic           = "Average"
  threshold           = "100000000" # 100MB
  alarm_description   = "This metric monitors RDS freeable memory"
  alarm_actions       = []

  dimensions = {
    DBInstanceIdentifier = aws_db_instance.rds_instance.id
  }

  tags = {
    Name        = "counselrank-rds-memory-alarm"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

resource "aws_cloudwatch_metric_alarm" "rds_free_storage_space" {
  alarm_name          = "counselrank-rds-free-storage-space"
  comparison_operator = "LessThanThreshold"
  evaluation_periods = "2"
  metric_name         = "FreeStorageSpace"
  namespace           = "AWS/RDS"
  period              = "300"
  statistic           = "Average"
  threshold           = "2000000000" # 2GB
  alarm_description   = "This metric monitors RDS free storage space"
  alarm_actions       = []

  dimensions = {
    DBInstanceIdentifier = aws_db_instance.rds_instance.id
  }

  tags = {
    Name        = "counselrank-rds-storage-alarm"
    Environment = var.environment
    Project     = "CounselRank"
  }
}

# Outputs
output "rds_endpoint" {
  description = "RDS instance endpoint"
  value       = aws_db_instance.rds_instance.endpoint
}

output "rds_port" {
  description = "RDS instance port"
  value       = aws_db_instance.rds_instance.port
}

output "rds_arn" {
  description = "RDS instance ARN"
  value       = aws_db_instance.rds_instance.arn
}

output "rds_security_group_id" {
  description = "RDS security group ID"
  value       = aws_security_group.rds_sg.id
}

output "database_url" {
  description = "Database connection URL"
  value       = "postgresql://${var.db_username}:${var.db_password}@${aws_db_instance.rds_instance.endpoint}:${aws_db_instance.rds_instance.port}/${var.db_name}?serverVersion=16&charset=utf8"
  sensitive   = true
}
